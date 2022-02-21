<?php

namespace App\Http\Controllers\site\student\authentication;

use App\Http\Controllers\Controller;
use App\Traits\response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class resetPasswored extends Controller
{
    use response;
    ////////sent code /////////////

    public function sendCode(Request $request){  // this is most important function to send mail and inside of that there are another function        
        if (!$this->validateUsername($request->username)) {  // this is validate to fail send mail or true
            return $this::faild(trans('auth.username not found'), 404, 'E04');
        }
        
        // code is important in send mail 
        $code = $this->createCode($request->username);
        // Mail::to($request->email)->send(new MailVerification($code, $request->email));

        return $this::success(trans('auth.send reset password code success, please check your phone.'), 200);
    }

    public function createCode($username){  // this is a function to get your request email that there are or not to send mail
        $oldCode = DB::table('student_password_resets')->where('username', $username)->first();

        //if user already has code
        if ($oldCode)
            return $oldCode->code;

        $code = rand(1000,9999);
        $this->saveCode($code, $username);
        return $code;
    }

    public function saveCode($code, $username){  // this function save new password
        DB::table('student_password_resets')->insert([
            'username'      => $username,
            'code'          => $code,
            'created_at'    => Carbon::now()
        ]);
    }

    public function validateUsername($username){  //this is a function to get your email from database
        return !!DB::table('students')->where('username', $username)->first();
    }
    ///////////////check if code is valid ////////////

    public function checkCode(Request $request){
        if($this->updatePasswordRow($request)->count() > 0){
            return $this::success(trans('auth.success'), 200);
        } else {
            return $this::faild(trans('auth.Either your username or code is wrong.'), 404, 'E04');
        }
    }

    //////////////////////// verification ////////////

    public function changePasswordProcess(Request $request){
        $validator = Validator::make($request->all(), [
            'username'             => 'required',
            'code'                 => 'required',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403, 'E03');
        }

        return $this->verificationRow($request)->count() > 0 ? $this->changePassword($request) : $this::faild(trans('auth.Either your username or code is wrong.'), 404, 'E04');
    }
  
    // Verify if code is valid
    public function passwordResetProcess(Request $request){
        $validator = Validator::make($request->all(), [
            'username'          => 'required',
            'code'              => 'required',
            'password'          => 'required|string|min:6',
            'confirmPassword'   => 'required|string|same:password',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403, 'E03');
        }

        return $this->updatePasswordRow($request)->count() > 0 ? $this->resetPassword($request) : $this::faild(trans('auth.Either your username or code is wrong.'), 404, 'E04');
    }
  
    // Verify if code is valid
    private function updatePasswordRow($request){
        return DB::table('student_password_resets')->where([
            'username'  => $request->username,
            'code'      => $request->code
        ]);
    }

    // Reset password
    private function resetPassword($request) {
        // update password
        DB::table('students')
        ->where('username', $request->username)
        ->update(['password' => bcrypt($request->password)]);

        // remove verification data from db
        $this->updatePasswordRow($request)->delete();

        // reset password response
        return response::success(trans('auth.Password has been updated.'), 200);
    } 
}

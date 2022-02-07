<?php

namespace App\Http\Controllers\site\student\authentication;

use App\Http\Controllers\Controller;
use App\Traits\response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class verification extends Controller
{
    use response;
    ////////sent email /////////////

    public function sendCode(Request $request){  // this is most important function to send mail and inside of that there are another function        
        if (!$this->validateUsername($request->username)) {  // this is validate to fail send mail or true
            return $this::faild('username not found', 404, 'E04');
        }
        
        // code is important in send mail 
        $code = $this->createCode($request->username);
        // Mail::to($request->email)->send(new MailVerification($code, $request->email));

        return $this::success('send verify code success, please check your phone.', 200);
    }

    public function createCode($username){  // this is a function to get your request email that there are or not to send mail
        $table = 'student_verified';

        $oldCode = DB::table($table)->where('username', $username)->first();

        //if user already has code
        if ($oldCode)
            return $oldCode->code;

        $code = rand(100000,999999);
        $this->saveCode($code, $username);
        return $code;
    }

    public function saveCode($code, $username){  // this function save new password
        DB::table('student_verified')->insert([
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
            return $this::success('success', 200);
        } else {
            return $this::faild('Either your username or code is wrong.', 404, 'E04');
        }
    }

    //////////////////////// verification ////////////

    public function verificationProcess(Request $request){
        $validator = Validator::make($request->all(), [
            'username'             => 'required',
            'code'                 => 'required',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403, 'E03');
        }

        return $this->verificationRow($request)->count() > 0 ? $this->verification($request) : $this::faild('Either your username or code is wrong.', 404, 'E04');
    }
  
    // Verify if code is valid
    private function verificationRow($request){
        return DB::table('student_verified')->where([
            'username'  => $request->username,
            'code'      => $request->code
        ]);
    }

    private function verification($request) {
        // update students
        DB::table('students')
        ->where('username', $request->username)
        ->update(['verified' => 1]);

        // remove verification data from db
        $this->verificationRow($request)->delete();

        // reset password response
        return response::success('acount verification success', 200);
    } 
}

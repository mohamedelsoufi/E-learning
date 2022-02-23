<?php

namespace App\Http\Controllers\site\teacher\authentication;

use App\Http\Controllers\Controller;
use App\Http\Resources\teacherResource;
use App\Traits\response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class verification extends Controller
{
    use response;
    ////////sent email /////////////

    public function sendCode(Request $request){  // this is most important function to send mail and inside of that there are another function        
        // validate
        $validator = Validator::make($request->all(), [
            'phone'          => 'required',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403);
        }
        
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }
        if (!$this->validatePhone($teacher->phone)) {  // this is validate to fail send mail or true
            return $this::faild(trans('auth.phone not found'), 404, 'E04');
        }
        
        // code is important in send mail 
        $code = $this->createCode($request->phone);
        // Mail::to($request->email)->send(new MailVerification($code, $request->email));

        return $this::success(trans('auth.send verify code success, please check your phone.'), 200);
    }

    public function createCode($phone){  // this is a function to get your request email that there are or not to send mail

        $oldCode = DB::table('teacher_verified')->where('phone', $phone)->first();

        //if user already has code
        if ($oldCode)
            return $oldCode->code;

        // $code = rand(1000,9999);
        $code = "1234";
        $this->saveCode($code, $phone);
        return $code;
    }

    public function saveCode($code, $phone){  // this function save new password
        DB::table('teacher_verified')->insert([
            'phone'         => $phone,
            'code'          => $code,
            'created_at'    => Carbon::now()
        ]);
    }

    public function validatePhone($phone){  //this is a function to get your email from database
        return !!DB::table('teachers')->where('phone', $phone)->first();
    }
    ///////////////check if code is valid ////////////
    public function checkCode(Request $request){
        if($this->updatePasswordRow($request)->count() > 0){
            return $this::success(trans('auth.success'), 200);
        } else {
            return $this::faild(trans('auth.your code is wrong.'), 404, 'E04');
        }
    }

    //////////////////////// verification ////////////
    public function verificationProcess(Request $request){
        $validator = Validator::make($request->all(), [
            'code'              => 'required',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403, 'E03');
        }

        return $this->verificationRow($request)->count() > 0 ? $this->verification($request) : $this::faild(trans('auth.your code is wrong.'), 404, 'E04');
    }
  
    // Verify if code is valid
    private function verificationRow($request){
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }
        return DB::table('teacher_verified')->where([
            'phone'       => $teacher->phone,
            'code'        => $request->code
        ]);
    }

    private function verification($request) {
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }
        // update teachers
        DB::table('teachers')
        ->where('phone', $teacher->phone)
        ->update(['verified' => 1]);

        // remove verification data from db
        $this->verificationRow($request)->delete();

        //get token
        try {
            if (! $token = JWTAuth::fromUser($teacher)) { //login
                return $this->faild(trans('auth.passwored or phone is wrong'), 404, 'E04');
            }
        } catch (JWTException $e) {
            return $this->faild(trans('auth.login faild'), 400, 'E00');
        }

        // check if teacher not active
        if($teacher['year_id'] == null){
            return response()->json([
                'successful'=> false,
                'step'      => 'setup_profile',
                'teacher'   => new teacherResource($teacher),
                'token'     => $token,
            ], 200);
        }
        
        //update token
        $teacher->token_firebase = $request->get('token_firebase');
        $teacher->save();

        return response()->json([
            'successful'=> true,
            'step'      => true,
            'message'   => 'success',
            'teacher'   => new teacherResource($teacher),
            'token'     => $token,
        ], 200);
    } 
}

<?php

namespace App\Http\Controllers\site\teacher\authentication;

use App\Http\Controllers\Controller;
use App\Http\Controllers\site\teacher\authentication\verification;
use App\Http\Resources\teacherResource;
use App\Models\Teacher;
use App\Traits\response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class resetPasswored extends Controller
{
    use response;
    public $verification;
    public function __construct(verification $verification)
    {
        $this->verification         = $verification;
    }
    ////////sent code /////////////

    public function sendCode(Request $request){  // this is most important function to send mail and inside of that there are another function        
        // validate
        $validator = Validator::make($request->all(), [
            'phone'          => 'required',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403);
        }
        
        if (!$this->validatePhone($request->phone)) {  // this is validate to fail send mail or true
            return $this::faild(trans('auth.phone not found'), 404, 'E04');
        }
        
        // code is important in send mail 
        $code = $this->createCode($request->phone);
        // Mail::to($request->email)->send(new MailVerification($code, $request->email));

        return $this::success(trans('auth.send reset password code success, please check your phone.'), 200);
    }

    public function createCode($phone){  // this is a function to get your request email that there are or not to send mail
        $oldCode = DB::table('teacher_password_resets')->where('phone', $phone)->first();

        //if user already has code
        if ($oldCode)
            return $oldCode->code;

        // $code = rand(1000,9999);
        $code = "1234";
        $this->saveCode($code, $phone);
        return $code;
    }

    public function saveCode($code, $phone){  // this function save new password
        DB::table('teacher_password_resets')->insert([
            'phone'      => $phone,
            'code'          => $code,
            'created_at'    => Carbon::now()
        ]);
    }

    public function validatePhone($phone){  //this is a function to get your email from database
        return !!DB::table('teachers')->where('phone', $phone)->first();
    }
    ///////////////check if code is valid ////////////

    public function checkCode(Request $request){
        $validator = Validator::make($request->all(), [
            'phone'             => 'required',
            'code'              => 'required',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403, 'E03');
        }

        $updatePasswordRow = DB::table('teacher_password_resets')->where([
            'phone'  => $request->phone,
            'code'      => $request->code
        ]);

        if($updatePasswordRow->count() > 0){
            $teacher = Teacher::where('phone', $request->phone)->first();

            try {
                if (! $token = JWTAuth::fromUser($teacher)) { //login
                    return $this->faild(trans('auth.passwored or phone is wrong'), 404, 'E04');
                }
            } catch (JWTException $e) {
                return $this->faild(trans('auth.login faild'), 400, 'E00');
            }

            return response()->json([
                "successful"=> true,
                'message'   => trans('auth.success'),
                'token'     => $token,
            ], 200);
        } else {
            return $this::faild(trans('auth.Either your phone or code is wrong.'), 404, 'E04');
        }
    }

    //////////////////////// verification ////////////

    public function changePasswordProcess(Request $request){
        $validator = Validator::make($request->all(), [
            'phone'                => 'required',
            'code'                 => 'required',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403, 'E03');
        }

        return $this->verificationRow($request)->count() > 0 ? $this->changePassword($request) : $this::faild(trans('auth.your code is wrong.'), 404, 'E04');
    }

    // Verify if code is valid
    public function passwordResetProcess(Request $request){
        $validator = Validator::make($request->all(), [
            'code'              => 'required',
            'password'          => 'required|string|min:6',
            'confirmPassword'   => 'required|string|same:password',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403, 'E03');
        }

        return $this->updatePasswordRow($request)->count() > 0 ? $this->resetPassword($request) : $this::faild(trans('auth.your code is wrong.'), 404, 'E04');
    }

    // Verify if code is valid
    private function updatePasswordRow($request){
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }

        return DB::table('teacher_password_resets')->where([
            'phone'     => $teacher->phone,
            'code'      => $request->code
        ]);
    }

    // Reset password
    private function resetPassword($request) {
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }
        // update password
        DB::table('teachers')
        ->where('phone', $teacher->phone)
        ->update(['password' => bcrypt($request->password)]);

        // remove verification data from db
        $this->updatePasswordRow($request)->delete();

        //check if user blocked
        if($teacher['status'] == 0)
            return $this->faild(trans('auth.you are blocked'), 402, 'E02');

        //get token
        try {
            if (! $token = JWTAuth::fromUser($teacher)) { //login
                return $this->faild(trans('auth.passwored or phone is wrong'), 404, 'E04');
            }
        } catch (JWTException $e) {
            return $this->faild(trans('auth.login faild'), 400, 'E00');
        }
        
        // check if teacher not active
        if($teacher['verified'] == 0){
            $request->phone = $teacher->phone;
            $this->verification->sendCode($request);

            return response()->json([
                'successful'=> false,
                'step'      => 'verify',
                'teacher'   => new teacherResource($teacher),
                'token'     => $token,
            ], 200);
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

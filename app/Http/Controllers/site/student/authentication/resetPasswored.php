<?php

namespace App\Http\Controllers\site\student\authentication;

use App\Http\Controllers\Controller;
use App\Http\Resources\studentResource;
use App\Models\Student;
use App\Traits\response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\site\student\authentication\verification;

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
            return $this::faild($validator->errors(), 403);
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
        $oldCode = DB::table('student_password_resets')->where('phone', $phone)->first();

        //if user already has code
        if ($oldCode)
            return $oldCode->code;

        // $code = rand(1000,9999);
        $code = "1234";
        $this->saveCode($code, $phone);
        return $code;
    }

    public function saveCode($code, $phone){  // this function save new password
        DB::table('student_password_resets')->insert([
            'phone'      => $phone,
            'code'          => $code,
            'created_at'    => Carbon::now()
        ]);
    }

    public function validatePhone($phone){  //this is a function to get your email from database
        return !!DB::table('students')->where('phone', $phone)->first();
    }
    ///////////////check if code is valid ////////////

    public function checkCode(Request $request){
        $validator = Validator::make($request->all(), [
            'phone'             => 'required',
            'code'              => 'required',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403, 'E03');
        }

        $updatePasswordRow = DB::table('student_password_resets')->where([
            'phone'  => $request->phone,
            'code'      => $request->code
        ]);

        if($updatePasswordRow->count() > 0){
            $student = Student::where('phone', $request->phone)->first();

            try {
                if (! $token = JWTAuth::fromUser($student)) { //login
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
            return response()->json([
                'successful'=> false,
                'step'      => 'validation',
            ], 200);
        }

        if($this->verificationRow($request)->count() > 0){
            return $this->changePassword($request);
        } else{
            return response()->json([
                'successful'=> false,
                'step'      => 'wrong_code',
                'message'   => trans('auth.your code is wrong.'),
            ], 200);
        } 
    }

    // Verify if code is valid
    public function passwordResetProcess(Request $request){
        $validator = Validator::make($request->all(), [
            'code'              => 'required',
            'password'          => 'required|string|min:6',
            'confirmPassword'   => 'required|string|same:password',
        ]);

        if($validator->fails()){
            return response()->json([
                'successful'=> false,
                'step'      => 'validation',
                'message'   => $validator->errors(),
            ], 200);
        }

        if($this->updatePasswordRow($request)->count() > 0){
            return $this->resetPassword($request);
        } else {
            return response()->json([
                'successful'=> false,
                'step'      => 'wrong_code',
                'message'   => trans('auth.your code is wrong.'),
            ], 200);
        }
            
    }

    // Verify if code is valid
    private function updatePasswordRow($request){
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        return DB::table('student_password_resets')->where([
            'phone'  => $student->phone,
            'code'      => $request->code
        ]);
    }

    // Reset password
    private function resetPassword($request) {
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }
        // update password
        DB::table('students')
        ->where('phone', $student->phone)
        ->update(['password' => bcrypt($request->password)]);

        // remove verification data from db
        $this->updatePasswordRow($request)->delete();

        //check if user blocked
        if($student['status'] == 0)
            return $this->faild(trans('auth.you are blocked'), 402, 'E02');

        //get token
        try {
            if (! $token = JWTAuth::fromUser($student)) { //login
                return $this->faild(trans('auth.passwored or phone is wrong'), 404, 'E04');
            }
        } catch (JWTException $e) {
            return $this->faild(trans('auth.login faild'), 400, 'E00');
        }

        //update token
        $student->token_firebase = $request->get('token_firebase');
        $student->save();
        
        // check if student not active
        if($student['verified'] == 0){
            $request->phone = $student->phone;
            $this->verification->sendCode($request);

            return response()->json([
                'successful'=> false,
                'step'      => 'verify',
                'student'   => new studentResource($student),
                'token'     => $token,
            ], 200);
        }

        // check if student not active
        if($student['year_id'] == null){
            return response()->json([
                'successful'=> false,
                'step'      => 'setup_profile',
                'student'   => new studentResource($student),
                'token'     => $token,
            ], 200);
        }

        return response()->json([
            'successful'=> true,
            'step'      => true,
            'message'   => 'success',
            'student'   => new studentResource($student),
            'token'     => $token,
        ], 200);
    } 
}

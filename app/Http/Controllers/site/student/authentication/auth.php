<?php

namespace App\Http\Controllers\site\student\authentication;

use App\Http\Controllers\Controller;
use App\Http\Resources\studentResource;
use App\Models\Student;
use App\Traits\response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class auth extends Controller
{
    use response;
    public function login(Request $request){
        $guard = 'student';
        
        // //validation
        $validator = Validator::make($request->all(), [
            'username'          => 'required|string',
            'password'          => 'required|string',
            'token_firebase'    => 'required|string',
        ]);

        if($validator->fails()){
            return $this->faild($validator->errors(), 403, 'E03');
        }

        //data
        $credentials = ['username' => $request->username, 'password' => $request->password];
        
        try {
            if (! $token = auth($guard)->attempt($credentials)) { //login
                return $this->faild(trans('auth.passwored or username is wrong'), 404, 'E04');
            }
        } catch (JWTException $e) {
            return $this->faild(trans('auth.login faild'), 400, 'E00');
        }

        //get student data
        if (! $student = auth($guard)->user())
            return $this->faild(trans('auth.student not found'), 404, 'E04');

        //check if user blocked
        if($student['status'] == 0)
            return $this->faild(trans('auth.you are blocked'), 402, 'E02');
        
        // check if student not active
        // if($student['verified'] == 0)
        //     return $this->faild(trans('auth.You must verify your acount'), 405, 'E05');
        
        //update token
        $student->token_firebase = $request->get('token_firebase');
        $student->save();

        return response()->json([
            'successful'=> true,
            'message'   => 'success',
            'student'   => new studentResource($student),
            'token'     => $token,
        ], 200);
    }

    public function logout(){
        FacadesAuth::guard('student')->logout();

        return response::success(trans('auth.logout success'), 200);
    }

    public function register(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'username'         => 'required|string|unique:students|min:3|max:255',
            'dialing_code'     => 'required|string',
            'phone'            => 'required|unique:students|string',
            'password'         => 'required|string|min:6',
            'confirm_password' => 'required|string|same:password',
            'country_id'       => 'required|exists:countries,id',
            'curriculum_id'    => 'required|exists:curriculums,id',
            'gender'           => ['required',Rule::in(0,1)],//0->male  1->female
            'token_firebase'   => 'required|string',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //create student
        $student = Student::create([
            'username'          => $request->get('username'),
            'dialing_code'      => $request->get('dialing_code'),
            'phone'             => $request->get('phone'),
            'password'          => Hash::make($request->get('password')),
            'country_id'        => $request->get('country_id'),
            'curriculum_id'     => $request->get('curriculum_id'),
            'gender'            => $request->get('gender'),
            'token_firebase'    => $request->get('token_firebase'),
        ]);

        //create token
        $token = JWTAuth::fromUser($student);

        //response
        return response()->json([
            "successful"=> true,
            'message'   => trans('auth.register success'),
            'student'   => new studentResource($student),
            'token'     => $token,
        ], 200);
    }
}

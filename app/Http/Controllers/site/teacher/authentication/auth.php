<?php

namespace App\Http\Controllers\site\teacher\authentication;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
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
        $guard = 'teacher';
        
        // //validation
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return $this->faild($validator->errors(), 403, 'E03');
        }

        //data
        $credentials = ['username' => $request->username, 'password' => $request->password];
        
        try {
            if (! $token = auth($guard)->attempt($credentials)) { //login
                return $this->faild('passwored or username is wrong', 404, 'E04');
            }
        } catch (JWTException $e) {
            return $this->faild('login faild', 400, 'E00');
        }

        //get teacher data
        if (! $teacher = auth($guard)->user())
            return $this->faild('user not found', 404, 'E04');

        //check if user blocked
        if($teacher['status'] == 0)
            return $this->faild('you are blocked', 402, 'E02');
        
        // check if student not active
        if($teacher['verified'] == 0)
            return $this->faild('You must verify your acount', 405, 'E05');
        
        return response()->json([
            'successful'=> true,
            'message'   => 'success',
            'teacher'   => $teacher,
            'token'     => $token,
        ], 200);
    }

    public function logout(){
        FacadesAuth::guard('teacher')->logout();

        return response::success('logout success', 200);
    }

    public function register(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'username'         => 'required|string|unique:teachers|min:3|max:255',
            'email'            => 'required|string|email|max:255|unique:teachers',
            'dialing_code'     => 'required|string',
            'phone'            => 'required|string',
            'password'         => 'required|string|min:6',
            'confirm_password' => 'required|string|same:password',
            'birth'            => 'required|date',
            'country_id'       => 'required|exists:countries,id',
            'gender'           => ['required',Rule::in(0,1)],//0->male  1->female
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //create teacher
        $teacher = Teacher::create([
            'username'          => $request->get('username'),
            'email'             => $request->get('email'),
            'dialing_code'      => $request->get('dialing_code'),
            'phone'             => $request->get('phone'),
            'password'          => Hash::make($request->get('password')),
            'country_id'        => $request->get('country_id'),
            'gender'            => $request->get('gender'),
            'birth'             => $request->get('birth'),
        ]);

        //create token
        $token = JWTAuth::fromUser($teacher);

        //response
        return response()->json([
            "successful"=> true,
            'message'   => 'register success',
            'teacher'   => $teacher,
            'token'     => $token,
        ], 200);
    }
}
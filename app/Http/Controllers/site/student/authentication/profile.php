<?php

namespace App\Http\Controllers\site\student\authentication;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class profile extends Controller
{
    public function index($request){
        // validate registeration request
        $validator = Validator::make($request->all(), [
            'student_id'       => 'required|string',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403);
        }

        //get student
        $student = Student::find($request->get('student_id'));

        //if student not found
        if($student == null)
            return $this::faild(trans('auth.student not found'), 404, 'E04');

        return $this->success(trans('auth.success'), 200, 'student', $student);
    }

    public function myProfile(){
        //get student
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('user.student not found'), 404, 'E04');
        }

        return $this->success(trans('auth.success'), 200, 'student', $student);
    }
}

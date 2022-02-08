<?php

namespace App\Http\Controllers\site\teacher\authentication;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class profile extends Controller
{
    public function index(Request $request){
        // validate registeration request
        $validator = Validator::make($request->all(), [
            'teacher_id'       => 'required|string',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403);
        }

        //get teacher
        $teacher = Teacher::find($request->get('teacher_id'));

        //if teacher not found
        if($teacher == null)
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');

        return $this->success(trans('auth.success'), 200, 'teacher', $teacher);
    }

    public function myProfile(){
        //get teacher
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }

        return $this->success(trans('auth.success'), 200, 'teacher', $teacher);
    }
}

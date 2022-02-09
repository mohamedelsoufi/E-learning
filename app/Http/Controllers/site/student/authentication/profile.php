<?php

namespace App\Http\Controllers\site\student\authentication;

use App\Http\Controllers\Controller;
use App\Http\Resources\studentResource;
use App\Models\Image;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class profile extends Controller
{
    public function index(Request $request){
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

        return $this->success(
                trans('auth.success'),
                200,
                'student',
                new studentResource($student)
            );
    }

    public function myProfile(){
        //get student
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('user.student not found'), 404, 'E04');
        }

        return $this->success(
            trans('auth.success'),
            200,
            'student',
            new studentResource($student)
        );
    }

    public function changePassword(Request $request){
        // validate registeration request
        $validator = Validator::make($request->all(), [
            'oldPassword'       => 'required|string',
            'password'          => 'required|string|min:6',
            'confirm_password'  => 'required|string|same:password',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403);
        }

        //get student or vender
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('user.student not found'), 404, 'E04');
        }        
        
        //update student pass
        if(Hash::check($request->oldPassword, $student->password)){
            $student->password  = Hash::make($request->get('password'));
        } else {
            return $this::faild(trans('auth.old password is wrong'), 400);
        }

        if($student->save()){
            return $this::success(trans('auth.change password success'), 200);
        } else {
            return $this::faild(trans('auth.update password falid'), 400);
        }
    }

    public function change_image(Request $request){
        try{
            DB::beginTransaction();
            // validate registeration request
            $validator = Validator::make($request->all(), [
                'image'       => 'required|mimes:jpeg,jpg,png,gif',
            ]);

            if($validator->fails()){
                return $this::faild($validator->errors(), 403);
            }

            //get student
            if (! $student = auth('student')->user()) {
                return $this::faild(trans('user.auth not found'), 404, 'E04');
            }

            //update image
            $path = $this->upload_image($request->file('image'),'uploads/students', 300, 300);

            if($student->Image == null){
                //if user don't have image 
                Image::create([
                    'imageable_id'   => $student->id,
                    'imageable_type' => 'App\Models\Student',
                    'src'            => $path,
                ]);

            } else {
                //if student have image
                $oldImage = $student->Image->src;

                if(file_exists(base_path('public/uploads/students/') . $oldImage)){
                    unlink(base_path('public/uploads/students/') . $oldImage);
                }

                $student->Image->src = $path;
                $student->Image->save();
            }

            DB::commit();
            return $this::success(trans('auth.update image success'), 200);
        } catch(\Exception $ex){
            return $this::faild(trans('auth.update image faild'), 200);
        }   
    }
}

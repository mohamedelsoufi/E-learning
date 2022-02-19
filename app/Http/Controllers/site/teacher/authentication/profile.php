<?php

namespace App\Http\Controllers\site\teacher\authentication;

use App\Http\Controllers\Controller;
use App\Http\Resources\teacherResource;
use App\Models\Image;
use App\Models\Tag;
use App\Models\Teacher;
use App\Traits\response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class profile extends Controller
{
    use response;
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

        return $this->success(trans('auth.success'), 200, 'teacher', new teacherResource($teacher));
    }

    public function myProfile(){
        //get teacher
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }

        return $this->success(trans('auth.success'), 200, 'teacher', new teacherResource($teacher));
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

        //get teacher or vender
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('user.teacher not found'), 404, 'E04');
        }        
        
        //update teacher pass
        if(Hash::check($request->oldPassword, $teacher->password)){
            $teacher->password  = Hash::make($request->get('password'));
        } else {
            return $this::faild(trans('auth.old password is wrong'), 400);
        }

        if($teacher->save()){
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

            //get teacher
            if (! $teacher = auth('teacher')->user()) {
                return $this::faild(trans('auth.teacher not found'), 404, 'E04');
            }

            //update image
            $path = $this->upload_image($request->file('image'),'uploads/teachers', 300, 300);

            if($teacher->Image == null){
                //if user don't have image 
                Image::create([
                    'imageable_id'   => $teacher->id,
                    'imageable_type' => 'App\Models\Teacher',
                    'src'            => $path,
                ]);

            } else {
                //if teacher have image
                $oldImage = $teacher->Image->src;

                if(file_exists(base_path('public/uploads/teachers/') . $oldImage)){
                    unlink(base_path('public/uploads/teachers/') . $oldImage);
                }

                $teacher->Image->src = $path;
                $teacher->Image->save();
            }

            DB::commit();
            return $this::success(trans('auth.update image success'), 200);
        } catch(\Exception $ex){
            return $this::faild(trans('auth.update image faild'), 200);
        }   
    }

    public function updateProfile(Request $request){
        //get teacher or vender
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }

        // validate
        $validator = Validator::make($request->all(), [
            'username'          => 'nullable|string|max:250|unique:teachers,username,'. $teacher->id,
            'email'             => 'nullable|email|max:255|unique:teachers,email,'. $teacher->id,
            'dialing_code'      => 'nullable|string|max:10',
            'phone'             => 'nullable|string|max:20|unique:teachers,phone,'. $teacher->id,
            'password'          => 'nullable|string|max:250',
            'country_id'        => 'nullable|integer|exists:countries,id',
            'curriculum_id'     => 'nullable|integer|exists:curriculums,id',
            'gender'            => ['nullable',Rule::in(0,1,2)],    //0->male  1->female
            'birth'             => 'nullable|date',
            'about'             => 'nullable|string|max:1000',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403);
        }

        //selet student

        $input = $request->only(
            'username','email','dialing_code', 'phone','password','country_id','curriculum_id','year_id',
            'gender', 'birth'
        );

        //update student
        if($teacher->update($input)){
            return $this->success(trans('auth.update profile success'), 200, 'teacher', new teacherResource($teacher));
        } else {
            return $this::faild(trans('auth.update profile falid'), 400);
        }
    }
}

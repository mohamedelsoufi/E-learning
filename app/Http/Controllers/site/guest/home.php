<?php

namespace App\Http\Controllers\site\guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\classTypeResource;
use App\Http\Resources\countryResource;
use App\Http\Resources\curriculumResource;
use App\Http\Resources\materialResource;
use App\Http\Resources\teacher_classesTypeResourc;
use App\Http\Resources\teacherResource;
use App\Models\Class_type;
use App\Models\Country;
use App\Models\Curriculum;
use App\Models\Subject;
use App\Models\Teacher;
use App\Traits\response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class home extends Controller
{
    use response;
    public function teachersBysubject(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'subject_id'    => 'required|exists:subjects,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //online
        $online_teachers = Teacher::active()
                                    ->where('online', 1)
                                    ->whereHas('Subject_teachers', function($q) use($request){
                                        $q->where('subject_id', $request->get('subject_id'));
                                    })
                                    ->limit(5)
                                    ->get();
        //offline
        $offline_teachers = Teacher::active()
                                    ->where('online', 0)
                                    ->whereHas('Subject_teachers', function($q) use($request){
                                        $q->where('subject_id', $request->get('subject_id'));
                                    })
                                    ->paginate(5);
        // ->inRandomOrder()

        return response()->json([
            'successful'        => true,
            'message'           => trans('auth.success'),
            'online_teachers'   => teacherResource::collection($online_teachers),
            'offline_teachers'  => teacher_classesTypeResourc::collection($offline_teachers)->response()->getData(true),
        ], 200);
    }

    public function online_teachers_bysubject(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'subject_id'    => 'required|exists:subjects,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //online
        $online_teachers = Teacher::active()
                                    ->where('online', 1)
                                    ->whereHas('Subject_teachers', function($q) use($request){
                                        $q->where('subject_id', $request->get('subject_id'));
                                    })
                                    ->limit(5)
                                    ->get();

        return response()->json([
            'successful'        => true,
            'message'           => trans('auth.success'),
            'online_teachers'   => teacher_classesTypeResourc::collection($online_teachers),
        ], 200);
    }

    public function materials(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'subject_id'    => 'required|exists:subjects,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //get materials
        $materials = Subject::find($request->get('subject_id'))->Materials;

        return $this->success(
            trans('auth.success'),
            200,
            'materials',
            materialResource::collection($materials)
        );
    }

    public function classes_type_cost(Request $request){
        // validate registeration request
        $validator = Validator::make($request->all(), [
            'teacher_id'     => 'required|integer|exists:teachers,id',
            'subject_id'     => 'required|integer|exists:subjects,id',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403);
        }

        $classes_type = Class_type::active()->get();

        return $this->success(
            trans('auth.success'),
            200,
            'classes_type',
            classTypeResource::collection($classes_type)
        );
        
    }

    public function countries(){
        $countries = Country::active()->get();

        return $this->success(
            trans('auth.success'),
            200,
            'countries',
            countryResource::collection($countries)
        );
    }

    public function curriculums(){
        $curriculums = Curriculum::active()->get();

        return $this->success(
            trans('auth.success'),
            200,
            'curriculums',
            curriculumResource::collection($curriculums)
        );
    }
}

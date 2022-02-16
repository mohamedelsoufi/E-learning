<?php

namespace App\Http\Controllers\site\guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\materialResource;
use App\Http\Resources\teacherResource;
use App\Models\Subject;
use App\Models\Subject_teacher;
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
                                    ->get();
        //offline
        $offline_teachers = Teacher::active()
                                    ->where('online', 0)
                                    ->whereHas('Subject_teachers', function($q) use($request){
                                        $q->where('subject_id', $request->get('subject_id'));
                                    })
                                    ->get();

        return response()->json([
            'successful'        => true,
            'message'           => trans('auth.success'),
            'online_teachers'   => teacherResource::collection($online_teachers),
            'offline_teachers'  => teacherResource::collection($offline_teachers),
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
        return materialResource::collection($materials);
    }
}

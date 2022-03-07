<?php

namespace App\Http\Controllers\site\guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\level_year_subjects;
use App\Http\Resources\level_year_subjectsResource;
use App\Http\Resources\level_yearResource;
use App\Http\Resources\subjectsResource;
use App\Http\Resources\yearResource;
use App\Models\Level;
use App\Models\Subject;
use App\Models\Year;
use App\Traits\response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class search extends Controller
{
    use response;
    public function level_year(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'curriculum_id'    => 'required|exists:curriculums,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors()->first(), 403, 'E03');
        }

        $level = Level::active()
                            ->where('curriculum_id', $request->get('curriculum_id'))
                            ->with('Years')
                            ->get();

        // return yearResource::collection(Year::get());
        return $this->success(trans('auth.success'), 200, 'levels', level_yearResource::collection($level));
    }

    public function level_year_subjects(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'curriculum_id'    => 'required|exists:curriculums,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors()->first(), 403, 'E03');
        }

        $levels = Level::active()
                            ->where('curriculum_id', $request->get('curriculum_id'))
                            ->with('Years.Subjects')
                            ->get();

        // return yearResource::collection(Year::get());
        return $this->success(trans('auth.success'), 200, 'levels', level_year_subjectsResource::collection($levels));
    }

    public function subjects(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'year_id'    => 'required|exists:years,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors()->first(), 403, 'E03');
        }

        $subjects = Subject::active()->whereHas('Term', function($query) use($request){
            $query->where('year_id', $request->get('year_id'));
        })->get();

        return $this->success(
            trans('auth.success'),
            200,
            'subjects',
            subjectsResource::collection($subjects),
        );
    }
}

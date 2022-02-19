<?php

namespace App\Http\Controllers\site\student;

use App\Http\Controllers\Controller;
use App\Http\Resources\classType_availableClassResource;
use App\Http\Resources\subjectsResource;
use App\Http\Resources\term_SubjectResource;
use App\Models\Available_class;
use App\Models\Class_type;
use App\Models\Cost_country;
use App\Models\Cost_level;
use App\Models\Cost_student_number;
use App\Models\Settings;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use App\Traits\response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class home extends Controller
{
    use response;
    public function index(){
        //get student or vender
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        if($student->year_id == null)
            return $this::faild(trans('site.student must choose an academic year'), 400, 'E00');

        $terms = Term::where('status', 1)
                        ->whereHas('Year', function($query) use($student){
                            $query->where('id', $student->year_id);
                        })->with(['Subjects' => function($q){
                            $q->active();
                        }])
                        ->get();

        return $this::success(trans('auth.success'), 400, 'terms', term_SubjectResource::collection($terms));
    }

    public function leave(){
        //get student
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        //change online and offline
        $student->online = 0;
        $student->save();

        return $this->success(trans('auth.success'), 200);
    }

    public function available_classes(Request $request){
        // validate registeration request
        $validator = Validator::make($request->all(), [
            'teacher_id'     => 'required|integer|exists:teachers,id',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403);
        }

        //get get class type available class
        $class_type = Class_type::active()->get();

        return $this->success(trans('auth.success'), 200, 'class_types', classType_availableClassResource::collection($class_type));
    }

    public function test(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'available_class_id' => 'required|exists:available_classes,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //get available_classes
        $available_class = Available_class::find($request->get('available_class_id'));
        //get setings
        $setting = Settings::first();

        //get class_type
        $class_type = $available_class->Class_type;

        //get cost_country
        $cost_country = Cost_country::where('country_id', $available_class->Teacher->id)->first();
        if($cost_country != null){
            $cost_country = $cost_country->cost;
        } else{
            $cost_country = $setting->cost_country;
        }

        //get cost levels
        $cost_level     = Cost_level::where('level_id', $available_class->Subject->Term->Year->Level->id)->first();
        if($cost_level != null){
            $cost_level = $cost_level->cost;
        } else {
            $cost_level = $setting->cost_level;
        }
        return ($cost_country * $cost_level * $class_type->long_cost) * $class_type->long;
    }
}

<?php

namespace App\Http\Controllers\site\student;

use App\Http\Controllers\Controller;
use App\Http\Resources\classType_availableClassResource;
use App\Http\Resources\student_classResource;
use App\Http\Resources\term_SubjectResource;
use App\Models\Class_type;
use App\Models\Cost_country;
use App\Models\Cost_level;
use App\Models\Settings;
use App\Models\Term;
use App\Traits\response;
use App\Services\AgoraService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class home extends Controller
{
    use response;
    public function __construct(AgoraService $AgoraService)
    {
        $this->AgoraService         = $AgoraService;
    }
    public function index(){
        //get student or vender
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        if($student->year_id == null)
            return $this::faild(trans('site.student must choose his grade'), 400, 'E00');

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
            'subject_id'     => 'required|integer|exists:subjects,id',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403);
        }

        //get get class type available class
        $class_type = Class_type::active()->get();

        return $this->success(trans('auth.success'), 200, 'class_types', classType_availableClassResource::collection($class_type));
    }

    public function my_reservations(){
        //get student or vender
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        return $this->success(
            trans('auth.success'),
            200,
            'reservations',
            student_classResource::collection($student->Student_classes)
        );

    }

    public function test(){
        return $this->AgoraService->generateToken();
    }
    
}

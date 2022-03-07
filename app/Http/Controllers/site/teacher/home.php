<?php

namespace App\Http\Controllers\site\teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\availableClassResource;
use App\Http\Resources\liveResource;
use App\Models\Available_class;
use App\Models\Class_type;
use App\Models\Live;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class home extends Controller
{
    public function schedule(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'date'             => 'nullable|date_format:Y-m-d',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403, 'E03');
        }

        //get teacher
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }

        //get all
        $schedules = Available_class::where('teacher_id', $teacher->id)
                                ->schedule()
                                ->get();

        //get by date
        if($request->get('date') != null){
            $schedules = Available_class::where('teacher_id', $teacher->id)
                                ->whereDate('from', '=',$request->get('date'))
                                ->schedule()
                                ->get();
        }

        return $this::success(
            trans('auth.success'),
            200,
            'schedules',
            availableClassResource::collection($schedules)
        );
    }

    public function add_schedule(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'subject_id'       => 'required|exists:subjects,id',
            'class_type_id'    => 'required|exists:class_types,id',
            'from'             => 'required|date_format:Y-m-d H:i:s',
            'note'             => 'nullable|exists:class_types,id',

        ]);

        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403, 'E03');
        }

        //get teacher
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }
        //get class type
        $class_type = Class_type::find($request->get('class_type_id'));

        //get subject_id
        // $subject = Subject::find($request->get('subject_id'));

        $newtimestamp = strtotime($request->get('from') . ' + ' . $class_type->long . ' minute');
        $to =  date('Y-m-d H:i:s', $newtimestamp);


        Available_class::create([
            'teacher_id'            => $teacher->id,
            'subject_id'            => $request->get('subject_id'),
            'class_type_id'         => $request->get('class_type_id'),
            'from'                  => $request->get('from'),
            'to'                    => $to,
            'long'                  => $class_type->long,
            'company_percentage'    => $this->get_company_percentage($teacher),
            'note'                  => $request->get('note'),
            'cost'                  => $class_type->long * $class_type->long_cost,
        ]);

        return $this->success(trans('auth.success'), 200);
    }

    public function cancel_schedule(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'schedule_id'       => 'required|exists:available_classes,id',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403, 'E03');
        }

        //get teacher
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }

        $available_class = Available_class::where('teacher_id', $teacher->id)->find($request->get('schedule_id'));
        //if availble_class is empty
        if($available_class == null)
            return $this->faild(trans('site.schedule not found'), 400, 'E04');
        
        $available_class->status = -1;
        $available_class->save();

        return $this->success(trans('auth.success'), 200);
    }
}

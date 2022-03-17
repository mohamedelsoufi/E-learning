<?php

namespace App\Http\Controllers\site\teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\schedules_dateResource;
use App\Models\Available_class;
use App\Models\Class_type;
use App\Models\Student;
use App\Models\student_notification;
use App\Models\Teacher;
use App\Services\AgoraService;
use App\Services\firbaseNotifications;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class home extends Controller
{
    public function __construct(AgoraService $AgoraService, firbaseNotifications $firbaseNotifications)
    {
        $this->AgoraService         = $AgoraService;
        $this->firbaseNotifications = $firbaseNotifications;
    }

    public function schedule(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            // 'date'             => 'nullable|date_format:Y-m-d',
            'month'            => 'nullable|min:1|max:12',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403, 'E03');
        }
        
        //get teacher
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }
        $request->teacher_id = $teacher->id;

        //get schedules_date
        ($request->get('month') == null) ? $month = Carbon::today()->month : $month = $request->get('month');
        $schedules_date = Available_class::where('teacher_id', $teacher->id)
                                ->schedule()
                                ->select('from', 'from_date')
                                ->whereMonth('from','=', $month)
                                ->distinct('from_date')
                                ->orderBy('from')
                                ->get();

        return response()->json([
            'successful'    => true,
            'message'       => trans('auth.success'),
            'schedules'     => schedules_dateResource::collection($schedules_date),
        ], 200);
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

        $newtimestamp = strtotime($request->get('from') . ' + ' . $class_type->long . ' minute');
        $to =  date('Y-m-d H:i:s', $newtimestamp);


        Available_class::create([
            'teacher_id'            => $teacher->id,
            'subject_id'            => $request->get('subject_id'),
            'class_type_id'         => $request->get('class_type_id'),
            'from'                  => $request->get('from'),
            'from_date'             => date('Y-m-d', strtotime($request->get('from'))),
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

    public function start_class(Request $request){
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

        //get available_classes
        $available_class = Available_class::find($request->get('schedule_id'));

        //make avilable class start
        $available_class->status = 2;
        $available_class->save();

        
        $student_classes = DB::table('student_class')
                            ->where('available_class_id', ($available_class->id))
                            ->get();
                            // ->update(['from' => Carbon::now()]);

        foreach($student_classes as $student_class){
            //make notification to student
            student_notification::create([
                'title'             => 'title',
                'content'           => 'content',
                'teacher_id'        => $teacher->id,
                'student_id'        => $student_class->student_id,
                'available_class_id'=> $student_class->available_class_id,
                'type'              => 3,
                'agora_token'       => $this->AgoraService->generateToken()['token'],
                'agora_channel_name'=> $this->AgoraService->generateToken()['channel_name'],
            ]);

            //send firbase notifications
            $student = Student::find($student_class->student_id);
            $this->firbaseNotifications->send_notification('title', 'body', $student->token_firebase);
        }
        
        return $this->success(trans('auth.success'), 200);
    }
}

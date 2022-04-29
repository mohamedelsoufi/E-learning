<?php

namespace App\Http\Controllers\site\teacher;

use App\Events\MyEvent;
use App\Events\studentNotification;
use App\Http\Controllers\Controller;
use App\Http\Resources\availableClassResource;
use App\Http\Resources\notificationResource;
use App\Http\Resources\yearResource;
use App\Models\Available_class;
use App\Models\Class_type;
use App\Models\Student;
use App\Models\student_notification;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Year;
use App\Services\AgoraService;
use App\Services\firbaseNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Jobs\teacherSalary as JobsTeacherSalary;


class home extends Controller
{
    public function __construct(AgoraService $AgoraService, firbaseNotifications $firbaseNotifications)
    {
        $this->AgoraService         = $AgoraService;
        $this->firbaseNotifications = $firbaseNotifications;
    }

    public function schedule_date(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
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
        // ($request->get('month') == null) ? $month = Carbon::today()->month : $month = $request->get('month');
        if($request->get('month') == null){
            $schedules_date = Available_class::where('teacher_id', $teacher->id)
                                ->schedule()
                                ->select('from_date as date')
                                // ->whereHas('Student_classes')
                                ->distinct('from_date')
                                ->orderBy('from')
                                ->get();
        } else {
            $schedules_date = Available_class::where('teacher_id', $teacher->id)
            ->schedule()
            ->select('from_date as date')
            ->whereMonth('from','=', $request->get('month'))
            // ->whereHas('Student_classes')
            ->distinct('from_date')
            ->orderBy('from')
            ->get();
        }

        return response()->json([
            'successful'    => true,
            'message'       => trans('auth.success'),
            'schedules_date'     => $schedules_date->pluck('date'),
        ], 200);                       
    }

    public function schedule(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'date'      => 'required|date_format:Y-m-d',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403, 'E03');
        }
        
        //get teacher
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }
        //get schedules_date
        $available_class = Available_class::where('teacher_id', $teacher->id)
                                    ->whereDate('from', '=', $request->get('date'))
                                    ->schedule()
                                    // ->whereHas('Student_classes')
                                    ->orderBy('from')
                                    ->get();

        return response()->json([
            'successful'    => true,
            'message'       => trans('auth.success'),
            'schedules'     => availableClassResource::collection($available_class),
        ], 200);                       
    }

    public function class_type(){
        $classes_type = Class_type::select('id', 'long')->active()->get();

        return $this->success(trans('auth.success'), 200, 'classes_type', $classes_type);
    }

    public function add_schedule(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'year_id'          => 'required|exists:years,id',
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
        $year_id = $request->get('year_id');
        $subject = Subject::where('main_subject_id', $teacher->main_subject_id)
                            ->whereHas('Term', function($query) use($year_id){
                                $query->where('year_id', $year_id);
                            })
                            ->first();
        
        if($subject == null){
            return $this->faild(trans('site.your subject not in this year'), 404, 'E04');
        }
        
        $newtimestamp = strtotime($request->get('from') . ' + ' . $class_type->long . ' minute');
        $to =  date('Y-m-d H:i:s', $newtimestamp);


        $schedule = Available_class::create([
            'teacher_id'            => $teacher->id,
            'subject_id'            => $subject->id,
            'class_type_id'         => $request->get('class_type_id'),
            'from'                  => $request->get('from'),
            'from_date'             => date('Y-m-d', strtotime($request->get('from'))),
            'to'                    => $to,
            'long'                  => $class_type->long,
            'company_percentage'    => $this->get_company_percentage($teacher),
            'note'                  => $request->get('note'),
            'cost'                  => $class_type->long * $class_type->long_cost,
        ]);

        return $this->success(trans('auth.success'), 200, 'schedule', new availableClassResource($schedule));
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

        $available_class = Available_class::where('status','!=' ,-1)
                                            ->where('teacher_id', $teacher->id)
                                            ->find($request->get('schedule_id'));
        //if availble_class is empty
        if($available_class == null)
            return $this->faild(trans('site.schedule not found'), 400, 'E04');
        
        //cansel class
        $available_class->status = -1;
        $available_class->save();

        //if student booking class return mony for him
        $students_class = DB::table('student_class')
                        ->where('available_class_id', $request->get('schedule_id'))
                        ->get();

        if($students_class != Null){
            foreach($students_class as $student_class){
                $student = Student::find($student_class->student_id);

                if($student_class->pay == 1){ //if student booking by free_classes
                    $student->free +=1;
                    $student->save();
                } else {    //if student booking by mony in balance
                    $available_class = Available_class::find($student_class->available_class_id);
                    $student->balance += $available_class->cost;
                    $student->save();
                }
            }
        }

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

        $students = Student::WhereHas('Student_classes', function($query) use($request){
            $query->where('available_class_id', $request->get('schedule_id'));
        })->get();

        //if teacher already make call
        if($available_class->agora_token != null){  
            $data = [
                'token'         => $available_class->agora_token,
                'rtm_token'     => $available_class->agora_rtm_token,
                'rtm_user_id'   => 'teacher_' . $teacher->id,
                'channel_name'  => $available_class->channel_name,
                'teacher'       => [
                    'id'        => $teacher->id,
                    'username'  => $teacher->username,
                    'image'     => $teacher->getImage(),
                ],
                'students'       => $students->map(function ($data) {
                    return [
                        'id'        => $data->id,
                        'username'  => $data->username,
                        'image'     => $data->getImage(),
                    ];
                }),
            ];
            
            return $this->success(trans('auth.success'), 200, 'agora', $data);
        }

        //if teacher do not make call
        $student_classes = DB::table('student_class')   
                            ->where('available_class_id', ($available_class->id))
                            ->get();
        
        if(count($student_classes) == 0){       //check if there are student booking this class
            return $this->faild(trans('auth.no student booking this class'), 400);
        }

        //creat agora room
        $agora          = $this->AgoraService->generateToken('teacher_' . $teacher->id);

        //change available_class status
        $available_class->status = 2;
        $available_class->save();

        foreach($student_classes as $student_class){
            $title = 'يوجد حصه الان';
            $body  = 'حصه قمت بحجزها سارع بالانضمام ' . $teacher->username . ' بدأ';

            //make notification to student
            $student_notification = student_notification::create([
                'title'             => $title,
                'content'           => $body,
                'teacher_id'        => $teacher->id,
                'student_id'        => $student_class->student_id,
                'available_class_id'=> $student_class->available_class_id,
                'type'              => 3,
                'agora_token'       => $agora['token'],
                'agora_rtm_token'         => $agora['rtm_token'],
                'agora_channel_name'=> $agora['channel_name'],
            ]);

            //save agora_token in class
            $available_class->agora_token  = $agora['token'];
            $available_class->agora_rtm_token  = $agora['rtm_token'];
            $available_class->channel_name = $agora['channel_name'];
            $available_class->save();

            //send firbase notifications
            if($request->get('pusher') == 1){
                config(['queue.default' => 'sync']);
                event(new studentNotification($student_class->student_id,new notificationResource($student_notification)));
            } else {
                $student = Student::find($student_class->student_id);
                $this->firbaseNotifications->send_notification($title, $body, $student->token_firebase);
            }
        }

        $data = [
            'token'         => $agora['token'],
            'rtm_token'     => $available_class->agora_rtm_token,
            'rtm_user_id'   => 'teacher_' . $teacher->id,
            'channel_name'  => $agora['channel_name'],
            'teacher'       => [
                'id'        => $teacher->id,
                'username'  => $teacher->username,
                'image'     => $teacher->getImage(),
            ],
            'students'       => $students->map(function ($data) {
                return [
                    'id'        => $data->id,
                    'username'  => $data->username,
                    'image'     => $data->getImage(),
                ];
            }),
        ];
        
        return $this->success(trans('auth.success'), 200, 'agora', $data);
    }

    public function teacher_years(){
        //get teacher
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }
        
        // return $teacher->Teacher_years;
        $years = Year::whereHas('Teacher_years', function($query) use($teacher){
            $query->where('teacher_id', $teacher->id);
        })
        ->whereHas('Terms', function($query) use($teacher){
            $query->whereHas('Subjects', function($q) use($teacher){
                $q->active()->where('main_subject_id', $teacher->main_subject_id);
            });
        })
        ->get();
        
        
        return $this->success(trans('auth.success'), 200, 'years', yearResource::collection($years));
    }

    public function test(){
        // return date("Y-m-d h:i:s",'1650795048');
        return $this->AgoraService->generateToken('teacher_id');
        config(['queue.default' => 'sync']);
        event(new MyEvent('test'));

        // return 'good';

        Teacher::whereHas('Available_classes', function($query){
            $query->where('teacher_mony', 0)->where('status', 2);
        })
        ->chunk(30, function($data){
            dispatch(new JobsTeacherSalary($data));
        });

        return 'good';
    }
}

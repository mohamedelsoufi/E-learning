<?php

namespace App\Http\Controllers\site\student;

use App\Events\teacherNotification;
use App\Http\Controllers\Controller;
use App\Http\Resources\availableClassResource;
use App\Http\Resources\classType_availableClassResource;
use App\Http\Resources\notificationResource;
use App\Http\Resources\student_classResource;
use App\Http\Resources\subjectsResource;
use App\Models\Available_class;
use App\Models\Class_type;
use App\Models\Subject;
use App\Models\Teacher_notification;
use App\Models\Video;
use App\Traits\response;
use App\Services\AgoraService;
use App\Services\firbaseNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class home extends Controller
{
    use response;
    public function __construct(AgoraService $AgoraService, firbaseNotifications $firbaseNotifications)
    {
        $this->AgoraService         = $AgoraService;
        $this->firbaseNotifications = $firbaseNotifications;
    }

    public function index(){
        //get student or vender
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        if($student->year_id == null)
            return $this::faild(trans('site.student must choose his grade'), 400, 'E00');

        $subjects = Subject::whereHas('Term', function($query) use($student){
                                $query->where('year_id', $student->year_id);
                            })
                            ->active()
                            ->get();

        return $this::success(trans('auth.success'), 200, 'subjects', subjectsResource::collection($subjects));
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
            return $this::faild($validator->errors()->first(), 403);
        }

        //get get class type available class
        $class_type = Class_type::active()->get();

        return $this->success(trans('auth.success'), 200, 'class_types', classType_availableClassResource::collection($class_type));
    }

    public function booking(Request $request){  //class
        // validate registeration request
        $validator = Validator::make($request->all(), [
            'available_class_id'     => 'required|integer|exists:available_classes,id',
            'promo_code'             => 'nullable|string',
            'pusher'                 => 'nullable|integer',
        ]);

        if($validator->fails()){
            return response()->json([
                'successful'=> false,
                'not_enough' => false,
                'message'    => $validator->errors()->first(),
            ], 400);
        }

        try{
            DB::beginTransaction();
            //get student
            if (! $student = auth('student')->user()) {
                return $this::faild(trans('auth.student not found'), 404, 'E04');
            }

            //get available class
            $available_class = Available_class::find($request->get('available_class_id'));

            //check if class is complete
            $row = DB::table('student_class')->where([
                'available_class_id'   => $request->get('available_class_id'),
            ])->first();

            if($row != null){
                return response()->json([
                    'successful'=> false,
                    'not_enough' => false,
                    'message'    => trans('site.this class is complete'),
                ], 400);
            }
            
            //get discount from promo code if exist
            $discount_percentage = $this->promo_code_percentage($request->get('promo_code'));
            $available_class_cost_after_discount = $this->get_price_after_discount($available_class->cost, $discount_percentage);

            //take booking mony
            if($student->free > 0){  //if student have free classes
                $student->free -= 1;
                $student->save();
                $pay = 1;
            } else {            
                //check if student balance Not enough
                // $available_class_cost = $available_class->Class_type->long * $available_class->Class_type->long_cost; //get price from class type becouse if admin chage class type cost
                if($student->balance - $available_class_cost_after_discount < 0){
                    return response()->json([
                        'successful'    => false,
                        'not_enough'    => true,
                        'message'       => trans('site.your balance not enough'),
                    ], 400);
                }

                $student->balance       -= $available_class_cost_after_discount;    //take class cost from student
                $student->save();
                $pay = 0;
            }

            //booking
            DB::table('student_class')->insert([
                'student_id'            =>  $student->id,
                'available_class_id'    =>  $request->get('available_class_id'),
                'promocode_descount'    =>  $discount_percentage,
                'pay'                   =>  $pay,
            ]);

            //create notification to teacher
            $teacher_notification = Teacher_notification::create([
                'title'             => 'title',
                'content'           => 'content',
                'teacher_id'        => $available_class->teacher_id,
                'student_id'        => $student->id,
                'available_class_id'=> $available_class->id,
                'type'              => 1,
            ]);

            //sent firbase notifications
            if($request->get('pusher') == 1){
                event(new teacherNotification($available_class->teacher_id,new notificationResource($teacher_notification)));
            } else {
                $this->firbaseNotifications->send_notification('title', 'body', $available_class->Teacher->token_firebase);
            }

            DB::commit();
            return $this->success(trans('auth.success'), 200);
        } catch(\Exception $ex){
            //if there are error
            return $this->faild(trans('auth.faild'), 200);
        }
    }

    public function buy_video(Request $request){ //video
        // validate registeration request
        $validator = Validator::make($request->all(), [
            'video_id'     => 'required|integer|exists:videos,id',
        ]);
        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403);
        }

        try{
            DB::beginTransaction();

            //get student
            if (! $student = auth('student')->user()) {
                return $this::faild(trans('auth.student not found'), 404, 'E04');
            }
            //get video
            $video = Video::find($request->get('video_id'));
            $teacher =  $video->Teacher;

            //check if student already buy this video
            $row = DB::table('student_video')->where([
                'student_id'   => $student->id,
                'video_id'     => $request->get('video_id'),
            ])->first();

            if($row != null)
                return $this->faild(trans('site.student already buy this video'), 200);

            //check if student balance Not enough
            if($student->balance - $video->cost < 0)
                return $this->faild(trans('site.your balance not enough'), 200);

            //booking
            DB::table('student_video')->insert([
                'student_id'   => $student->id,
                'video_id'     => $request->get('video_id'),
            ]);
            $student->balance -= $video->cost;    //tack video cost from student
            $student->save();

            $teacher->balance += $video->cost;    //add video cost for teacher
            $teacher->save();

            DB::commit();
            return $this->success(trans('auth.success'), 200);
        } catch(\Exception $ex){
            //if there are error
            return $this->faild(trans('auth.faild'), 200);
        }
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

    public function schedule(){
        //get student or vender
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        $available_classes = available_class::whereHas('Student_classes', function($query) use($student){
            $query->where('student_id', $student->id);
        })
        ->orderBy('from')
        ->schedule();

        return response()->json([
            'successful'            => true,
            'message'               => trans('auth.success'),
            'schedules_count'       => $available_classes->count(),
            'schedules'             => availableClassResource::collection($available_classes->paginate(5))->response()->getData(true),
        ], 200);

        return $this->success(
            trans('auth.success'),
            200,
            'schedules',
        );

    }

    public function cancel_schedule(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'schedule_id'       => 'required|exists:available_classes,id',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403, 'E03');
        }

        //get student
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        DB::table('student_class')
            ->where('available_class_id', $request->get('schedule_id'))
            ->where('student_id', $student->id)
            ->delete();

        return $this->success(trans('auth.success'), 200);
    }

    public function test(){
        return $this->AgoraService->generateToken();
    }
    
}

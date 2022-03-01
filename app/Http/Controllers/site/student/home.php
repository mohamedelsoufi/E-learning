<?php

namespace App\Http\Controllers\site\student;

use App\Http\Controllers\Controller;
use App\Http\Resources\classType_availableClassResource;
use App\Http\Resources\student_classResource;
use App\Http\Resources\subjectsResource;
use App\Models\Available_class;
use App\Models\Class_type;
use App\Models\Subject;
use App\Models\Video;
use App\Traits\response;
use App\Services\AgoraService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // $terms = Term::where('status', 1)
        //                 ->whereHas('Year', function($query) use($student){
        //                     $query->where('id', $student->year_id);
        //                 })->with(['Subjects' => function($q){
        //                     $q->active();
        //                 }])
        //                 ->get();
        
        $subject = Subject::whereHas('Term', function($query) use($student){
                                $query->where('year_id', $student->year_id);
                            })
                            ->active()
                            ->get();

        return $this::success(trans('auth.success'), 400, 'subjects', subjectsResource::collection($subject));
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

    public function booking(Request $request){
        // validate registeration request
        $validator = Validator::make($request->all(), [
            'available_class_id'     => 'required|integer|exists:available_classes,id',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors(), 403);
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

            if($row != null)
                return $this->faild(trans('site.this class is complete'), 200);

            //check if student balance Not enough
            if($student->balance - $available_class->cost < 0)
                return $this->faild(trans('site.your balance not enough'), 200);

            //booking
            DB::table('student_class')->insert([
                'student_id'            =>  $student->id,
                'available_class_id'    => $request->get('available_class_id'),
            ]);
            $student->balance -= $available_class->cost;    //tack class cost from student
            $student->save();

            DB::commit();
            return $this->success(trans('auth.success'), 200);
        } catch(\Exception $ex){
            //if there are error
            return $this->faild(trans('auth.faild'), 200);
        }
    }

    public function buy_video(Request $request){
        // validate registeration request
        $validator = Validator::make($request->all(), [
            'video_id'     => 'required|integer|exists:videos,id',
        ]);
        if($validator->fails()){
            return $this::faild($validator->errors(), 403);
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

    public function test(){
        return $this->AgoraService->generateToken();
    }
    
}

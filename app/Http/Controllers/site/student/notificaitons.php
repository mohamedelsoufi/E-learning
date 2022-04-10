<?php

namespace App\Http\Controllers\site\student;

use App\Http\Controllers\Controller;
use App\Http\Resources\notificationResource;
use App\Models\student_notification;
use Illuminate\Http\Request;

class notificaitons extends Controller
{
    public function index(){
        //get student
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        //get student notifications
        $new_notifications = student_notification::where('student_id', $student->id)
                                ->where('seen', 0)
                                ->count();

        $notifications = student_notification::where('student_id', $student->id)
                                ->orderBy('id', 'desc')
                                ->get();

        student_notification::where('student_id', $student->id)
                                ->where('seen', 0)
                                ->update(['seen'=> 1]);

        return response()->json([
            'successful'            => true,
            'message'               => trans('auth.success'),
            'new_notifications'     => $new_notifications,
            'notifications'         => notificationResource::collection($notifications),
        ], 200);
    }

    public function notification_count(){
        //get student
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        //get teacher notifications
        $notifications_count = student_notification::where('student_id', $student->id)
                                ->where('seen', 0)
                                ->count();

        return response()->json([
            'successful'            => true,
            'message'               => trans('auth.success'),
            'notifications_count'   => $notifications_count,
        ], 200);
    }
}

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

        //get teacher notifications
        $notifications = student_notification::where('student_id', $student->id)
                                ->orderBy('id', 'desc')
                                ->get();

        return $this->success(
            trans('auth.success'),
            200,
            'notifications',
            notificationResource::collection($notifications)
        );
    }
}

<?php

namespace App\Http\Controllers\site\teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\notificationResource;
use App\Models\Teacher;
use App\Models\Teacher_notification;
use Illuminate\Http\Request;

class notificaitons extends Controller
{
    public function index(){
        //get teacher
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }

        //get teacher notifications
        $notifications = Teacher_notification::where('teacher_id', $teacher->id)
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

<?php

namespace App\Http\Resources;

use App\Http\Controllers\admin\students;
use App\Models\Available_class;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Resources\Json\JsonResource;

class notificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        ($request->header('lang') == 'ar')? $lang = 'ar': $lang = 'en';

        $student = Student::find($this->student_id);
        if($student != null){
            $student = [
                'id'        => $student->id,
                'username'  => $student->username,
                'image'     => $student->getImage(),
            ];
        } else {
            $student = null;
        }

        $teacher = Teacher::find($this->teacher_id);
        if($teacher != null){
            $teacher = [
                'id'        => $teacher->id,
                'username'  => $teacher->username,
                'image'     => $teacher->getImage(),
            ];
        } else {
            $teacher = null;
        }

        $available_class = Available_class::find($this->available_class_id );

        if($available_class != null){
            $available_class = [
                'id'    => $available_class->id,
                'to'    => $available_class->to,
                'from'  => $available_class->from,
                'subject'   => [
                                    'id'    => $available_class->Subject->id,
                                    'name'  => $available_class->Subject->Main_subject->translate($lang)->name,
                                ],
            ];
        } else {
            $available_class = null;
        }

        if($this->agora_token != null){
            $agora = [
                        'token'             => $this->agora_token,
                        'agora_rtm_token'   => $this->agora_rtm_token,
                        'channel_name'      => $this->agora_channel_name,
                        'available_class'   => $available_class,
            ];
        } else {
            $agora = null;
        }

        return [
            'id'                    => $this->id,
            'title'                 => $this->title,
            'content'               => $this->content,
            'type'                  => $this->type,
            'seen'                  => $this->seen,
            'created_at'            => date("Y-m-d H:i:s", strtotime($this->created_at)),
            'agora'                 => $agora,
            'student'               => $student,
            'teacher'               => $teacher,
            // 'class'                 => $available_class,
        ];
    }
}

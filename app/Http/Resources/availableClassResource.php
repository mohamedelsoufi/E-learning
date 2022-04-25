<?php

namespace App\Http\Resources;

use App\Models\Student;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class availableClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //lang
        if($request->header('lang') == 'ar'){
            $lang = 'ar';
        } else{
            $lang = 'en';
        }

        Student::WhereHas('Student_classes', function($query){
            $query->where('available_class_id', $this->id);
        })->get()->map(function ($data) {
            return [
                'id'        => $data->id,
                'username'  => $data->username,
                'image'     => $data->getImage(),
            ];
        });

        $students = Student::WhereHas('Student_classes', function($query){
            $query->where('available_class_id', $this->id);
        })->get();

        //agora response
        if($this->agora_token == null){
            $agora = null;
        } else {
            $agora = [
                'agora_token'       => $this->agora_token,
                'channel_name'      => $this->channel_name,
                'teacher'       => [
                    'id'        => $this->Teacher->id,
                    'username'  => $this->Teacher->username,
                    'image'     => $this->Teacher->getImage(),
                ],
                'students'       => $students->map(function ($data) {
                    return [
                        'id'        => $data->id,
                        'username'  => $data->username,
                        'image'     => $data->getImage(),
                    ];
                }),
            ];
        }

        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->from);
        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now());

        //get time now
        if($this->from > date('Y-m-d H:i:s')){  //date npt come
            //if there are less than 5 minutes for class 
            ($to->diffInMinutes($from) <= 5)? $time_now = 1:  $time_now = 0;
        } else {
            //date already come
            $time_now = 1;
        }

        //if no student booking this class
        if(DB::table('student_class')->where('available_class_id', $this->id)->count() == 0){
            $time_now = 0;
        }

        return [
            'id'                => $this->id,
            'from'              => $this->from,
            'to'                => $this->to,
            'long'              => $this->long,
            'cost'              => $this->cost,
            'student_number'    => count($this->Student_classes),
            'time_now'          => $time_now,
            'agora'             => $agora,
            'year'              =>  [
                                        'id'    => $this->Subject->Term->Year->id,
                                        'name'  => $this->Subject->Term->Year->translate($lang)->name
            ],
            'subject'           => [
                                        'id'    => $this->subject_id,
                                        'name'  => $this->subject->Main_subject->translate($lang)->name
                                    ],
            'teacher'           => [
                                        'id'    => $this->Teacher->id,
                                        'name'  => $this->Teacher->username,
                                        'iamge' => $this->Teacher->getImage(),
                                    ],
            'students'       => $students->map(function ($data) {
                                        return [
                                            'id'        => $data->id,
                                            'username'  => $data->username,
                                            'image'     => $data->getImage(),
                                        ];
                                    }),
        ];
    }
}

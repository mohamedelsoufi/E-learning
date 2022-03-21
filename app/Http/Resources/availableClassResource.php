<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        if($request->header('lang') == 'ar'){
            $lang = 'ar';
        } else{
            $lang = 'en';
        }

        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->from);
        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now());

        ($to->diffInMinutes($from) <= 5)? $time_now = 1:  $time_now = 0;

        return [
            'id'                => $this->id,
            'from'              => $this->from,
            'to'                => $this->to,
            'long'              => $this->long,
            'cost'              => $this->cost,
            'student_number'    => count($this->Student_classes),
            'time_now'          => $time_now,
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
                                        'nmae'  => $this->Teacher->username,
                                        'iamge' => $this->Teacher->getImage(),
                                    ],
        ];
    }
}

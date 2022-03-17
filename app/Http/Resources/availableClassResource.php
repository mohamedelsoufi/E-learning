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
        return [
            'id'                => $this->id,
            'from'              => $this->from,
            'to'                => $this->to,
            'long'              => $this->long,
            'student_number'    => count($this->Student_classes),
            'year'              =>  [
                                        'id'    => $this->Subject->Term->Year->id,
                                        'name'  => $this->Subject->Term->Year->translate($lang)->name
            ],
            'subject'           => [
                                        'id'    => $this->subject_id,
                                        'name'  => $this->subject->Main_subject->translate($lang)->name
                                    ]
        ];
    }
}

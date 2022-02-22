<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class student_classResource extends JsonResource
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
            'from'           => $this->Available_class->from,
            'subject'        => $this->Available_class->Subject->translate($lang)->name,
            'long'           => $this->Available_class->long,
            'cost'           => $this->Available_class->cost,
            'teacher'        => [
                                    'id'        => $this->Available_class->Teacher->id,
                                    'username'  => $this->Available_class->Teacher->username,
                                    'image'     => $this->Available_class->Teacher->getImage(),
                                ],
        ];
    }
}

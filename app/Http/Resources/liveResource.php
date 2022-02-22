<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class liveResource extends JsonResource
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
            'subject'           => $this->Subject->translate($lang)->name,
            'title'             => $this->title,
            'cost'              => $this->cost,
            'from'              => $this->from,
            'teacher'           => [
                                        'id'        => $this->Teacher->id,
                                        'username'  => $this->Teacher->username,
                                        'image'     => $this->Teacher->getImage(),
                                    ],
        ];
    }
}

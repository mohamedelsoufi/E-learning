<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class teacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'email'         => $this->email,
            'phone'         => [
                                'dialing_code'  =>$this->dialing_code,
                                'phone'         =>$this->phone,
                            ],
            'country'       => $this->Country->name,
            'curriculum'    => $this->getCurriculum(),
            'balance'       => $this->balance,
            'birth'         => $this->birth,
            'about'         => $this->about,
            'class_count'   => count($this->Available_classes),
            'gender'        => $this->getGender(),
            'rating'        => $this->getRating(),
            'tags'          => tagResource::collection($this->Tags),
            'image'         => $this->getImage(),
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Class_type;
use Illuminate\Http\Resources\Json\JsonResource;

class teacher_classesTypeResourc extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $request->teacher_id = $this->id;
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
            'class_count'   => count($this->Available_classes->where('to', '>', date('Y-m-d H:i:s'))),
            'gender'        => $this->getGender(),
            'rating'        => $this->getRating(),
            'image'         => $this->getImage(),
            // 'classes_type'  => classTypeResource::collection(Class_type::active()->get())
        ];
    }
}

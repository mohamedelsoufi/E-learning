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
        if($request->header('lang') == 'ar'){
            $lang = 'ar';
        } else{
            $lang = 'en';
        }

        return [
            'id'            => $this->id,
            'username'      => $this->username,
            'email'         => $this->email,
            'phone'         => [
                                'dialing_code'  =>$this->dialing_code,
                                'phone'         =>$this->phone,
                            ],
            'country'       => [
                                'id'   => $this->country_id,
                                'name' => $this->Country->translate($lang)->name,
                            ],
            'curriculum'    => [
                'id'   => $this->curriculum_id,
                'name' => $this->getCurriculum($lang),
            ],
            'balance'       => $this->balance,
            'birth'         => $this->birth,
            'about'         => $this->about,
            'class_count'   => count($this->Available_classes->where('to', '<', date('Y-m-d H:i:s'))),
            'gender'        => $this->getGender(),
            'rating'        => $this->getRating(),
            'image'         => $this->getImage(),
            'subjects'      => $this->Subject_teachers->map(function ($data) use($lang){
                                    return  [
                                            'id'    => $data->subject_id,
                                            'name'  => $data->Subject->translate($lang)->name
                                        ];
                                }),
            'videos'        => $this->Videos->map(function($data){
                                    return [
                                        'id'        => $data->id,
                                        'title'     => $data->title,
                                        'src'       => $data->src,
                                    ];
                                }),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class studentResource extends JsonResource
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
            'email'         =>$this->email,
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
            'year'          => [
                                    'id'   => $this->year_id,
                                    'name' => $this->getYear($lang),
                                ],
            'balance'       => $this->balance,
            'birth'         => $this->birth,
            'free_classes'  =>$this->free,
            'gender'        => $this->getGender(),
            'gender_boolean'=> $this->gender,
            'image'         => $this->getImage(),
        ];
    }
}

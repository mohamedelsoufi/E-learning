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
            'email'         =>$this->email,
            'phone'         => [
                                'dialing_code'  =>$this->dialing_code,
                                'phone'         =>$this->phone,
                            ],
            'country'       => $this->Country->name,
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
            'gender'        => $this->getGender(),
            'image'         => $this->getImage(),
        ];
    }
}

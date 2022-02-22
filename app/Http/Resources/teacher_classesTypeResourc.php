<?php

namespace App\Http\Resources;

use App\Http\Controllers\site\student\home;
use App\Models\Class_type;
use App\Models\Subject;
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
        $subject = Subject::find($request->get('subject_id'));

        return [
            'id'            => $this->id,
            'username'      => $this->username,
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
            'classes_type'  => Class_type::active()->get()->map(function($data) use($subject){
                return [
                    'id'        => $data->id,
                    'long'      => $data->long,
                    'cost'      => number_format(home::get_cost($data->id, $this->id, $subject->Term->Year->Level->id), 2),
                ];
            }),
        ];
    }
}

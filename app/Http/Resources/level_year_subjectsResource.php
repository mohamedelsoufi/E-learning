<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class level_year_subjectsResource extends JsonResource
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
            'name'              => $this->translate($lang)->name,
            'curriculum_id'     => $this->curriculum_id,
            'status'            => $this->status,
            'years'             => $this->years->where('status', 1)->map(function ($data) use($lang){
                                        return  [
                                                'id'                => $data->id,
                                                'name'              => $data->translate($lang)->name,
                                                'subjects'          => $data->subjects->where('status', 1)->map(function ($subject) use($lang){
                                                    return  [
                                                            'id'                => $subject->id,
                                                            'name'              => $subject->translate($lang)->name,
                                                            'image'             => $subject->getImage(),
                                                    ];
                                                }),
                                        ];
                                    }),
        ];
    }
}

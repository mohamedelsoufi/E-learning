<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class subjectsResource extends JsonResource
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
            'name'          => $this->translate($lang)->name,
            'term_id'       => $this->term_id,
            'iamge'         => $this->getImage(),
            'status'        => $this->status,
        ];
    }
}

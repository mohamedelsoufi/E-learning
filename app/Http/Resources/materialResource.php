<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class materialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($request->lang == 'ar'){
            $lang = 'ar';
        } else{
            $lang = 'en';
        }

        return [
            'id'                => $this->id,
            'name'              => $this->translate($lang)->name,
            'subject_id'        => $this->subject_id,
            'status'            => $this->status,
            'file'              => url('public/uploads/materials/' . $this->file->src),
        ];
    }
}

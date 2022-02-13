<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class questionsResource extends JsonResource
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
            'student_id'    => $this->student_id,
            'question'      => $this->question,
            'status'        => $this->getStatus(),
            'created_at'    => date("Y-m-d H:i", strtotime($this->created_at)),
        ];
    }
}

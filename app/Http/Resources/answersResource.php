<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class answersResource extends JsonResource
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
            'answer'        => $this->question_id,
            'question_id'   => $this->question_id,
            'status'        => $this->getStatus(),
            'recommendation'=> $this->recommendation,
            'created_at'    => date("Y-m-d H:i", strtotime($this->created_at)),
            'answer_owner'  => [
                'id'    => $this->answerable_id,
                'type'  => $this->getType(),
            ],
        ];
    }
}

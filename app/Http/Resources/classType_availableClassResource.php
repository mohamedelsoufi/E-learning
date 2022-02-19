<?php

namespace App\Http\Resources;

use App\Http\Requests\admin\countries\add;
use App\Models\Available_class;
use App\Models\Teacher;
use Illuminate\Http\Resources\Json\JsonResource;

class classType_availableClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $available_classes = Available_class::where('class_type_id', $this->id)
                                            ->where('teacher_id', $request->get('teacher_id'))
                                            ->where('to', '>', date('Y-m-d H:i:s'))
                                            ->doesntHave('Student_classes')
                                            ->get();
        return [
            'id'                => $this->id,
            'long'              => $this->long,
            'long_cost'         => $this->long_cost,
            'available_classes' => availableClassResource::collection($available_classes),
        ];
    }
}

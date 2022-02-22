<?php

namespace App\Http\Resources;

use App\Http\Controllers\site\student\home;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Resources\Json\JsonResource;

class classTypeResource extends JsonResource
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
            'id'        => $this->id,
            'long'      => $this->long,
            'cost'      => number_format(home::get_cost($this->id, $request->get('teacher_id'), $subject->Term->Year->Level->id), 2),
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    protected $table = 'answers';

    protected $guarded = [];

    protected $casts = [
        'id'                   => 'integer',
        'answerable_id'        => 'integer',
        'question_id'          => 'integer',
        'recommendation'       => 'integer'
    ];

    //relations
    public function Question(){
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function answerable()
    {
        return $this->morphTo();
    }
    //scope
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    //
    public function getStatus(){
        if($this->status == 0){
            return 'not active';
        } else {
            return 'active';
        }
    }

    public function getType(){
        if($this->answerable_type == "App\Models\Student"){
            return 'student';
        } else {
            return 'teacher';
        }
    }
}

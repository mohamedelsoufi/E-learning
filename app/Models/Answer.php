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
        'answerable_type'      => 'integer',
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
}

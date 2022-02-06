<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $table = 'questions';

    protected $guarded = [];

    protected $casts = [
        'id'    => 'integer',
    ];

    //relations
    public function Student(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function Answers(){
        return $this->hasMany(Answer::class, 'question_id');
    }
}

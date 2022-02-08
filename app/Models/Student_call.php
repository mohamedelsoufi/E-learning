<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student_call extends Model
{
    use HasFactory;
    protected $table = 'student_class';

    protected $guarded = [];

    protected $casts = [
        'student_id'            => 'integer',
        'video_call_id'         => 'integer',
    ];

    //relations
    public function Students(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function Video_calls(){
        return $this->belongsTo(Video_call::class, 'video_call_id');
    }
}

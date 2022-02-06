<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Available_class extends Model
{
    use HasFactory;
    protected $table = 'available_classes';

    protected $guarded = [];

    protected $casts = [
        'id'                => 'integer',
        'teacher_id'        => 'integer',
        'subject_id'        => 'integer',
        'class_type_id'     => 'integer',
        'long'              => 'integer',
        'max_student_number'=> 'integer',
        'cost'              => 'float',
        'max_student_number'=> 'integer',


    ];
    //relations
    public function Teacher(){
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function Subject(){
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function Class_type(){
        return $this->belongsTo(Class_type::class, 'class_type_id');
    }

    public function Student_classes(){
        return $this->hasMany(Student_class::class, 'available_class_id');
    }

    public function Video_calls(){
        return $this->hasMany(Video_call::class, 'available_class_id');
    }
}
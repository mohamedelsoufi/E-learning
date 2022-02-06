<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    protected $table = 'subjects';

    protected $guarded = [];

    protected $casts = [
        'id'            => 'integer',
        'term_id'       => 'integer',
        'parent'        => 'integer',
    ];
    
    //relations
    public function Term(){
        return $this->belongsTo(Term::class, 'term_id');
    }

    public function Subject_teachers(){
        return $this->hasMany(Subject_teacher::class, 'subject_id');
    }

    public function Available_classes(){
        return $this->hasMany(Available_class::class, 'subject_id');
    }

    public function Student_classes(){
        return $this->hasMany(Student_class::class, 'student_id');
    }
}

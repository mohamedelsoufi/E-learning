<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class Subject extends Model implements TranslatableContract
{
    use HasFactory, Translatable;
    protected $table = 'subjects';
    public $translatedAttributes = ['name'];
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

    public function Materials(){
        return $this->hasMany(Material::class, 'subject_id');
    }

    public function Student_classes(){
        return $this->hasMany(Student_class::class, 'student_id');
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
}

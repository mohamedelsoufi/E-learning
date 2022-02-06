<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $table = 'teachers';

    protected $guarded = [];

    protected $casts = [
        'id'            => 'integer',
        'country_id'    => 'integer',
        'balance'       => 'float',
        'verified'      => 'integer',
        'status'        => 'integer',
        'online'        => 'integer',
        'gender'        => 'integer',
    ];

    //relations
    public function Country(){
        return $this->belongsTo(Country::class, 'country_id');
    }
    
    public function Subject_teachers(){
        return $this->hasMany(Subject_teacher::class, 'teacher_id');
    }

    public function Available_classes(){
        return $this->hasMany(Available_class::class, 'teacher_id');
    }

    public function images()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function Answer()
    {
        return $this->morphOne(Answer::class, 'answerable');
    }
}

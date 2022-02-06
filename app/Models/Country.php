<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = 'countries';

    protected $guarded = [];

    protected $casts = [
    ];

    //relations
    public function Students(){
        return $this->hasMany(Student::class, 'country_id');
    }

    public function Teachers(){
        return $this->hasMany(Teacher::class, 'country_id');
    }

    public function Curriculums(){
        return $this->hasMany(Curriculum::class, 'country_id');
    }

    public function Class_types(){
        return $this->hasMany(Class_type::class, 'country_id');
    }
}

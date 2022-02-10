<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class Country extends Model implements TranslatableContract
{
    use HasFactory, Translatable;
    protected $table = 'countries';
    public $translatedAttributes = ['name'];

    protected $guarded = [];

    protected $casts = [
        'id'        => 'integer',
        'status'    => 'integer',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;

    protected $table = 'curriculums';

    protected $guarded = [];

    protected $casts = [
        'id'            => 'integer',
        'country_id'    => 'integer',
        'parent'        => 'integer',
        'curriculum_id' => 'integer',
    ];

    //relations
    public function Country(){
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function Curriculums(){
        return $this->hasMany(Curriculum::class, 'curriculum_id');
    }
    
    public function Levels(){
        return $this->hasMany(Level::class, 'curriculum_id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;
    protected $table = 'levels';

    protected $guarded = [];

    protected $casts = [
    ];

    //relations
    public function Curriculum(){
        return $this->belongsTo(Curriculum::class, 'curriculum_id');
    }
    public function Years(){
        return $this->hasMany(Year::class, 'year_id');
    }
}

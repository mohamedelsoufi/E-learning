<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;
    protected $table = 'terms';

    protected $guarded = [];

    protected $casts = [
    ];

    //relations
    public function Year(){
        return $this->belongsTo(Year::class, 'year_id');
    }

    public function Subjects(){
        return $this->hasMany(Subject::class, 'term_id');
    }
}

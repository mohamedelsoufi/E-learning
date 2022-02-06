<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;
    protected $table = 'years';

    protected $guarded = [];

    protected $casts = [
    ];

    //relations
    public function Students(){
        return $this->hasMany(Student::class, 'year_id');
    }

    public function Level(){
        return $this->belongsTo(Level::class, 'year_id');
    }

    public function Terms(){
        return $this->hasMany(Term::class, 'year_id');
    }
}

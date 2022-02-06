<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Class_type extends Model
{
    use HasFactory;
    protected $table = 'class_types';

    protected $guarded = [];

    protected $casts = [
        'id'                    => 'integer',
        'country_id'            => 'integer',
        'cost'                  => 'float',
        'students_number'       => 'integer',
        'company_percentage'    => 'integer',
        'max_students_number'   => 'integer',
        'min_students_number'   => 'integer',
        'long'                  => 'integer',
    ];

    //relations
    public function Country(){
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function Available_classes(){
        return $this->hasMany(Available_class::class, 'class_type_id');
    }
}

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
        'cost'                  => 'float',
        'students_number'       => 'integer',
        'long'                  => 'integer',
    ];

    //relations
    public function Country(){
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function Available_classes(){
        return $this->hasMany(Available_class::class, 'class_type_id');
    }

    public function getStatus(){
        if($this->status == 0){
            return 'not active';
        } else {
            return 'active';
        }
    }
}

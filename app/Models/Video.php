<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    protected $table = 'videos';

    protected $guarded = [];
    protected $casts = [
        'teacher_id'    => 'integer',
    ];

    //scope
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}

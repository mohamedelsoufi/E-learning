<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video_call extends Model
{
    use HasFactory;
    protected $table = 'video_calls';

    protected $guarded = [];

    protected $casts = [
    ];
}

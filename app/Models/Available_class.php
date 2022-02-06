<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Available_class extends Model
{
    use HasFactory;
    protected $table = 'available_classes';

    protected $guarded = [];

    protected $casts = [
    ];
}

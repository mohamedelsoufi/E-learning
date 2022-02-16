<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class materialTranslation extends Model
{
    use HasFactory;
    protected $table = 'materials_translations';
    protected $guarded = [];
    public $timestamps = false;
}

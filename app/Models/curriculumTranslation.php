<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class curriculumTranslation extends Model
{
    use HasFactory;
    protected $table = 'curriculums_translations';
    protected $guarded = [];
    public $timestamps = false;
}

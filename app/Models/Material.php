<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $table = 'materials';

    protected $guarded = [];

    protected $casts = [
        'id'            => 'integer',
    ];

    //relations
    public function Subject(){
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
    //scope
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    //
    public function getStatus(){
        if($this->status == 0){
            return 'not active';
        } else {
            return 'active';
        }
    }
}

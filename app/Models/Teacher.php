<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Teacher extends Authenticatable implements JWTSubject
{
    use HasFactory,Notifiable;
    protected $table = 'teachers';

    protected $guarded = [];

    protected $casts = [
        'id'            => 'integer',
        'country_id'    => 'integer',
        'balance'       => 'float',
        'verified'      => 'integer',
        'status'        => 'integer',
        'online'        => 'integer',
        'gender'        => 'integer',
    ];

    //relations
    public function Country(){
        return $this->belongsTo(Country::class, 'country_id');
    }
    
    public function Subject_teachers(){
        return $this->hasMany(Subject_teacher::class, 'teacher_id');
    }

    public function Available_classes(){
        return $this->hasMany(Available_class::class, 'teacher_id');
    }

    public function Rating(){
        return $this->hasMany(Rating::class, 'teacher_id');
    }

    public function Tags(){
        return $this->hasMany(Tag::class, 'teacher_id');
    }

    public function images()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function Answer()
    {
        return $this->morphOne(Answer::class, 'answerable');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'type'       => 'teacher',
        ];
    }
}

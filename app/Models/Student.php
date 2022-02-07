<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $table = 'students';

    protected $guarded = [];

    protected $casts = [
        'id'            => 'integer',
        'country_id'    => 'integer',
        'year_id'       => 'integer',
        'balance'       => 'float',
        'verified'      => 'integer',
        'online'        => 'integer',
        'gender'        => 'integer',
    ];

    //relations
    public function Country(){
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function Year(){
        return $this->belongsTo(Year::class, 'year_id');
    }

    public function Materials(){
        return $this->hasMany(Material::class, 'subject_id');
    }

    public function Questions(){
        return $this->hasMany(Question::class, 'student_id');
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
            'type'       => 'student'
        ];
    }
}

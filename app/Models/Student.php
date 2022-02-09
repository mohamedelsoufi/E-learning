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

    public function Student_calls(){
        return $this->hasMany(Student_call::class, 'student_id');
    }

    public function Questions(){
        return $this->hasMany(Question::class, 'student_id');
    }
    
    public function Image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function Answer()
    {
        return $this->morphOne(Answer::class, 'answerable');
    }
    //
    public function getGender(){
        if($this->gender == 0){
            return trans('auth.female');
        } else {
            return trans('auth.male');
        }
    }

    public function getYear(){
        if($this->Year != null){
            return $this->Year->name;
        } else {
            return null;
        }
    }

    public function getCurriculum(){
        if($this->Curriculum != null){
            return $this->Curriculum->name;
        } else {
            return null;
        }
    }

    public function getImage(){
        if($this->Image != null){
            return url('public/uploads/students/' . $this->Image->src);
        } else {
            return url('public/uploads/students/default.jpg');
        }
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

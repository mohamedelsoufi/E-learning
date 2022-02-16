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

    public function Curriculum(){
        return $this->belongsTo(Curriculum::class, 'curriculum_id');
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

    public function Image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function Answer()
    {
        return $this->morphMany(Answer::class, 'answerable');
    }
    //scope
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    //
    public function getGender(){
        if($this->gender == 0){
            return trans('auth.female');
        } else {
            return trans('auth.male');
        }
    }

    public function getCurriculum(){
        if($this->Curriculum != null){
            return $this->Curriculum->name;
        } else {
            return null;
        }
    }

    public function getStatus(){
        if($this->status == 0){
            return 'block';
        } else {
            return 'active';
        }
    }

    public function changeStatus(){
        if($this->status == 0){
            return 'active';
        } else {
            return 'block';
        }
    }

    public function getImage(){
        if($this->Image != null){
            return url('public/uploads/teachers/' . $this->Image->src);
        } else {
            return url('public/uploads/teachers/default.jpg');
        }
    }

    public function getRating(){
        if(count($this->Rating) > 0){
            return $this->Rating->sum('stars') / count($this->Rating);
        } else {
            return 0;
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
            'type'       => 'teacher',
        ];
    }
}

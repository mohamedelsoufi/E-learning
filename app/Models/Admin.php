<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;
    protected $table = 'admins';

    protected $guarded = [];

    //
    public function getImage(){
        if($this->Image != null){
            return url('public/uploads/admins/' . $this->Image->src);
        } else {
            return url('public/uploads/admins/default.jpg');
        }
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Available_class;
use Illuminate\Http\Request;

class classes extends Controller
{
    public function index(){
        $classes = Available_class::get();
        return view('admins.classes.index')->with('classes', $classes);
    }
}

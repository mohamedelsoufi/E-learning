<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => ['changeLang'] ,'prefix' => 'guest'], function(){
    Route::get('/level_year', 'App\Http\Controllers\site\guest\search@level_year');

    Route::get('/subjects', 'App\Http\Controllers\site\guest\search@subjectsByTerm');

    //get teacher by subject_id
    Route::get('/teachers', 'App\Http\Controllers\site\guest\home@teachersBysubject');

    Route::get('/materials', 'App\Http\Controllers\site\guest\home@materials');

});



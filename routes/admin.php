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
Route::group(['prefix' => 'admins'], function(){
    Route::get('/', 'App\Http\Controllers\admin\dashbourd@index')->middleware('auth:admin');

    Route::get('/login', 'App\Http\Controllers\admin\authentication@loginView')->name('adminlogin')->middleware('guest:admin');
    Route::post('/login', 'App\Http\Controllers\admin\authentication@login')->middleware('guest:admin');

    Route::get('/logout', 'App\Http\Controllers\admin\authentication@logout')->middleware('auth:admin');

    Route::group(['prefix' => 'admins'],function(){
        Route::get('/', 'App\Http\Controllers\admin\admins@index')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\admins@delete')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\admins@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\admins@create')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\admins@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\admins@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'roles'],function(){
        Route::get('/', 'App\Http\Controllers\admin\roles@index')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\roles@delete')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\roles@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\roles@create')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\roles@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\roles@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'countries'],function(){
        Route::get('/', 'App\Http\Controllers\admin\country@index')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\country@delete')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\country@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\country@create')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\country@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\country@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'curriculums'],function(){
        Route::get('/', 'App\Http\Controllers\admin\curriculums@index')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\curriculums@delete')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\curriculums@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\curriculums@create')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\curriculums@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\curriculums@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'levels'],function(){
        Route::get('/', 'App\Http\Controllers\admin\levels@index')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\levels@delete')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\levels@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\levels@create')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\levels@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\levels@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'years'],function(){
        Route::get('/', 'App\Http\Controllers\admin\years@index')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\years@delete')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\years@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\years@create')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\years@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\years@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'terms'],function(){
        Route::get('/', 'App\Http\Controllers\admin\terms@index')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\terms@delete')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\terms@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\terms@create')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\terms@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\terms@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'terms'],function(){
        Route::get('/', 'App\Http\Controllers\admin\terms@index')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\terms@delete')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\terms@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\terms@create')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\terms@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\terms@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'subjects'],function(){
        Route::get('/', 'App\Http\Controllers\admin\subjects@index')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\subjects@delete')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\subjects@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\subjects@create')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\subjects@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\subjects@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'materials'],function(){
        Route::get('/', 'App\Http\Controllers\admin\materials@index')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\materials@delete')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\materials@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\materials@create')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\materials@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\materials@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'promo_codes'],function(){
        Route::get('/', 'App\Http\Controllers\admin\promo_codes@index')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\promo_codes@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\promo_codes@create')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\promo_codes@delete')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\promo_codes@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\promo_codes@edit')->middleware('auth:admin');
    });
    
    Route::group(['prefix' => 'questions'],function(){
        Route::get('/', 'App\Http\Controllers\admin\questions@index')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\questions@delete')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'answers'],function(){
        Route::get('/', 'App\Http\Controllers\admin\answers@index')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\answers@delete')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'students'],function(){
        Route::get('/', 'App\Http\Controllers\admin\students@index')->middleware('auth:admin');
        Route::get('/block/{id}', 'App\Http\Controllers\admin\students@block')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'teachers'],function(){
        Route::get('/', 'App\Http\Controllers\admin\teachers@index')->middleware('auth:admin');
        Route::get('/block/{id}', 'App\Http\Controllers\admin\teachers@block')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'levels_cost'],function(){
        Route::get('/', 'App\Http\Controllers\admin\levels_cost@index')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\levels_cost@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\levels_cost@create')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\levels_cost@delete')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\levels_cost@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\levels_cost@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'countries_cost'],function(){
        Route::get('/', 'App\Http\Controllers\admin\countries_cost@index')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\countries_cost@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\countries_cost@create')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\countries_cost@delete')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\countries_cost@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\countries_cost@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'company_percentages'],function(){
        Route::get('/', 'App\Http\Controllers\admin\company_percentages@index')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\company_percentages@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\company_percentages@create')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\company_percentages@delete')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\company_percentages@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\company_percentages@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'students_numbers_cost'],function(){
        Route::get('/', 'App\Http\Controllers\admin\students_numbers_cost@index')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\students_numbers_cost@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\students_numbers_cost@create')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\students_numbers_cost@delete')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\students_numbers_cost@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\students_numbers_cost@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'settings'],function(){
        Route::get('/edit', 'App\Http\Controllers\admin\settings@editView')->middleware('auth:admin');
        Route::post('/edit', 'App\Http\Controllers\admin\settings@edit')->middleware('auth:admin');
    });

    Route::group(['prefix' => 'class_types'],function(){
        Route::get('/', 'App\Http\Controllers\admin\class_types@index')->middleware('auth:admin');
        Route::get('/create', 'App\Http\Controllers\admin\class_types@createView')->middleware('auth:admin');
        Route::post('/create', 'App\Http\Controllers\admin\class_types@create')->middleware('auth:admin');
        Route::get('/delete/{id}', 'App\Http\Controllers\admin\class_types@delete')->middleware('auth:admin');
        Route::get('/edit/{id}', 'App\Http\Controllers\admin\class_types@editView')->middleware('auth:admin');
        Route::post('/edit/{id}', 'App\Http\Controllers\admin\class_types@edit')->middleware('auth:admin');
    });
});



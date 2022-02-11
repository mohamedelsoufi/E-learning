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

});



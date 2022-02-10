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

    Route::get('/logout', 'App\Http\Controllers\admin\authentication@logout')->middleware('auth:admin');
});



<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
date_default_timezone_set('Africa/cairo');

Route::group(['middleware' => ['changeLang'] ,'prefix' => 'teachers'], function(){
    Route::post('login', 'App\Http\Controllers\site\teacher\authentication\auth@login');
    Route::post('register', 'App\Http\Controllers\site\teacher\authentication\auth@register');

    Route::group(['prefix' => 'passwordReset'], function(){
        Route::post('/', 'App\Http\Controllers\site\teacher\authentication\resetPasswored@passwordResetProcess')->middleware('checkJWTToken:teacher');
        Route::post('checkCode', 'App\Http\Controllers\site\teacher\authentication\resetPasswored@checkCode');
        Route::post('sendCode', 'App\Http\Controllers\site\teacher\authentication\resetPasswored@sendCode');
    });

    Route::get('questions', 'App\Http\Controllers\site\teacher\questions@index');
    Route::get('answers', 'App\Http\Controllers\site\teacher\answers@index');
    
    //auth
    Route::group(['middleware' => 'checkJWTToken:teacher'], function(){
        Route::group(['prefix' => 'profile'], function(){
            Route::post('/', 'App\Http\Controllers\site\teacher\authentication\profile@myProfile');
            Route::post('changePassword', 'App\Http\Controllers\site\teacher\authentication\profile@changePassword');
            Route::post('changeImage', 'App\Http\Controllers\site\teacher\authentication\profile@change_image');
            Route::post('update', 'App\Http\Controllers\site\teacher\authentication\profile@updateProfile');
            Route::post('setup_profile', 'App\Http\Controllers\site\teacher\authentication\profile@setup_profile');
        });

        Route::group(['prefix' => 'verification'], function(){
            Route::post('/', 'App\Http\Controllers\site\teacher\authentication\verification@verificationProcess');
            Route::post('sendCode', 'App\Http\Controllers\site\teacher\authentication\verification@sendCode');
        });

        Route::group(['prefix' => 'answers'], function(){
            Route::post('/create', 'App\Http\Controllers\site\teacher\answers@create');
            Route::post('/delete', 'App\Http\Controllers\site\teacher\answers@delete');
            Route::post('/edit', 'App\Http\Controllers\site\teacher\answers@update');
        });
        //pages
        Route::group(['prefix' => 'schedules'], function(){
            Route::get('/', 'App\Http\Controllers\site\teacher\home@schedule');
            Route::post('add', 'App\Http\Controllers\site\teacher\home@add_schedule');
            Route::post('cancel', 'App\Http\Controllers\site\teacher\home@cancel_schedule');
        });

        Route::group(['prefix' => 'videos'], function(){
            Route::get('/', 'App\Http\Controllers\site\teacher\home@videos');
            Route::post('add', 'App\Http\Controllers\site\teacher\home@add_video');
            Route::post('cancel', 'App\Http\Controllers\site\teacher\home@cancel_video');
        });

        Route::post('logout', 'App\Http\Controllers\site\teacher\authentication\auth@logout');
    });
});

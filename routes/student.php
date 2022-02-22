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

Route::group(['middleware' => ['changeLang'] ,'prefix' => 'students'], function(){
    Route::post('login', 'App\Http\Controllers\site\student\authentication\auth@login');
    Route::post('register', 'App\Http\Controllers\site\student\authentication\auth@register');

    Route::group(['prefix' => 'verification'], function(){
        Route::post('/', 'App\Http\Controllers\site\student\authentication\verification@verificationProcess');
        Route::post('sendCode', 'App\Http\Controllers\site\student\authentication\verification@sendCode');
    });

    Route::group(['prefix' => 'passwordReset'], function(){
        Route::post('/', 'App\Http\Controllers\site\student\authentication\resetPasswored@passwordResetProcess');
        Route::post('checkCode', 'App\Http\Controllers\site\student\authentication\resetPasswored@checkCode');
        Route::post('sendCode', 'App\Http\Controllers\site\student\authentication\resetPasswored@sendCode');
    });
    

    Route::get('questions', 'App\Http\Controllers\site\student\questions@index');
    Route::get('answers', 'App\Http\Controllers\site\student\answers@index');

    Route::group(['middleware' => 'checkJWTToken:student'], function(){
        Route::get('/home', 'App\Http\Controllers\site\student\home@index');

        Route::group(['prefix' => 'myProfile'], function(){
            Route::get('/', 'App\Http\Controllers\site\student\authentication\profile@myProfile');
            Route::post('changePassword', 'App\Http\Controllers\site\student\authentication\profile@changePassword');
            Route::post('changeImage', 'App\Http\Controllers\site\student\authentication\profile@change_image');
            Route::post('update', 'App\Http\Controllers\site\student\authentication\profile@updateProfile');
        });

        Route::group(['prefix' => 'questions'], function(){
            Route::post('/create', 'App\Http\Controllers\site\student\questions@create');
            Route::post('/delete', 'App\Http\Controllers\site\student\questions@delete');
            Route::post('/edit', 'App\Http\Controllers\site\student\questions@update');
        });

        Route::group(['prefix' => 'answers'], function(){
            Route::post('/create', 'App\Http\Controllers\site\student\answers@create');
            Route::post('/delete', 'App\Http\Controllers\site\student\answers@delete');
            Route::post('/edit', 'App\Http\Controllers\site\student\answers@update');
        });

        Route::post('/reservations', 'App\Http\Controllers\site\student\home@my_reservations');

        Route::post('/available_classes', 'App\Http\Controllers\site\student\home@available_classes');
        Route::post('leave', 'App\Http\Controllers\site\student\home@leave');

        Route::get('/lives', 'App\Http\Controllers\site\student\lives@lives');

        Route::post('logout', 'App\Http\Controllers\site\student\authentication\auth@logout');
    });
});
Route::get('test', 'App\Http\Controllers\site\student\home@test');






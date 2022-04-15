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

Route::group(['middleware' => ['changeLang'] ,'prefix' => 'students'], function(){
    Route::get('/', 'App\Http\Controllers\Controller@test');
    Route::post('login', 'App\Http\Controllers\site\student\authentication\auth@login');
    Route::post('register', 'App\Http\Controllers\site\student\authentication\auth@register');

    Route::group(['prefix' => 'passwordReset'], function(){
        Route::post('/', 'App\Http\Controllers\site\student\authentication\resetPasswored@passwordResetProcess')->middleware('checkJWTToken:student');
        Route::post('checkCode', 'App\Http\Controllers\site\student\authentication\resetPasswored@checkCode');
        Route::post('sendCode', 'App\Http\Controllers\site\student\authentication\resetPasswored@sendCode');
    });

    Route::get('/offers', 'App\Http\Controllers\site\student\offers@index');

    Route::group(['middleware' => 'checkJWTToken:student'], function(){
        Route::group(['prefix' => 'verification'], function(){
            Route::post('/', 'App\Http\Controllers\site\student\authentication\verification@verificationProcess');
            Route::post('sendCode', 'App\Http\Controllers\site\student\authentication\verification@sendCode');
        });

        Route::group(['prefix' => 'myProfile'], function(){
            Route::get('/', 'App\Http\Controllers\site\student\authentication\profile@myProfile');
            Route::post('/setup_profile', 'App\Http\Controllers\site\student\authentication\profile@updateYear');
            Route::post('changePassword', 'App\Http\Controllers\site\student\authentication\profile@changePassword');
            Route::post('changeImage', 'App\Http\Controllers\site\student\authentication\profile@change_image');
            Route::post('update', 'App\Http\Controllers\site\student\authentication\profile@updateProfile');
        });

        Route::group(['prefix' => 'questions'], function(){
            Route::get('/', 'App\Http\Controllers\site\student\questions@index');
            Route::get('/my-question', 'App\Http\Controllers\site\student\questions@myQuestion');
            Route::post('/create', 'App\Http\Controllers\site\student\questions@create');
            Route::post('/delete', 'App\Http\Controllers\site\student\questions@delete');
            Route::post('/edit', 'App\Http\Controllers\site\student\questions@update');
        });

        Route::group(['prefix' => 'answers'], function(){
            Route::get('/', 'App\Http\Controllers\site\student\answers@index');
            Route::post('/create', 'App\Http\Controllers\site\student\answers@create');
            Route::post('/delete', 'App\Http\Controllers\site\student\answers@delete');
            Route::post('/edit', 'App\Http\Controllers\site\student\answers@update');
        });

        Route::group(['prefix' => 'offers'], function(){
            Route::post('/take', 'App\Http\Controllers\site\student\offers@take_offer');;
        });

        Route::group(['prefix' => 'notifications'], function(){
            Route::get('/', 'App\Http\Controllers\site\student\notificaitons@index');
            Route::get('/notifications-count', 'App\Http\Controllers\site\student\notificaitons@notification_count');
        });

        Route::group(['prefix' => 'schedules'], function(){
            Route::get('/', 'App\Http\Controllers\site\student\home@schedule');
            Route::post('/cancel', 'App\Http\Controllers\site\student\home@cancel_schedule');
        });

        Route::get('/home', 'App\Http\Controllers\site\student\home@index');

        Route::post('/reservations', 'App\Http\Controllers\site\student\home@my_reservations');

        Route::post('/available_classes', 'App\Http\Controllers\site\student\home@available_classes');
        Route::post('/booking', 'App\Http\Controllers\site\student\home@booking');
        Route::post('/buy/video', 'App\Http\Controllers\site\student\home@buy_video');

        Route::post('leave', 'App\Http\Controllers\site\student\home@leave');

        Route::get('/lives', 'App\Http\Controllers\site\student\lives@lives');

        Route::post('logout', 'App\Http\Controllers\site\student\authentication\auth@logout');
    });
});
Route::get('test', 'App\Http\Controllers\site\student\home@test');

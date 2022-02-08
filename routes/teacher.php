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
Route::group(['middleware' => ['changeLang'] ,'prefix' => 'teachers'], function(){
    Route::post('login', 'App\Http\Controllers\site\teacher\authentication\auth@login');
    Route::post('register', 'App\Http\Controllers\site\teacher\authentication\auth@register');

    Route::group(['prefix' => 'verification'], function(){
        Route::post('/', 'App\Http\Controllers\site\teacher\authentication\verification@verificationProcess');
        Route::post('sendCode', 'App\Http\Controllers\site\teacher\authentication\verification@sendCode');
    });

    Route::group(['prefix' => 'passwordReset'], function(){
        Route::post('/', 'App\Http\Controllers\site\teacher\authentication\resetPasswored@passwordResetProcess');
        Route::post('checkCode', 'App\Http\Controllers\site\teacher\authentication\resetPasswored@checkCode');
        Route::post('sendCode', 'App\Http\Controllers\site\teacher\authentication\resetPasswored@sendCode');
    });

    Route::group(['middleware' => 'checkJWTToken:teacher'], function(){
        Route::post('logout', 'App\Http\Controllers\site\teacher\authentication\auth@logout');
    });
});

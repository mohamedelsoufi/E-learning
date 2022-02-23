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

    Route::group(['prefix' => 'passwordReset'], function(){
        Route::post('/', 'App\Http\Controllers\site\teacher\authentication\resetPasswored@passwordResetProcess')->middleware('checkJWTToken:teacher');
        Route::post('checkCode', 'App\Http\Controllers\site\teacher\authentication\resetPasswored@checkCode');
        Route::post('sendCode', 'App\Http\Controllers\site\teacher\authentication\resetPasswored@sendCode');
    });

    Route::get('questions', 'App\Http\Controllers\site\teacher\questions@index');
    Route::get('answers', 'App\Http\Controllers\site\teacher\answers@index');
    
    //auth
    Route::group(['middleware' => 'checkJWTToken:teacher'], function(){
        Route::post('myProfile', 'App\Http\Controllers\site\teacher\authentication\profile@myProfile');
        Route::post('myProfile/changePassword', 'App\Http\Controllers\site\teacher\authentication\profile@changePassword');
        Route::post('myProfile/changeImage', 'App\Http\Controllers\site\teacher\authentication\profile@change_image');
        Route::post('myProfile/update', 'App\Http\Controllers\site\teacher\authentication\profile@updateProfile');
        Route::post('myProfile/update/subjects', 'App\Http\Controllers\site\teacher\authentication\profile@update_subjects');

        Route::group(['prefix' => 'verification'], function(){
            Route::post('/', 'App\Http\Controllers\site\teacher\authentication\verification@verificationProcess');
            Route::post('sendCode', 'App\Http\Controllers\site\teacher\authentication\verification@sendCode');
        });

        Route::group(['prefix' => 'answers'], function(){
            Route::post('/create', 'App\Http\Controllers\site\teacher\answers@create');
            Route::post('/delete', 'App\Http\Controllers\site\teacher\answers@delete');
            Route::post('/edit', 'App\Http\Controllers\site\teacher\answers@update');
        });
        Route::post('logout', 'App\Http\Controllers\site\teacher\authentication\auth@logout');
    });
});

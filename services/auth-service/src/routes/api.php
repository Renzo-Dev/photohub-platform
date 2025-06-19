<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', 'AuthController@login');
    Route::post('login', 'AuthController@register');

    Route::middleware('auth:api')->group(function () {
        Route::get('me', 'AuthController@me');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('logout', 'AuthController@logout');
        Route::post('change-password', 'AuthController@changePassword');
    });
});

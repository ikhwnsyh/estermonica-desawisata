<?php

use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_USER])->group(function () {
    Route::get("get", "UserController@get");
    Route::get("logout", "UserController@logout");
});
Route::post("register", "UserController@register");
Route::post("login", "UserController@login");
Route::post("forgot-password/send", "UserController@sendForgotPassword");
Route::post("forgot-password/change", "UserController@changeForgotPassword");

<?php

use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_ADMIN])->group(function () {
    Route::get("self", "AdminController@self");
    Route::get("get", "AdminController@get");
    Route::post("register", "AdminController@register");
    Route::post("edit", "AdminController@edit");
    Route::delete("delete/{id}", "AdminController@delete");
    Route::get("logout", "AdminController@logout");
});
Route::post("login", "AdminController@login");

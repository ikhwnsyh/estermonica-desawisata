<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "ChatController@get");
Route::get("get/detail/{id}", "ChatController@getDetail");
Route::post("add", "ChatController@add");

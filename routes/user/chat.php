<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "ChatController@get");
Route::post("add", "ChatController@add");

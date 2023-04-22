<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "CommentController@get");
Route::post("add", "CommentController@add");

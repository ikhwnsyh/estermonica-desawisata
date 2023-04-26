<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "TransactionController@get");
Route::get("check/in/{id}", "TransactionController@checkIn");
Route::get("check/out/{id}", "TransactionController@checkOut");

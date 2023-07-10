<?php

use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::get("get", "TicketController@get");
Route::get("get/bundle", "TicketController@getBundle");
Route::get("get/non-bundle", "TicketController@getNonBundle");
Route::get("get/detail/{id}", "TicketController@getDetail");
Route::post("update", "TicketController@update");
Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_USER])->post("buy", "TicketController@buy");

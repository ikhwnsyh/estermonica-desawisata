<?php

use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::get("get", "TicketController@get");
Route::get("get/detail/{id}", "TicketController@getDetail");
Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_USER])->post("buy", "TicketController@buy");
Route::post("update", "TicketController@update");

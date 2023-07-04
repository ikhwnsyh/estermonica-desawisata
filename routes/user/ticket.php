<?php

use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::get("get/bundle", "TicketController@getBundle");
Route::get("get/non-bundle", "TicketController@getNonBundle");
Route::get("get/bundle/detail/{id}", "TicketController@getDetailBundle");
Route::get("get/non-bundle/detail/{id}", "TicketController@getDetailNonBundle");
Route::post("update", "TicketController@update");
Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_USER])
    ->prefix("buy")->group(function () {
    Route::post("bundle", "TicketController@buyBundle");
    Route::post("non-bundle", "TicketController@buyNonBundle");
});

<?php

use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_USER])->group(function () {
    Route::get("get", "TransactionController@get");
    Route::get("active", "TransactionController@active");
});
Route::post("update", "TransactionController@update");

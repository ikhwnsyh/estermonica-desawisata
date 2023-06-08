<?php

use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_USER])->get("get", "TransactionController@get");
Route::post("update", "TransactionController@update");

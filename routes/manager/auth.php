<?php

use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_MANAGER])->get("logout", "ManagerController@logout");
Route::post("login", "ManagerController@login");

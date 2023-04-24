<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "TicketBundleController@get");
Route::get("get/list", "TicketBundleController@getList");
Route::post("add", "TicketBundleController@add");
Route::post("edit", "TicketBundleController@edit");
Route::delete("delete/{id}", "TicketBundleController@delete");

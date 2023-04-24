<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "TicketController@get");
Route::post("add", "TicketController@add");
Route::post("edit", "TicketController@edit");
Route::delete("delete/{id}", "TicketController@delete");

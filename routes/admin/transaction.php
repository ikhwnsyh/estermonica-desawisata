<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "TransactionController@get");
Route::get("get/chart/visitor", "TransactionController@getVisitor");
Route::get("get/chart/income", "TransactionController@getIncome");

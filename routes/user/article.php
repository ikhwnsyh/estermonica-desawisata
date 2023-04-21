<?php

use Illuminate\Support\Facades\Route;

Route::get("get/home", "ArticleController@getHome");
Route::get("get/tour", "ArticleController@getTour");
Route::get("get/detail/{id}", "ArticleController@getDetail");

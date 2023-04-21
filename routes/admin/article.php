<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "ArticleController@get");
Route::post("add", "ArticleController@add");
Route::post("edit", "ArticleController@edit");
Route::delete("delete/{id}", "ArticleController@delete");

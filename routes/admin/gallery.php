<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "GalleryController@get");
Route::post("add", "GalleryController@add");
Route::delete("delete/{id}", "GalleryController@delete");

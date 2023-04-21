<?php

use App\Constants\ApiConstant;
use Illuminate\Support\Facades\Route;

Route::prefix(ApiConstant::PREFIX_AUTH)->namespace(ucfirst(ApiConstant::PREFIX_AUTH))->group(__DIR__ . "/" . ApiConstant::PREFIX_AUTH . ".php");
Route::namespace(ucfirst(ApiConstant::PREFIX_USER))->group(function () {
    Route::prefix(ApiConstant::PREFIX_GALLERY)->group(__DIR__ . "/" . ApiConstant::PREFIX_GALLERY . ".php");
    Route::prefix(ApiConstant::PREFIX_ARTICLE)->group(__DIR__ . "/" . ApiConstant::PREFIX_ARTICLE . ".php");
});

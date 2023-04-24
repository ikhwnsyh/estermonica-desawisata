<?php

use App\Constants\ApiConstant;
use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::prefix(ApiConstant::PREFIX_AUTH)->namespace(ucfirst(ApiConstant::PREFIX_AUTH))->group(__DIR__ . "/" . ApiConstant::PREFIX_AUTH . ".php");
Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_ADMIN])
    ->namespace(ucfirst(ApiConstant::PREFIX_ADMIN))
    ->group(function () {
    Route::prefix(ApiConstant::PREFIX_GALLERY)->group(__DIR__ . "/" . ApiConstant::PREFIX_GALLERY . ".php");
    Route::prefix(ApiConstant::PREFIX_ARTICLE)->group(__DIR__ . "/" . ApiConstant::PREFIX_ARTICLE . ".php");
    Route::prefix(ApiConstant::PREFIX_TICKET_BUNDLE)->group(__DIR__ . "/" . ApiConstant::PREFIX_TICKET_BUNDLE . ".php");
    Route::prefix(ApiConstant::PREFIX_TICKET)->group(__DIR__ . "/" . ApiConstant::PREFIX_TICKET . ".php");
});

<?php

use App\Constants\ApiConstant;
use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::prefix(ApiConstant::PREFIX_AUTH)->namespace(ucfirst(ApiConstant::PREFIX_AUTH))->group(__DIR__ . "/" . ApiConstant::PREFIX_AUTH . ".php");
Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_MANAGER])
    ->namespace(ucfirst(ApiConstant::PREFIX_MANAGER))
    ->group(function () {
        Route::prefix(ApiConstant::PREFIX_TRANSACTION)->group(__DIR__ . "/" . ApiConstant::PREFIX_TRANSACTION . ".php");
    });

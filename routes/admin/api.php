<?php

use App\Constants\ApiConstant;
use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::prefix(ApiConstant::PREFIX_AUTH)->namespace(ucfirst(ApiConstant::PREFIX_AUTH))->group(__DIR__ . "/" . ApiConstant::PREFIX_AUTH . ".php");
Route::prefix(ApiConstant::PREFIX_GALLERY)
    ->namespace(ucfirst(ApiConstant::PREFIX_ADMIN))
    ->middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_ADMIN])
    ->group(__DIR__ . "/" . ApiConstant::PREFIX_GALLERY . ".php");

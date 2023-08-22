<?php

use App\Constants\ApiConstant;
use App\Constants\TokenConstant;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\User\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix(ApiConstant::VERSION)->namespace("App\Http\Controllers")->group(function () {
    Route::prefix(ApiConstant::PREFIX_ADMIN)->group(__DIR__ . "/" . ApiConstant::PREFIX_ADMIN . "/api.php");
    Route::prefix(ApiConstant::PREFIX_MANAGER)->group(__DIR__ . "/" . ApiConstant::PREFIX_MANAGER . "/api.php");
    Route::prefix(ApiConstant::PREFIX_USER)->group(__DIR__ . "/" . ApiConstant::PREFIX_USER . "/api.php");
});

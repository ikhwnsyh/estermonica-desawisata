<?php

namespace App\Http\Controllers\User;

use App\Constants\ArticleTypeConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\ArticleModel;
use Illuminate\Http\Request;

class ArticleController extends Controller {
    public function getHome(Request $request) {
        $articles = ArticleModel::where("type", ArticleTypeConstant::HOME)->orderByDesc("id")->get();

        return ResponseHelper::response($articles);
    }

    public function getTour(Request $request) {
        $articles = ArticleModel::where("type", ArticleTypeConstant::TOUR)->orderByDesc("id")->paginate();

        return ResponseHelper::response($articles);
    }
}

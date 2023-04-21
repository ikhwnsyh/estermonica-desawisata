<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ArticleTypeConstant;
use App\Helpers\ResponseHelper;
use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\ArticleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ArticleController extends Controller {
    protected $articleTable;

    public function __construct() {
        $this->articleTable = (new ArticleModel())->getTable();
    }

    public function get(Request $request) {
        $articles = ArticleModel::orderByDesc("id")->paginate();

        return ResponseHelper::response($articles);
    }

    public function set(Request $request, ArticleModel $article) {
        $article->title = $request->title;
        $article->description = $request->description;
        if ($request->hasFile("image")) $article->image = StorageHelper::save($request, "image", "articles");
        $article->type = $request->type;
        $article->save();

        return ResponseHelper::response($article);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "title" => "required|string",
            "description" => "required|string",
            "image" => "required|file|image",
            "type" => ["required", Rule::in([ArticleTypeConstant::HOME, ArticleTypeConstant::TOUR])]
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, new ArticleModel());
    }

    public function edit(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->articleTable,id",
            "title" => "required|string",
            "description" => "required|string",
            "image" => "required",
            "type" => ["required", Rule::in([ArticleTypeConstant::HOME, ArticleTypeConstant::TOUR])]
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, ArticleModel::find($request->id));
    }

    public function delete(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->articleTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        ArticleModel::find($id)->delete();

        return ResponseHelper::response();
    }
}

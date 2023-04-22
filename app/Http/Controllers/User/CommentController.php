<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\CommentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller {
    public function get(Request $request) {
        $comments = CommentModel::with("user")->orderByDesc("id")->paginate();

        return ResponseHelper::response($comments);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "comment" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $comment = CommentModel::create([
            "user_id" => auth()->id(),
            "comment" => $request->comment
        ]);

        return ResponseHelper::response($comment);
    }
}

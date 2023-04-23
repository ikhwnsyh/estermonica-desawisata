<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\ChatModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller {
    protected $chatTable, $userTable;

    public function __construct() {
        $this->chatTable = (new ChatModel())->getTable();
        $this->userTable = (new UserModel())->getTable();
    }

    public function get(Request $request) {
        $chats = UserModel::orderByDesc("id")->paginate();

        return ResponseHelper::response($chats);
    }

    public function getDetail(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->userTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $chats = ChatModel::with(["user", "admin"])->where("user_id", $id)->orderByDesc("id")->paginate();

        return ResponseHelper::response($chats);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric|exists:$this->userTable,id",
            "message" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $chat = ChatModel::create([
            "user_id" => $request->user_id,
            "admin_id" => auth()->id(),
            "message" => $request->message
        ]);

        return ResponseHelper::response($chat);
    }
}

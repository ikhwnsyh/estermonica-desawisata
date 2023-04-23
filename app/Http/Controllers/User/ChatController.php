<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\ChatModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller {
    protected $chatTable;

    public function __construct() {
        $this->chatTable = (new ChatModel())->getTable();
    }

    public function get(Request $request) {
        $chats = ChatModel::with(["user", "admin"])
            ->where("user_id", auth()->id())
            ->orderByDesc("id")
            ->paginate();

        return ResponseHelper::response($chats);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "message" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $chat = ChatModel::create([
            "user_id" => auth()->id(),
            "message" => $request->message
        ]);

        return ResponseHelper::response($chat);
    }
}

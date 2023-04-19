<?php

namespace App\Http\Controllers\Auth;

use App\Constants\AdminTypeConstant;
use App\Constants\TokenConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\AdminModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ManagerController extends Controller {
    protected $adminTable;

    public function __construct() {
        $this->adminTable = (new AdminModel())->getTable();
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            "email" => "required|string|email|exists:$this->adminTable,email",
            "password" => "required|string|min:8",
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $user = AdminModel::where("email", $request->email)->where("type", AdminTypeConstant::MANAGER)->first();

        if (empty($user->id) || !Hash::check($request->password, $user->password)) return ResponseHelper::response(null, "The provided credentials are incorrect.", 401);

        return ResponseHelper::response([
            "user" => $user,
            "token" => $user->createToken(TokenConstant::TOKEN_NAME, [TokenConstant::AUTH_MANAGER])->plainTextToken
        ]);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return ResponseHelper::response();
    }
}

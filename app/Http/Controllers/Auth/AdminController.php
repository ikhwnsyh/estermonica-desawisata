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
use Illuminate\Validation\Rule;

class AdminController extends Controller {
    protected $adminTable;

    public function __construct() {
        $this->adminTable = (new AdminModel())->getTable();
    }

    public function self(Request $request) {
        return ResponseHelper::response($request->user());
    }

    public function get(Request $request) {
        $admins = AdminModel::orderByDesc("id")->paginate();

        return ResponseHelper::response($admins);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "email" => "required|string|email|unique:$this->adminTable,email",
            "password" => "required|string|min:8",
            "confirm_password" => "required|string|min:8|same:password",
            "type" => ["required", "numeric", Rule::in([AdminTypeConstant::ADMINISTRATOR, AdminTypeConstant::MANAGER])]
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $user = AdminModel::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "type" => $request->type
        ]);

        return ResponseHelper::response([
            "user" => $user,
            "token" => $user->createToken(TokenConstant::TOKEN_NAME, [TokenConstant::AUTH_ADMIN])->plainTextToken
        ]);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            "email" => "required|string|email|exists:$this->adminTable,email",
            "password" => "required|string|min:8",
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $user = AdminModel::where("email", $request->email)->where("type", AdminTypeConstant::ADMINISTRATOR)->first();

        if (!Hash::check($request->password, $user->password)) return ResponseHelper::response(null, "The provided credentials are incorrect.", 401);

        return ResponseHelper::response([
            "user" => $user,
            "token" => $user->createToken(TokenConstant::TOKEN_NAME, [TokenConstant::AUTH_ADMIN])->plainTextToken
        ]);
    }

    public function edit(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->adminTable,id",
            "name" => "required|string",
            "email" => "required|string|email",
            "type" => ["required", "numeric", Rule::in([AdminTypeConstant::ADMINISTRATOR, AdminTypeConstant::MANAGER])]
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $user = AdminModel::find($request->id);
        if ($user->email !== $request->email) {
            $validator = Validator::make($request->all(), [
                "email" => "unique:$this->adminTable,email"
            ]);
            if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);
        }
        if (!empty($request->input("password"))) {
            $validator = Validator::make($request->all(), [
                "password" => "required|string|min:8",
                "confirm_password" => "required|string|min:8|same:password",
            ]);
            if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->type = $request->type;
        $user->save();

        return ResponseHelper::response($user);
    }

    public function delete(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->adminTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        AdminModel::find($id)->delete();

        return ResponseHelper::response();
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return ResponseHelper::response();
    }
}

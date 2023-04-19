<?php

namespace App\Http\Controllers\Auth;

use App\Constants\GenderConstant;
use App\Constants\TokenConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\PasswordResetModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller {
    protected $userTable, $passwordResetTable;

    public function __construct() {
        $this->userTable = (new UserModel())->getTable();
        $this->passwordResetTable = (new PasswordResetModel())->getTable();
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "email" => "required|string|email|unique:$this->userTable,email",
            "password" => "required|string|min:8",
            "confirm_password" => "required|string|min:8|same:password",
            "phone" => ["required", "regex:/^([0-9\s\-\+\(\)]*)$/", "min:10"],
            "gender" => ["required", "numeric", Rule::in([GenderConstant::MALE, GenderConstant::FEMALE])],
            "birthday" => "required|date",
            "city" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $user = UserModel::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "phone" => $request->phone,
            "gender" => $request->gender,
            "birthday" => $request->birthday,
            "city" => $request->city
        ]);

        return ResponseHelper::response([
            "user" => $user,
            "token" => $user->createToken(TokenConstant::TOKEN_NAME, [TokenConstant::AUTH_USER])->plainTextToken
        ]);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            "email" => "required|string|email|exists:$this->userTable,email",
            "password" => "required|string|min:8",
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $user = UserModel::where("email", $request->email)->first();

        if (!Hash::check($request->password, $user->password)) return ResponseHelper::response(null, "The provided credentials are incorrect.", 401);

        return ResponseHelper::response([
            "user" => $user,
            "token" => $user->createToken(TokenConstant::TOKEN_NAME, [TokenConstant::AUTH_USER])->plainTextToken
        ]);
    }

    public function sendForgotPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            "email" => "required|string|email|exists:$this->userTable,email"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        PasswordResetModel::create([
            "email" => $request->email,
            "token" => Str::random(64)
        ]);

        Mail::to($request->email)->send(new PasswordResetMail());

        return ResponseHelper::response();
    }

    public function changeForgotPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            "token" => "required|string|exists:$this->passwordResetTable,token",
            "password" => "required|string|min:8",
            "confirm_password" => "required|string|min:8|same:password"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $reset = PasswordResetModel::where("token", $request->token)->first();
        $user = UserModel::where("email", $reset->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        $reset->forceDelete();

        return ResponseHelper::response();
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return ResponseHelper::response();
    }
}

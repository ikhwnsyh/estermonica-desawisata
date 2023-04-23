<?php

namespace App\Models;

class ChatModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "chats";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "user_id",
        "admin_id",
        "message",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function user() {
        return $this->belongsTo(UserModel::class, "user_id");
    }

    public function admin() {
        return $this->belongsTo(AdminModel::class, "admin_id");
    }
}

<?php

namespace App\Models;

class TransactionHistoryModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "transaction_histories";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "transaction_id",
        "status",
        "created_at",
        "updated_at",
        "deleted_at"
    ];
}

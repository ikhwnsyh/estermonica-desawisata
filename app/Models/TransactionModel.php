<?php

namespace App\Models;

class TransactionModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "transactions";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "invoice_number",
        "user_id",
        "gross_amount",
        "total_adult",
        "total_child",
        "type",
        "snap_url",
        "date",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function user() {
        return $this->belongsTo(UserModel::class, "user_id");
    }

    public function ticketBundle() {
        return $this->hasOne(TransactionTicketBundleModel::class, "transaction_id");
    }

    public function histories() {
        return $this->hasMany(TransactionHistoryModel::class, "transaction_id");
    }

    public function latestHistory() {
        return $this->hasOne(TransactionHistoryModel::class, "transaction_id")->orderByDesc("id");
    }
}

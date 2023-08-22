<?php

namespace App\Models;

class TransactionModel extends BaseModel
{
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
        "ticket_id",
        "user_id",
        "gross_amount",
        "total_adult",
        "total_child",
        "snap_url",
        "date",
        'data_buy',
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function ticket()
    {
        return $this->belongsTo(TicketModel::class, "ticket_id");
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, "user_id");
    }

    public function histories()
    {
        return $this->hasMany(TransactionHistoryModel::class, "transaction_id");
    }

    public function latestHistory()
    {
        return $this->hasOne(TransactionHistoryModel::class, "transaction_id")->orderByDesc("id");
    }
}

<?php

namespace App\Models;

class TransactionTicketModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "transaction_tickets";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "adult_price",
        "child_price",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function ticketBundles() {
        return $this->belongsToMany(TransactionTicketBundleModel::class, (new TransactionTicketBundleTicketModel())->getTable(), "transaction_ticket_id", "transaction_ticket_bundle_id");
    }
}

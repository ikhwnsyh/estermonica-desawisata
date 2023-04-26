<?php

namespace App\Models;

class TransactionTicketBundleTicketModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "transaction_ticket_bundle_tickets";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "transaction_ticket_bundle_id",
        "transaction_ticket_id",
        "created_at",
        "updated_at",
        "deleted_at"
    ];
}

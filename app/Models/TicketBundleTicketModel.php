<?php

namespace App\Models;

class TicketBundleTicketModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "ticket_bundle_tickets";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "ticket_bundle_id",
        "ticket_id",
        "created_at",
        "updated_at",
        "deleted_at"
    ];
}

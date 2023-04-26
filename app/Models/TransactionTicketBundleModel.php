<?php

namespace App\Models;

class TransactionTicketBundleModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "transaction_ticket_bundles";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "transaction_id",
        "name",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function tickets() {
        return $this->belongsToMany(TransactionTicketModel::class, (new TransactionTicketBundleTicketModel())->getTable(), "transaction_ticket_bundle_id", "transaction_ticket_id");
    }
}

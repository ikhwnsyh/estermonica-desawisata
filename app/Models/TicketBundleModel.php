<?php

namespace App\Models;

class TicketBundleModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "ticket_bundles";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function tickets() {
        return $this->belongsToMany(TicketModel::class, (new TicketBundleTicketModel())->getTable(), "ticket_bundle_id", "ticket_id");
    }
}

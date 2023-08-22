<?php

namespace App\Models;

class TicketModel extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "tickets";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "description",
        "adult_price",
        "child_price",
        "minimum_child",
        "minimum_adult",
        "type",
        'stock',
        "created_at",
        "updated_at",
        "deleted_at"
    ];
    //  protected $guarded = [];
    public function imageTicket()
    {
        return $this->hasMany('App\Models\Image');
    }
}

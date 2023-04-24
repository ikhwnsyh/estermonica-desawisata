<?php

use App\Models\TicketBundleModel;
use App\Models\TicketBundleTicketModel;
use App\Models\TicketModel;
use App\Traits\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use MigrationTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create($this->getTable(new TicketBundleTicketModel()), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("ticket_bundle_id");
            $table->unsignedBigInteger("ticket_id");
            $this->timestamps($table);
            $this->softDeletes($table);

            $table->foreign("ticket_bundle_id")->references("id")->on($this->getTable(new TicketBundleModel()))->onDelete("cascade");
            $table->foreign("ticket_id")->references("id")->on($this->getTable(new TicketModel()))->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new TicketBundleTicketModel()));
    }
};

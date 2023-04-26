<?php

use App\Models\TransactionTicketBundleModel;
use App\Models\TransactionTicketBundleTicketModel;
use App\Models\TransactionTicketModel;
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
        Schema::create($this->getTable(new TransactionTicketBundleTicketModel()), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("transaction_ticket_bundle_id");
            $table->unsignedBigInteger("transaction_ticket_id");
            $this->timestamps($table);
            $this->softDeletes($table);

            $table->foreign("transaction_ticket_bundle_id")->references("id")->on($this->getTable(new TransactionTicketBundleModel()))->onDelete("cascade");
            $table->foreign("transaction_ticket_id")->references("id")->on($this->getTable(new TransactionTicketModel()))->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new TransactionTicketBundleTicketModel()));
    }
};

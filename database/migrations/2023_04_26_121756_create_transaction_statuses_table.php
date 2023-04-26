<?php

use App\Models\TransactionHistoryModel;
use App\Models\TransactionModel;
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
        Schema::create($this->getTable(new TransactionHistoryModel()), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("transaction_id");
            $table->unsignedBigInteger("status");
            $this->timestamps($table);
            $this->softDeletes($table);

            $table->foreign("transaction_id")->references("id")->on($this->getTable(new TransactionModel()))->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new TransactionHistoryModel()));
    }
};

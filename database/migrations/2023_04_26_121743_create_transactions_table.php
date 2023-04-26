<?php

use App\Models\TransactionModel;
use App\Models\UserModel;
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
        Schema::create($this->getTable(new TransactionModel()), function (Blueprint $table) {
            $table->id();
            $table->string("invoice_number");
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("gross_amount");
            $table->unsignedBigInteger("total_adult");
            $table->unsignedBigInteger("total_child");
            $table->string("snap_url");
            $this->timestamps($table);
            $this->softDeletes($table);

            $table->foreign("user_id")->references("id")->on($this->getTable(new UserModel()))->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new TransactionModel()));
    }
};

<?php

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
        Schema::create($this->getTable(new TicketModel()), function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->unsignedBigInteger("adult_price");
            $table->unsignedBigInteger("child_price");
            $table->unsignedBigInteger("bundle_adult_price");
            $table->unsignedBigInteger("bundle_child_price");
            $this->timestamps($table);
            $this->softDeletes($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new TicketModel()));
    }
};

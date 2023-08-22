<?php

use App\Models\TicketModel;
use App\Traits\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use MigrationTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->longText("description");
            $table->unsignedBigInteger("adult_price");
            $table->unsignedBigInteger("child_price");
            $table->integer("minimum_adult");
            $table->integer("minimum_child");
            $table->integer("stock");
            $table->unsignedBigInteger("type");
            $this->timestamps($table);
            $this->softDeletes($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->getTable(new TicketModel()));
    }
};

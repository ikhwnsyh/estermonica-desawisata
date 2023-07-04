<?php

use App\Constants\AdminTypeConstant;
use App\Models\AdminModel;
use App\Traits\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;

return new class extends Migration {
    use MigrationTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create($this->getTable(new AdminModel()), function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("email")->unique();
            $table->timestamp("email_verified_at")->nullable();
            $table->string("password");
            $table->unsignedBigInteger("type");
            $table->rememberToken();
            $this->timestamps($table);
            $this->softDeletes($table);
        });

        $email = "admin@gmail.com";
        (new ConsoleOutput())->writeln(PHP_EOL);
        (new ConsoleOutput())->writeln("  Administrator Email : $email");
        $password = "admin123";
        (new ConsoleOutput())->writeln("  Administrator Password : $password");
        AdminModel::create([
            "name" => "Administrator",
            "email" => $email,
            "password" => Hash::make($password),
            "type" => AdminTypeConstant::ADMINISTRATOR
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new AdminModel()));
    }
};

<?php

use Doctrine\DBAL\Types\Type;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecreatingToStateColumnOnInstancesTable extends Migration
{
    public function __construct()
    {
        Type::addType('enum', get_class(Type::getType('string')));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->enum('state', ['preparing', 'running', 'stopped', 'recreating'])->default('preparing')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->enum('state', ['preparing', 'running', 'stopped'])->default('preparing')->change();
        });
    }
}

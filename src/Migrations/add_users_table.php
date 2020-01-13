<?php

namespace Chat\Config\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Capsule::schema()->hasTable('users')) {
            Capsule::schema()->create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->uuid('uuid');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('users');
    }

}
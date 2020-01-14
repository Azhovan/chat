<?php

namespace Chat\Config\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddUserConversationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // if table exist, ignore this migration
        if (Capsule::schema()->hasTable('user_conversations')) {
            return;
        }

        Capsule::schema()->create('user_conversations', function (Blueprint $table) {
            // since this table potentially has very high load
            // I'm choosing bigIncrements
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('conversation_id');
            //text looks good enough
            $table->text('message');
            $table->timestamps();

            // add index
            $table->index('user_id');

            // add foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('conversation_id')->references('id')->on('conversations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('user_conversations');
    }

}
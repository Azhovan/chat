<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // if table exist, ignore this migration
        if (Capsule::schema()->hasTable('conversations')) {
            return;
        }

        Capsule::schema()->create('conversations', function (Blueprint $table) {
            // since this table potentially has very high load
            // I'm choosing bigIncrements
            $table->bigIncrements('id');
            // this key provides end-to-end encryption per conversation
            // every conversation has it's own encryption key
            $table->string('encryption_key');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('conversations');
    }

}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignedOnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signed_ons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identifier');
            $table->string('dept');
            $table->dateTime('last_ping');
            $table->string('session_identifier');
            $table->string('session_name');
            $table->enum('status', ['busy', 'out of serivce', 'available', 'transport', 'enroute', 'on scene', 'off duty'])->default('off duty');
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
        Schema::dropIfExists('signed_ons');
    }
}

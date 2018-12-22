<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->string('rp')->nullable();
            $table->string('address')->nullable();
            $table->string('postal')->nullable();
            $table->string('nature')->nullable();
            $table->mediumText('data')->nullable();
            $table->text('assigned')->nullable();
            $table->tinyInteger('is_archived')->default(0);
            $table->timestamps();
        });
        Schema::create('call_notes', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('calls');
        Schema::dropIfExists('call_notes');
    }
}

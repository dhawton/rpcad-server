<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("users", function (Blueprint $table) {
            $table->enum("status", ["Offline","Available","Busy","Out of Service"])->nullable()->after("password");
            $table->integer("server_id")->nullable()->after("status");
            $table->string("department")->nullable()->after("server_id");
            $table->string("division")->nullable()->after("department");
            $table->dateTime("signed_on")->nullable()->after("department");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("user_id");
            $table->string('idnumber')->nullable();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('address');
            $table->enum("gender", ["Male","Female"]);
            $table->date("datebirth");
            $table->string("race");
            $table->string("haircolor");
            $table->smallInteger("height_feet");
            $table->smallInteger("height_inches");
            $table->smallInteger("weight");
            $table->text("photo")->nullable()->comment("Base 64 encoded image");
            $table->enum("licensestatus", ["ID Only","Learner\'s Permit","Valid","Suspended","Revoked"])->default("ID Only");
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
        Schema::dropIfExists('characters');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 75)->unique();
            $table->date('birth_date')->nullable();
            $table->string('birth_place', 75)->nullable();
            $table->string('photo', 255);
            $table->text('bio');
            $table->string('profile_url', 75)->unique();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('actor_id');
            $table->foreign('actor_id')->references('id')->on('actors');
            $table->string('film', 100);
            $table->string('role', 1500);
            $table->string('year', 15);
            $table->unique(['actor_id', 'film', 'year']);
        });

        Schema::create('configs', function (Blueprint $table) {
            $table->string('type', 50);
            $table->string('value', 50);
            $table->unique('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actors');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('configs');
    }
}

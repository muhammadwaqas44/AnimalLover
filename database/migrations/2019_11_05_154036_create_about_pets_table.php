<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAboutPetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('about_pets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kind')->nullable();
            $table->string('name')->nullable();
            $table->string('age')->nullable();
            $table->string('breed')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->text('pets_description')->nullable();
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
        Schema::dropIfExists('about_pets');
    }
}

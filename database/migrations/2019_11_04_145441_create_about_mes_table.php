<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAboutMesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('about_mes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('gender')->nullable();
            $table->string('relationship_status')->nullable();
            $table->string('occupation')->nullable();
            $table->string('interested_animal')->nullable();
            $table->string('interested_gender')->nullable();
            $table->string('age')->nullable();
            $table->text('about_me')->nullable();
            $table->bigInteger('user_id')->nullable();
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
        Schema::dropIfExists('about_mes');
    }
}

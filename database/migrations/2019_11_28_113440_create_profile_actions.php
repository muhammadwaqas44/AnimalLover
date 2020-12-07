<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('action_to')->unsigned();
            $table->bigInteger('action_by')->unsigned();
            $table->boolean('like')->default(0);
            $table->boolean('dont_like')->default(0);
            $table->boolean('not_sure')->default(0);
            $table->boolean('block')->default(0);
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
        Schema::dropIfExists('profile_actions');
    }
}

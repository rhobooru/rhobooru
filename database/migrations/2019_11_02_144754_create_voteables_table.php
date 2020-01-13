<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoteablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voteables', function (Blueprint $table) {
            // Voteables Info
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('voteable_id');
            $table->string('voteable_type');
            $table->tinyInteger('score'); // -1 or 1 to allow for easy SUM()ing

            // Indicies & Constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->primary(['user_id', 'voteable_id', 'voteable_type']);
            $table->index(['voteable_id', 'voteable_type']);
            $table->index(['user_id', 'voteable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voteables');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFriendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_friends', function (Blueprint $table) {
            // User Friend Info
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('friend_user_id');
            $table->boolean('accepted')->default(false);
            $table->string('invite_message')->nullable();

            // Indicies
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('friend_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->primary(['user_id', 'friend_user_id']);
            $table->index('user_id');
            $table->index('friend_user_id');
            $table->index('accepted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_friends');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBookmarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bookmarks', function (Blueprint $table) {
            // Audit Info
            $table->dateTime('created_at');

            // Bookmark Info
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('folder_id');
            $table->unsignedBigInteger('last_seen_page')->default(1);

            // Indicies
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');

            $table->index('user_id');
            $table->index(['user_id', 'folder_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_bookmarks');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Audit Info
            $table->timestamps();
            $table->softDeletes();
            $table->timestampUsers();
            $table->softDeletesUser();

            // Folder Info
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->unsignedSmallInteger('folder_type_id');
            $table->unsignedSmallInteger('access_type_id');

            // Cached Aggregates
            $table->unsignedBigInteger('cache_records')->default(0);
            $table->unsignedBigInteger('cache_views')->default(0);
            $table->unsignedBigInteger('cache_favorites_count')->default(0);
            $table->unsignedBigInteger('cache_folders_count')->default(0);
            $table->unsignedBigInteger('cache_comments_count')->default(0);

            // Indices & Constraints
            $table->foreign('folder_type_id')->references('id')->on('folder_types');
            $table->foreign('access_type_id')->references('id')->on('access_types');

            $table->index(['folder_type_id', 'created_by_user_id']);
            $table->index('name');
            $table->index('access_type_id');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folders');
    }
}

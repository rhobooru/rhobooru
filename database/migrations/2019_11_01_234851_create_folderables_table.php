<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFolderablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folderables', function (Blueprint $table) {
            // Folderables Info
            $table->unsignedbigInteger('folder_id');
            $table->unsignedBigInteger('folderable_id');
            $table->string('folderable_type');

            // Indicies
            $table->foreign('folder_id')->references('id')->on('folders');

            $table->index('folder_id');
            $table->index(['folderable_id', 'folderable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folderables');
    }
}

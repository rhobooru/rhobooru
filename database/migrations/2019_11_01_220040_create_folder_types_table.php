<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFolderTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folder_types', function (Blueprint $table) {
            $table->smallIncrements('id');

            // Folder Type Info
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('static_name')->unique();
            $table->boolean('can_be_managed_manually')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder_types');
    }
}

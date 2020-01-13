<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagAssociationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_association_types', function (Blueprint $table) {
            $table->smallIncrements('id');

            // Tag Association Type Info
            $table->string('name');
            $table->string('description');

            // Indices & Constraints
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_association_types');
    }
}

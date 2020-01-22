<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagAssociationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_associations', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Associated Tags Info
            $table->unsignedBigInteger('tag_id_1');
            $table->unsignedBigInteger('tag_id_2');
            $table->unsignedSmallInteger('tag_association_type_id');

            // Indices & Constraints
            $table->foreign('tag_association_type_id')->references('id')->on('tag_association_types');
            $table->foreign('tag_id_1')->references('id')->on('tags');
            $table->foreign('tag_id_2')->references('id')->on('tags');

            $table->unique(['tag_id_1', 'tag_id_2', 'tag_association_type_id'], 'tag_association_unique_1_2_type');
            $table->index(['tag_id_1', 'tag_association_type_id']);
            $table->index(['tag_id_2', 'tag_association_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_associations');
    }
}

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
            // Associated Tags Info
            $table->unsignedBigInteger('tag_id_1')->index()->references('id')->on('tags');
            $table->unsignedBigInteger('tag_id_2')->index()->references('id')->on('tags');
            $table->unsignedSmallInteger('tag_association_type_id');

            // Indices & Constraints
            $table->foreign('tag_association_type_id')->references('id')->on('tag_association_types');

            $table->primary(['tag_id_1', 'tag_id_2']);
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

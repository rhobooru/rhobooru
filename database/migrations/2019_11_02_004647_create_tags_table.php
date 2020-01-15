<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Audit Info
            $table->timestamps();
            $table->softDeletes();
            $table->timestampUsers();
            $table->softDeletesUser();

            // Tag Info
            $table->string('name');
            $table->mediumText('description')->nullable();
            $table->unsignedBigInteger('aliased_to_tag_id')->nullable();

            // Indicies & Constraints
            $table->foreign('aliased_to_tag_id')->references('id')->on('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}

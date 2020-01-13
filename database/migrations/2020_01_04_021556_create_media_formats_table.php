<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaFormatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_formats', function (Blueprint $table) {
            $table->mediumIncrements('id');


            // Media Format Info
            $table->string('extension');
            $table->string('mime');
            $table->unsignedSmallInteger('record_type_id');
            $table->boolean('can_produce_thumbnails')->default(true);
            $table->boolean('accepted_for_upload')->default(true);

            // Indices & Constraints
            $table->foreign('record_type_id')->references('id')->on('record_types');

            $table->index('extension');
            $table->index('mime');
            $table->index('record_type_id');
            $table->index('accepted_for_upload');
            $table->unique('extension');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_formats');
    }
}

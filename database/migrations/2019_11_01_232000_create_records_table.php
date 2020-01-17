<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Audit Info
            $table->timestamps();
            $table->softDeletes();
            $table->timestampUsers();
            $table->softDeletesUser();
            $table->boolean('upload_complete')->default(false);
            $table->boolean('approved')->default(false);

            // Media Info
            $table->string('md5');

            /*
             * As of MariaDB 10.4, BIT_COUNT() ONLY works on BIGINTs.
             * If you're using MySQL, it should work on BINARYs and BLOBs.
             */
            $table->unsignedBigInteger('phash')->nullable();

            $table->string('title')->nullable();
            $table->string('file_extension')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedBigInteger('width')->default(0);
            $table->unsignedBigInteger('height')->default(0);
            $table->unsignedDecimal('megapixels', 18, 9)->default(0);
            $table->unsignedDecimal('aspect_ratio', 9, 8)->default(0);
            $table->unsignedInteger('duration')->default(0);
            $table->unsignedDecimal('framerate', 9, 4)->default(0);
            $table->unsignedSmallInteger('record_type_id');
            $table->unsignedSmallInteger('content_rating_id');
            $table->unsignedBigInteger('views')->default(0);

            // Indices & Constraints
            $table->unique('md5');

            $table->foreign('record_type_id')->references('id')->on('record_types');
            $table->foreign('content_rating_id')->references('id')->on('content_ratings');
            
            $table->index(['md5', 'approved', 'deleted_at']);
            $table->index('content_rating_id');
            $table->index('approved');
            $table->index('upload_complete');
            $table->index('deleted_at');
            $table->index('phash');
        });

        //DB::statement('ALTER TABLE records ADD `phash` BINARY(64) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('records');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_ratings', function (Blueprint $table) {
            $table->smallIncrements('id');

            // Content Rating Info
            $table->string('name');
            $table->string('short_name'); // ie. A character for display in tight spaces
            $table->text('description')->nullable();
            $table->boolean('available_to_anonymous')->default(true);   // Allow items with this ContentRating to 
                                                                        // be viewed by anonymous users
            $table->unsignedSmallInteger('order')->unique();

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
        Schema::dropIfExists('content_ratings');
    }
}

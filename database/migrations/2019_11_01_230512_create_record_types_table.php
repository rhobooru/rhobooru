<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordTypesTable extends Migration
{
    /**
     * Whether to allow created_at and updated_at.
     * 
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('record_types', function (Blueprint $table) {
            $table->smallIncrements('id');

            // Record Type Info
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('requires_player_controls')->default(false);

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
        Schema::dropIfExists('record_types');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            // Setting Info
            $table->unsignedInteger('setting_group_id')->nullable();
            $table->boolean('system_setting')->default(false);
            $table->string('key');
            $table->unsignedBigInteger('order')->default(0);
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('default_value')->nullable();
            $table->enum('control', ['textbox', 'textarea', 'number', 'checkbox', 'select']);
            $table->boolean('allow_null')->default(false);
            $table->boolean('allow_multiple')->default(false);
            $table->bigInteger('minimum_value')->nullable()->default(null);
            $table->bigInteger('maximum_value')->nullable()->default(null);
            $table->string('references_model')->nullable(); // Qualified model name for dropdown lookups
            $table->string('references_method')->nullable(); // Model method for lookup, if not ::all()
            $table->string('references_value')->nullable(); // Value column for lookup.
            $table->string('references_text')->nullable(); // Display text column for lookup.

            // Indices & Constraints
            $table->foreign('setting_group_id')->references('id')->on('setting_groups')->onDelete('set null');

            $table->unique(['key', 'system_setting', 'setting_group_id']);
            $table->index('system_setting');
            $table->index('setting_group_id');
            $table->index('references_model');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}

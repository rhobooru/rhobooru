<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            // Setting Group Info
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('setting_group_id')->nullable();
            $table->unsignedInteger('order');

            // Indices & Constraints
            $table->foreign('setting_group_id')->references('id')->on('setting_groups')->onDelete('set null');

            $table->unique(['name', 'setting_group_id']);
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_groups');
    }
}

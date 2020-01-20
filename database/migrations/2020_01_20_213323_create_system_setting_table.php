<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_setting', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            // User Setting Info
            $table->unsignedBigInteger('setting_id');
            $table->text('value')->nullable();

            // Indices & Constraints
            $table->foreign('setting_id')->references('id')->on('settings');

            $table->unique('setting_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_setting');
    }
}

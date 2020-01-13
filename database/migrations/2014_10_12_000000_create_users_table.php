<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // Audit Info
            $table->timestamps();
            $table->softDeletes();
            $table->timestampUsers(true);
            $table->softDeletesUser();

            // User Info
            $table->string('username');
            $table->string('password');
            $table->rememberToken();
            $table->string('avatar')->nullable();
            $table->boolean('system_account')->default(false);
            $table->boolean('anonymous_account')->nullable();

            // Indicies & Constraints
            $table->unique('username');
            $table->unique('anonymous_account');
            $table->index(['deleted_at', 'system_account', 'anonymous_account']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    private $settingsDefault = [
        'email' => [
            'records' => [
                'record_approval' => false,
                'record_removal' => true,
            ],

            'friends' => [
                'friend_request_received' => true,
                'friend_request_accepted' => true,
            ],

            'comments' => [
                'new_post_on_own_record' => false,
                'new_post_on_own_thread' => true,
                'new_post_on_commented_thread' => true,
                'mentions' => true,
            ],

            'dm' => [
                'new_pm_from_user' => true,
                'new_pm_from_staff' => true,
            ],

            'moderation' => [
                'moderator_action' => true,
            ],

            'following' => [
                'following_user_activity' => true,
            ],
        ],
    ];

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
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('bio')->nullable();

            // $table->unsignedSmallInteger('record_fit_id');
            // $table->unsignedSmallInteger('maximum_content_rating_id');

            // Indicies & Constraints
            $table->unique('username');
            $table->unique('anonymous_account');
            $table->index(['deleted_at', 'system_account', 'anonymous_account']);

            // $table->foreign('maximum_content_rating_id')->references('id')->on('content_ratings');
            // $table->foreign('date_format_id')->references('id')->on('date_formats');
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    private $settingsDefault = [
        'utc_offset' => [
            'name' => 'UTC Offset',
            'description' => 'Timezone offset for diplaying dates in local time.',
            'allowed_types' => 'int',
            'value' => 0,
        ],

        'privacy' => [
            'allow_friend_requests' => true,
            'allow_pms_from_anyone' => false,
            'profile_is_public' => false,
            'friends_list_is_public' => false,
            'folders_list_is_public' => false,
            'community_list_is_public' => false,
            'favorite_tags_list_is_public' => false,
            'comment_anonymous_by_default' => false,
        ],

        // Moderation
        'hide_blocked_users' => false,

        'record_viewing' => [
            'show_full_size_records' => false,
            'warn_when_downloading_samples' => true,
            'nested_comments' => true,
            'show_prev_next_on_searched_record_view' => true,
            'infinite_scroll' => false,
            'minimum_comment_score' => -10,
            'minimum_record_score' => -100,
        ],

        'forum_viewing' => [
            'threads' => [
                'infinite_scroll' => false,
            ],
            'posts' => [
                'ascending_order' => true,
                'infinite_scroll' => false,
                'minimum_post_score' => -10,
            ],
        ],

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

            // $table->unsignedSmallInteger('site_theme_id');
            // $table->unsignedSmallInteger('date_format_id');
            // $table->unsignedSmallInteger('record_fit_id');
            // $table->unsignedSmallInteger('maximum_content_rating_id');

            // Indicies & Constraints
            $table->unique('username');
            $table->unique('anonymous_account');
            $table->index(['deleted_at', 'system_account', 'anonymous_account']);
            
            // $table->foreign('site_theme_id')->references('id')->on('site_themes');
            // $table->foreign('record_fit_id')->references('id')->on('record_fits');
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

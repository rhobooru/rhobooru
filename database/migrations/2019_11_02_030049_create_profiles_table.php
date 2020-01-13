<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();

            // Profile Info
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('bio')->nullable();
            $table->string('banner_image')->nullable();

            // User Settings
            $table->unsignedSmallInteger('site_theme_id');
            $table->smallInteger('utc_offset')->default(0);
            $table->unsignedSmallInteger('date_format_id');

                // Privacy
                $table->boolean('allow_friend_requests')->default(true);
                $table->boolean('allow_pms_from_anyone')->default(false);
                $table->boolean('profile_is_public')->default(false);
                $table->boolean('friends_list_is_public')->default(false);
                $table->boolean('folders_list_is_public')->default(false);
                $table->boolean('community_list_is_public')->default(false);
                $table->boolean('favorite_tags_list_is_public')->default(false);
                $table->boolean('comment_anonymous_by_default')->default(false);

                // Moderation
                $table->bigInteger('minimum_comment_score_threshold')->default(-5);
                $table->bigInteger('minimum_post_score_threshold')->nullable();
                $table->boolean('hide_blocked_users')->default(false);

                // Record Viewing
                $table->unsignedSmallInteger('record_fit_id');
                $table->boolean('show_full_size_records')->default(false);
                $table->boolean('warn_when_downloading_samples')->default(true);
                $table->unsignedSmallInteger('maximum_content_rating_id');
                $table->boolean('nested_comments')->default(true);
                $table->integer('comment_threshold')->default(-10);
                $table->boolean('show_prev_next_on_searched_record_view')->default(true);

                // Forum Viewing
                $table->boolean('posts_in_ascending_order')->default(true);


                // Infinite Scroll
                $table->boolean('infinite_scroll_records')->default(false);
                $table->boolean('infinite_scroll_threads')->default(false);
                $table->boolean('infinite_scroll_posts')->default(false);

                // Email Preferences
                    // Records
                    $table->boolean('email_for_record_approval')->default(false);
                    $table->boolean('email_for_record_removal')->default(true);

                    // Friends
                    $table->boolean('email_for_friend_request_received')->default(true);
                    $table->boolean('email_for_friend_request_accepted')->default(true);

                    // Comments
                    $table->boolean('email_for_new_post_on_own_record')->default(false);
                    $table->boolean('email_for_new_post_on_own_thread')->default(true);
                    $table->boolean('email_for_new_post_on_commented_thread')->default(true);
                    $table->boolean('email_for_mentions')->default(true);
                    
                    // PMs
                    $table->boolean('email_for_new_pm_from_user')->default(true);
                    $table->boolean('email_for_new_pm_from_staff')->default(true);

                    // Moderation
                    $table->boolean('email_for_moderator_action')->default(true);

                    // Following
                    $table->boolean('email_for_following_user_activity')->default(true);

            // Cached Aggregates
            $table->unsignedBigInteger('cache_records_count')->default(0);
            $table->unsignedBigInteger('cache_folders_count')->default(0);
            $table->unsignedBigInteger('cache_posts_count')->default(0);
            $table->unsignedBigInteger('cache_threads_count')->default(0);
            $table->unsignedBigInteger('cache_favorites_count')->default(0);
            $table->unsignedBigInteger('cache_friends_count')->default(0);
            $table->bigInteger('cache_records_score')->default(0);
            $table->unsignedBigInteger('cache_notes')->default(0);

            // Indicies & Constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('site_theme_id')->references('id')->on('site_themes');
            $table->foreign('record_fit_id')->references('id')->on('record_fits');
            $table->foreign('maximum_content_rating_id')->references('id')->on('content_ratings');
            $table->foreign('date_format_id')->references('id')->on('date_formats');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}

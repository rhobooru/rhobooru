<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\SeedsDefaultValues;
use Tests\TestCase;
use \App\Models\Profile;

class ProfileTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * Profiles must be able to find their user.
     *
     * @test
     * @covers \App\Models\Profile::user
     */
    public function profile_can_find_user()
    {
        $user = factory(\App\Models\User::class)->create();
        $user->profile->delete();
        $user_id = $user->id;

        $profile_id = factory(\App\Models\Profile::class)->create([
            'user_id' => $user_id,
        ])->user_id;

        $this->assertInstanceOf(\App\Models\User::class, Profile::find($profile_id)->user);
        $this->assertEquals($user_id, Profile::find($profile_id)->user->id);
    }

    /**
     * Profiles must be able to find their site theme.
     *
     * @test
     * @covers \App\Models\Profile::site_theme
     */
    public function profile_can_find_site_theme()
    {
        $relation_id = \App\Models\SiteTheme::where('is_default', true)->first()->id;

        $profile_id = factory(\App\Models\Profile::class)->create([
            'site_theme_id' => $relation_id,
        ])->user_id;

        $this->assertInstanceOf(\App\Models\SiteTheme::class, Profile::find($profile_id)->site_theme);
        $this->assertEquals($relation_id, Profile::find($profile_id)->site_theme->id);
    }

    /**
     * Profiles must be able to find their date format.
     *
     * @test
     * @covers \App\Models\Profile::date_format
     */
    public function profile_can_find_date_format()
    {
        $relation_id = \App\Models\DateFormat::where('is_default', true)->first()->id;

        $profile_id = factory(\App\Models\Profile::class)->create([
            'date_format_id' => $relation_id,
        ])->user_id;

        $this->assertInstanceOf(\App\Models\DateFormat::class, Profile::find($profile_id)->date_format);
        $this->assertEquals($relation_id, Profile::find($profile_id)->date_format->id);
    }

    /**
     * Profiles must be able to find their record fit.
     *
     * @test
     * @covers \App\Models\Profile::record_fit
     */
    public function profile_can_find_record_fit()
    {
        $relation_id = \App\Models\RecordFit::where('is_default', true)->first()->id;

        $profile_id = factory(\App\Models\Profile::class)->create([
            'record_fit_id' => $relation_id,
        ])->user_id;

        $this->assertInstanceOf(\App\Models\RecordFit::class, Profile::find($profile_id)->record_fit);
        $this->assertEquals($relation_id, Profile::find($profile_id)->record_fit->id);
    }

    /**
     * Profiles must be able to find their max content rating.
     *
     * @test
     * @covers \App\Models\Profile::max_content_rating
     */
    public function profile_can_find_max_content_rating()
    {
        $relation_id = \App\Models\ContentRating::orderBy('order','desc')->first()->id;

        $profile_id = factory(\App\Models\Profile::class)->create([
            'maximum_content_rating_id' => $relation_id,
        ])->user_id;

        $this->assertInstanceOf(\App\Models\ContentRating::class, Profile::find($profile_id)->max_content_rating);
        $this->assertEquals($relation_id, Profile::find($profile_id)->max_content_rating->id);
    }
}

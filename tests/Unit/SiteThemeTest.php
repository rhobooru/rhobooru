<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use \App\Models\SiteTheme;

class SiteThemeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Should be able to find the default site theme.
     *
     * @test
     * @covers \App\Models\SiteTheme::scopeDefault
     */
    public function can_find_default_site_theme()
    {
        factory(SiteTheme::class, 5)->create(['is_default' => false]);
        $default_theme = factory(SiteTheme::class)->create(['is_default' => true]);

        $this->assertInstanceOf(SiteTheme::class, SiteTheme::default()->first());
        $this->assertEquals($default_theme->id, SiteTheme::default()->first()->id);
    }
}

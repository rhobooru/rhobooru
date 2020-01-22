<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use \App\Models\DateFormat;

class DateFormatTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Should be able to find the default date format.
     *
     * @test
     * @covers \App\Models\DateFormat::scopeDefault
     */
    public function can_find_default_date_format()
    {
        factory(\App\Models\DateFormat::class, 5)->create(['is_default' => false]);
        $default = factory(\App\Models\DateFormat::class)->create(['is_default' => true]);

        $this->assertInstanceOf(\App\Models\DateFormat::class, DateFormat::default()->first());
        $this->assertEquals($default->id, DateFormat::default()->first()->id);
    }
}

<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use \App\Models\RecordFetch;

class RecordFetchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Should be able to find the default record fetch.
     *
     * @test
     * @covers \App\Models\RecordFetch::scopeDefault
     */
    public function can_find_default_record_fetch()
    {
        factory(RecordFetch::class, 5)->create(['is_default' => false]);
        $default = factory(RecordFetch::class)->create(['is_default' => true]);

        $this->assertInstanceOf(RecordFetch::class, RecordFetch::default()->first());
        $this->assertEquals($default->id, RecordFetch::default()->first()->id);
    }
}

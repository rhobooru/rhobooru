<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use \App\Models\RecordFit;

class RecordFitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Should be able to find the default record fit.
     *
     * @test
     * @covers \App\Models\RecordFit::scopeDefault
     */
    public function can_find_default_record_fit()
    {
        factory(RecordFit::class, 5)->create(['is_default' => false]);
        $default = factory(RecordFit::class)->create(['is_default' => true]);

        $this->assertInstanceOf(RecordFit::class, RecordFit::default()->first());
        $this->assertEquals($default->id, RecordFit::default()->first()->id);
    }
}

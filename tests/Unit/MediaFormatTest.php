<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\SeedsDefaultValues;
use \App\Models\MediaFormat;
use \App\Models\RecordType;

class MediaFormatTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * MediaFormats must be able to find their related RecordType.
     *
     * @test
     * @covers \App\Models\MediaFormat::record_type
     */
    public function media_format_can_find_record_type()
    {
        $record_type = factory(RecordType::class)->create();

        $media_format = factory(MediaFormat::class)->create([
            'record_type_id' => $record_type->id,
        ]);

        $this->assertInstanceOf(RecordType::class, MediaFormat::find($media_format->id)->record_type);
        $this->assertEquals($record_type->id, MediaFormat::find($media_format->id)->record_type->id);
    }
}
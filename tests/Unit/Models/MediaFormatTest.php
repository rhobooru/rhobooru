<?php

namespace Tests\Unit\Models;

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

    /**
     * MediaFormats can limit query to requires_media_controls.
     *
     * @test
     * @covers \App\Models\MediaFormat::scopeRequiresMediaControls
     */
    public function can_limit_to_requires_media_controls()
    {
        $record_type = factory(RecordType::class)->create([
            'requires_player_controls' => true,
        ]);

        $media_format = factory(MediaFormat::class)->create([
            'record_type_id' => $record_type->id,
        ]);

        $this->assertTrue(in_array($media_format->id, MediaFormat::requiresMediaControls()->pluck('id')->toArray()));
    }

    /**
     * MediaFormats can limit query to accepted_for_uploads.
     *
     * @test
     * @covers \App\Models\MediaFormat::scopeAcceptedForUpload
     */
    public function can_limit_to_accepted_for_upload()
    {
        $media_format = factory(MediaFormat::class)->create([
            'accepted_for_upload' => true,
        ]);

        $this->assertTrue(in_array($media_format->id, MediaFormat::acceptedForUpload()->pluck('id')->toArray()));
    }
}
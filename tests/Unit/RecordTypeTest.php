<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\SeedsDefaultValues;
use \App\Models\RecordType;
use \App\Models\Record;
use \App\Models\MediaFormat;

class RecordTypeTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * RecordTypes must be able to find their related Records.
     *
     * @test
     * @covers \App\Models\RecordType::records
     */
    public function record_type_can_find_records()
    {
        $record_type = factory(RecordType::class)->create();

        $record = factory(Record::class)->states('approved')->create([
            'record_type_id' => $record_type->id,
        ]);

        $this->assertEquals(1, RecordType::find($record_type->id)->records->count());
        $this->assertEquals($record->md5, RecordType::find($record_type->id)->records->first()->md5);
    }

    /**
     * RecordTypes must be able to find their related MediaFormats.
     *
     * @test
     * @covers \App\Models\RecordType::media_formats
     */
    public function record_type_can_find_media_formats()
    {
        $record_type = factory(RecordType::class)->create();
        $trash = factory(RecordType::class)->create();

        $media_format = factory(MediaFormat::class)->create([
            'record_type_id' => $record_type->id,
        ]);

        $media_format2 = factory(MediaFormat::class)->create([
            'record_type_id' => $record_type->id,
        ]);

        $media_format3 = factory(MediaFormat::class)->create([
            'record_type_id' => $trash->id,
        ]);

        $this->assertEquals(2, RecordType::find($record_type->id)->media_formats()->count());
        $this->assertEquals($media_format->id, RecordType::find($record_type->id)->media_formats()->first()->id);
    }

    /**
     * RecordType queries can be limited by requires_media_controls.
     *
     * @test
     * @covers \App\Models\RecordType::scopeRequiresMediaControls
     */
    public function record_type_queries_can_be_limited_by_requires_media_controls()
    {
        foreach(RecordType::all() as $model)
        {
            $model->requires_player_controls = false;
            $model->save();
        }

        $requires = factory(RecordType::class)->create([
            'requires_player_controls' => true,
        ]);

        $doesnt_require = factory(RecordType::class)->create([
            'requires_player_controls' => false,
        ]);

        $this->assertEquals(1, RecordType::requiresMediaControls()->count());
        $this->assertEquals($requires->id, RecordType::requiresMediaControls()->first()->id);
    }
}
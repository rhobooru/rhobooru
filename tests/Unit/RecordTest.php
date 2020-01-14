<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\SeedsDefaultValues;
use \App\Models\Record;
use \App\Models\ContentRating;
use \App\Models\RecordType;
use \App\Models\Tag;
use \App\Models\User;

class RecordTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * Records must be able to find their ContentRating.
     *
     * @test
     * @covers \App\Models\Record::content_rating
     */
    public function records_can_find_their_content_rating()
    {
        $content_rating = factory(ContentRating::class)->create();
        
        $record = factory(Record::class)->states('approved')->create([
            'content_rating_id' => $content_rating->id,
        ]);

        $this->assertEquals($content_rating->id, Record::find($record->id)->content_rating->id);
    }

    /**
     * Records must be able to find their RecordType.
     *
     * @test
     * @covers \App\Models\Record::record_type
     */
    public function records_can_find_their_record_type()
    {
        $record_type = factory(RecordType::class)->create();

        $record = factory(Record::class)->states('approved')->create([
            'record_type_id' => $record_type->id,
        ]);

        $this->assertEquals($record_type->id, Record::find($record->id)->record_type->id);
    }

    /**
     * The 'scopeApproved' scope should find all and only approved Records.
     *
     * @test
     * @covers \App\Models\Record::scopeApproved
     */
    public function approved_scope_finds_approved_records()
    {
        factory(Record::class, 5)->states('unapproved')->create();
        factory(Record::class, 7)->states('approved')->create();

        $this->assertEquals(7, Record::approved()->count());
        $this->assertEquals([true, true, true, true, true, true, true], 
            Record::approved()->pluck('approved')->toArray());
    }

    /**
     * The 'scopeNotApproved' scope should find all and only unapproved Records.
     *
     * @test
     * @covers \App\Models\Record::scopeNotApproved
     */
    public function unapproved_scope_finds_unapproved_records()
    {
        factory(Record::class, 5)->states('unapproved')->create();
        factory(Record::class, 7)->states('approved')->create();

        $this->assertEquals(5, Record::notApproved()->count());
        $this->assertEquals([false, false, false, false, false], 
            array_map(function($element){ return (bool) $element; }, 
            Record::notApproved()
                ->pluck('approved')
                ->toArray()));
    }

    /**
     * Records must be able to find their Tags.
     *
     * @test
     * @covers \App\Models\Record::tags
     */
    public function records_can_find_their_tags()
    {
        $record = factory(Record::class)->states('approved')->create();
        $tag = factory(Tag::class)->create();

        $this->assertEquals(0, $record->tags()->count());

        $record->tags()->save($tag);

        $this->assertEquals(1, $record->tags()->count());
        $this->assertEquals($tag->id, Record::find($record->id)->tags()->first()->id);
    }
}

<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Data;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\SeedsDefaultValues;
use \App\Models\ContentRating;

class ContentRatingTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * ContentRatings must be able to find their related Records.
     *
     * @test
     * @covers \App\Models\ContentRating::records
     */
    public function content_rating_can_find_records()
    {
        $content_rating = factory(\App\Models\ContentRating::class)->create();

        $record = factory(\App\Models\Record::class)->create([
            'content_rating_id' => $content_rating->id,
        ]);

        $content_rating = ContentRating::find($content_rating->id);

        $this->assertEquals(1, $content_rating->records->count());
        $this->assertEquals($record->md5, $content_rating->records->first()->md5);
    }

    /**
     * Must be able to find content rating with maximum 'order'.
     *
     * @test
     * @covers \App\Models\ContentRating::scopeMaximum
     */
    public function can_find_maximum_rating()
    {
        factory(ContentRating::class, 5)->create();

        $max_order = ContentRating::max('order');
        $max_rating_id = ContentRating::where('order', $max_order)->first()->id;

        $this->assertEquals($max_order, ContentRating::maximum()->first()->order);
        $this->assertEquals($max_rating_id, ContentRating::maximum()->first()->id);
    }

    /**
     * Must be able to find content rating with minimum 'order'.
     *
     * @test
     * @covers \App\Models\ContentRating::scopeMinimum
     */
    public function can_find_minimum_rating()
    {
        factory(ContentRating::class, 5)->create();

        $min_order = ContentRating::min('order');
        $min_rating_id = ContentRating::where('order', $min_order)->first()->id;

        $this->assertEquals($min_order, ContentRating::minimum()->first()->order);
        $this->assertEquals($min_rating_id, ContentRating::minimum()->first()->id);
    }

    /**
     * Must be able to find content ratings that are available to anonymous users.
     *
     * @test
     * @covers \App\Models\ContentRating::scopePublic
     */
    public function can_find_public_ratings()
    {
        // Make sure we don't have extra data lying around in the table.
        $this->artisan('migrate:refresh');

        $public_inserted = factory(ContentRating::class, 6)->create(['available_to_anonymous' => true])->pluck('id')->toArray();
        factory(ContentRating::class, 3)->create(['available_to_anonymous' => false]);

        $public_ids = ContentRating::public()->pluck('id')->toArray();

        $this->assertEquals(6, count($public_ids));
        $this->assertEqualsCanonicalizing($public_inserted, $public_ids);
    }

    /**
     * By default, content ratings should come out ordered by 'order' ascending.
     *
     * @test
     * @covers \App\Models\ContentRating::boot
     */
    public function default_order_is_enforced()
    {
        factory(ContentRating::class, 10)->create();

        $ratings = ContentRating::get();
        $sorted_ratings = ContentRating::orderBy('order', 'asc')->get();

        $this->assertEquals($sorted_ratings->toArray(), $ratings->toArray());
    }
}

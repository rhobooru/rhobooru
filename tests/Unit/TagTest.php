<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\SeedsDefaultValues;
use \App\Models\Record;
use \App\Models\Tag;
use \App\Models\User;

class TagTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * Tags must be able to find their Records.
     *
     * @test
     * @covers \App\Models\Tag::records
     */
    public function tag_can_find_their_records()
    {
        $record = factory(Record::class)->create();
        $tag = factory(Tag::class)->create();

        $this->assertEquals(0, $tag->records()->count());

        $tag->records()->save($record);

        $this->assertEquals(1, $tag->records()->count());
        $this->assertEquals($record->id, Tag::find($tag->id)->records()->first()->id);
    }

    /**
     * Adding a Record to a Tag must increment that Tag's cached record count.
     *
     * @test
     * @covers \App\Observers\RecordTagObserver::created
     * @covers \App\Pivots\RecordTag::save
     */
    public function adding_record_should_increment_cached_field()
    {
        $record = factory(Record::class)->create();
        $tag = factory(Tag::class)->create();

        $this->assertEquals(0, Tag::first()->cache_record_count);

        $tag->records()->save($record);

        $this->assertEquals(1, Tag::first()->cache_record_count);
    }

    /**
     * Removing a Record from a Tag must decrement that Tag's cached record count.
     *
     * @test
     * @covers \App\Observers\RecordTagObserver::deleted
     * @covers \App\Pivots\RecordTag::deleting
     * @covers \App\Pivots\RecordTag::delete
     */
    public function removing_record_should_decrement_cached_field()
    {
        $record = factory(Record::class)->create();
        $tag = factory(Tag::class)->create();

        $tag->records()->save($record);

        $this->assertEquals(1, Tag::first()->cache_record_count);

        Tag::first()->records()->detach($record);

        $this->assertEquals(0, Tag::first()->cache_record_count);
    }

    /**
     * Force deleting a Record must decrement its Tags' cached record count.
     *
     * @test
     * @covers \App\Observers\RecordObserver::forceDeleted
     */
    public function force_deleting_record_should_decrement_cached_field()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $record = factory(Record::class)->create();
        $tag = factory(Tag::class)->create();

        $tag->records()->save($record);

        $this->assertEquals(1, Tag::first()->cache_record_count);

        $record->forceDelete();
        $tag->refresh();

        $this->assertEquals(0, $tag->cache_record_count);
    }

    /**
     * Force deleting a tag must decrement its records' cached tag count.
     *
     * @test
     * @covers \App\Observers\TagObserver::forceDeleted
     * @covers \App\Services\TagService::decrementRecordTagCount
     */
    public function force_deleting_tag_should_decrement_cached_field()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $record = factory(Record::class)->create();
        $tag = factory(Tag::class)->create();

        $tag->records()->save($record);
        $record->refresh();

        $this->assertEquals(1, $record->cache_tag_count);

        $tag->forceDelete();
        $record->refresh();

        $this->assertEquals(0, $record->cache_tag_count);
    }

    /**
     * Tags must be able to find their aliases (tags aliased to this tag).
     *
     * @test
     * @covers \App\Models\Tag::aliases
     */
    public function tags_can_find_their_aliases()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $tag = factory(Tag::class)->create();
        $alias = factory(Tag::class)->create();

        $this->assertTrue(Tag::first()->aliases->isEmpty());

        $tag->aliases()->save($alias);

        $this->assertEquals(1, Tag::first()->aliases()->count());
        $this->assertEquals($alias->id, Tag::first()->aliases()->first()->id);
    }

    /**
     * Tags must be able to find their alias target (tag to which this tag is aliased).
     *
     * @test
     * @covers \App\Models\Tag::aliased_to
     */
    public function tags_can_find_their_alias_target()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $tag = factory(Tag::class)->create();
        $alias = factory(Tag::class)->create();

        $this->assertNull(Tag::find($alias->id)->aliased_to);

        $tag->aliases()->save($alias);

        $this->assertEquals($tag->id, Tag::find($alias->id)->aliased_to->id);
    }

    /**
     * When aliased to another Tag, this Tag's Records must be moved to the alias,
     * if they do not already exist.
     *
     * @test
     * @covers \App\Services\TagService::doAliasSideEffects
     * @covers \App\Observers\TagObserver::updating
     */
    public function aliased_tags_move_their_records()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $tag1 = factory(Tag::class)->create();
        $tag2 = factory(Tag::class)->create();

        $tag1_records = factory(Record::class, 10)->create();
        $tag2_records = factory(Record::class, 5)->create();

        $tag1_records_count = $tag1_records->count();
        $tag2_records_count = $tag2_records->count();
        $total_records = $tag1_records_count + $tag2_records_count;

        $tag1->records()->saveMany($tag1_records);
        $tag2->records()->saveMany($tag2_records);

        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->records()->count());
        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->cache_record_count);
        $this->assertEquals($tag2_records_count, Tag::find($tag2->id)->records()->count());
        $this->assertEquals($tag2_records_count, Tag::find($tag2->id)->cache_record_count);

        $tag1->aliases()->save($tag2);

        $this->assertEquals($total_records, Tag::find($tag1->id)->records()->count());
        $this->assertEquals($total_records, Tag::find($tag1->id)->cache_record_count);
        $this->assertEquals(0, Tag::find($tag2->id)->records()->count());
        $this->assertEquals(0, Tag::find($tag2->id)->cache_record_count);
    }

    /**
     * When aliased to another Tag, this Tag's Records must update their cache_tag_count fields.
     *
     * @test
     * @covers \App\Services\TagService::doAliasSideEffects
     * @covers \App\Observers\TagObserver::updating
     */
    public function aliased_tag_records_update_cache_tag_counts()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $tag1 = factory(Tag::class)->create();
        $tag2 = factory(Tag::class)->create();

        $tag1_records = factory(Record::class, 10)->create();
        $tag2_records = factory(Record::class, 5)->create();
        $shared_records = factory(Record::class, 4)->create();

        $tag1_records_ids = $tag1_records->pluck('id')->toArray();
        $tag2_records_ids = $tag2_records->pluck('id')->toArray();
        $shared_records_ids = $shared_records->pluck('id')->toArray();

        $tag1->records()->saveMany($tag1_records);
        $tag1->records()->saveMany($shared_records);
        $tag2->records()->saveMany($tag2_records);
        $tag2->records()->saveMany($shared_records);

        foreach(Record::whereIn('id', $tag1_records_ids)->get() as $record)
        {
            $this->assertEquals(1, $record->tags()->count());
            $this->assertEquals(1, $record->cache_tag_count);
        }

        foreach(Record::whereIn('id', $tag2_records_ids)->get() as $record)
        {
            $this->assertEquals(1, $record->tags()->count());
            $this->assertEquals(1, $record->cache_tag_count);
        }

        foreach(Record::whereIn('id', $shared_records_ids)->get() as $record)
        {
            $this->assertEquals(2, $record->tags()->count());
            $this->assertEquals(2, $record->cache_tag_count);
        }

        $tag1->aliases()->save($tag2);

        foreach(Record::whereIn('id', $tag1_records_ids)->get() as $record)
        {
            // The alias_target tag's records should be unchanged.
            $this->assertEquals(1, $record->tags()->count());
            $this->assertEquals(1, $record->cache_tag_count);
        }

        foreach(Record::whereIn('id', $tag2_records_ids)->get() as $record)
        {
            // The aliased tag's records should be the same after incrementing and decrementing.
            $this->assertEquals(1, $record->tags()->count());
            $this->assertEquals(1, $record->cache_tag_count);
        }

        foreach(Record::whereIn('id', $shared_records_ids)->get() as $record)
        {
            // The shared records should be decremented.
            $this->assertEquals(1, $record->tags()->count());
            $this->assertEquals(1, $record->cache_tag_count);
        }
    }

    /**
     * When an aliased tag is re-aliased, no side-effects should happen.
     *
     * @test
     * @covers \App\Services\TagService::doAliasSideEffects
     * @covers \App\Observers\TagObserver::updating
     */
    public function realiased_tag_doesnt_affect_records_or_counts()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $tag1 = factory(Tag::class)->create();
        $tag2 = factory(Tag::class)->create();
        $tag3 = factory(Tag::class)->create();

        $tag1_records = factory(Record::class, 10)->create();
        $tag3_records = factory(Record::class, 10)->create();

        $tag1_records_ids = $tag1_records->pluck('id')->toArray();
        $tag1_records_count = $tag1_records->count();
        $tag3_records_ids = $tag3_records->pluck('id')->toArray();
        $tag3_records_count = $tag3_records->count();

        $tag1->records()->saveMany($tag1_records);
        $tag3->records()->saveMany($tag3_records);

        $tag1->aliases()->save($tag2);

        foreach(Record::whereIn('id', $tag1_records_ids)->get() as $record)
        {
            $this->assertEquals(1, $record->tags()->count());
            $this->assertEquals(1, $record->cache_tag_count);
        }

        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->records()->count());
        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->cache_record_count);

        foreach(Record::whereIn('id', $tag3_records_ids)->get() as $record)
        {
            $this->assertEquals(1, $record->tags()->count());
            $this->assertEquals(1, $record->cache_tag_count);
        }

        $this->assertEquals($tag3_records_count, Tag::find($tag3->id)->records()->count());
        $this->assertEquals($tag3_records_count, Tag::find($tag3->id)->cache_record_count);

        $this->assertEquals(0, Tag::find($tag2->id)->records()->count());
        $this->assertEquals(0, Tag::find($tag2->id)->cache_record_count);



        $tag3->aliases()->save($tag2);



        foreach(Record::whereIn('id', $tag1_records_ids)->get() as $record)
        {
            // The alias_target tag's records should be unchanged.
            $this->assertEquals(1, $record->tags()->count());
            $this->assertEquals(1, $record->cache_tag_count);
        }

        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->records()->count());
        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->cache_record_count);

        foreach(Record::whereIn('id', $tag3_records_ids)->get() as $record)
        {
            $this->assertEquals(1, $record->tags()->count());
            $this->assertEquals(1, $record->cache_tag_count);
        }

        $this->assertEquals($tag3_records_count, Tag::find($tag3->id)->records()->count());
        $this->assertEquals($tag3_records_count, Tag::find($tag3->id)->cache_record_count);

        $this->assertEquals(0, Tag::find($tag2->id)->records()->count());
        $this->assertEquals(0, Tag::find($tag2->id)->cache_record_count);
    }

    /**
     * When an aliased tag is un-aliased, no side-effects should happen.
     *
     * @test
     * @covers \App\Services\TagService::doAliasSideEffects
     * @covers \App\Observers\TagObserver::updating
     */
    public function unaliased_tag_doesnt_affect_records_or_counts()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $tag1 = factory(Tag::class)->create();
        $tag2 = factory(Tag::class)->create();

        $tag1_records = factory(Record::class, 10)->create();

        $tag1_records_ids = $tag1_records->pluck('id')->toArray();
        $tag1_records_count = $tag1_records->count();

        $tag1->records()->saveMany($tag1_records);

        $tag1->aliases()->save($tag2);

        foreach(Record::whereIn('id', $tag1_records_ids)->get() as $record)
        {
            $this->assertEquals(1, $record->tags()->count());
            $this->assertEquals(1, $record->cache_tag_count);
        }

        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->records()->count());
        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->cache_record_count);

        $this->assertEquals(0, Tag::find($tag2->id)->records()->count());
        $this->assertEquals(0, Tag::find($tag2->id)->cache_record_count);



        $tag2->aliased_to_tag_id = null;
        $tag2->save();



        foreach(Record::whereIn('id', $tag1_records_ids)->get() as $record)
        {
            // The alias_target tag's records should be unchanged.
            $this->assertEquals(1, $record->tags()->count());
            $this->assertEquals(1, $record->cache_tag_count);
        }

        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->records()->count());
        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->cache_record_count);

        $this->assertEquals(0, Tag::find($tag2->id)->records()->count());
        $this->assertEquals(0, Tag::find($tag2->id)->cache_record_count);
    }
}

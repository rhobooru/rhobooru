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
        $record = factory(Record::class)->states('approved')->create();
        $tag = factory(Tag::class)->create();

        $this->assertEquals(0, $tag->records()->count());

        $tag->records()->save($record);

        $tag->refresh();

        $this->assertEquals(1, $tag->records()->count());
        $this->assertEquals($record->id, Tag::find($tag->id)->records()->first()->id);
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
     * @covers \App\Models\Tag::doAliasSideEffects
     * @covers \App\Observers\TagObserver::updating
     */
    public function aliased_tags_move_their_records()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $tag1 = factory(Tag::class)->create();
        $tag2 = factory(Tag::class)->create();

        $tag1_records = factory(Record::class, 10)->states('approved')->create();
        $tag2_records = factory(Record::class, 5)->states('approved')->create();

        $tag1_records_count = $tag1_records->count();
        $tag2_records_count = $tag2_records->count();
        $total_records = $tag1_records_count + $tag2_records_count;

        $tag1->records()->saveMany($tag1_records);
        $tag2->records()->saveMany($tag2_records);

        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->records()->count());
        $this->assertEquals($tag2_records_count, Tag::find($tag2->id)->records()->count());

        $tag1->aliases()->save($tag2);

        $this->assertEquals($total_records, Tag::find($tag1->id)->records()->count());
        $this->assertEquals(0, Tag::find($tag2->id)->records()->count());
    }

    /**
     * When an aliased tag is re-aliased, no side-effects should happen.
     *
     * @test
     * @covers \App\Models\Tag::doAliasSideEffects
     * @covers \App\Observers\TagObserver::updating
     */
    public function realiased_tag_doesnt_affect_records_or_counts()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $tag1 = factory(Tag::class)->create();
        $tag2 = factory(Tag::class)->create();
        $tag3 = factory(Tag::class)->create();

        $tag1_records = factory(Record::class, 10)->states('approved')->create();
        $tag3_records = factory(Record::class, 10)->states('approved')->create();

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
        }

        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->records()->count());

        foreach(Record::whereIn('id', $tag3_records_ids)->get() as $record)
        {
            $this->assertEquals(1, $record->tags()->count());
        }

        $this->assertEquals($tag3_records_count, Tag::find($tag3->id)->records()->count());

        $this->assertEquals(0, Tag::find($tag2->id)->records()->count());



        $tag3->aliases()->save($tag2);



        foreach(Record::whereIn('id', $tag1_records_ids)->get() as $record)
        {
            // The alias_target tag's records should be unchanged.
            $this->assertEquals(1, $record->tags()->count());
        }

        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->records()->count());

        foreach(Record::whereIn('id', $tag3_records_ids)->get() as $record)
        {
            $this->assertEquals(1, $record->tags()->count());
        }

        $this->assertEquals($tag3_records_count, Tag::find($tag3->id)->records()->count());

        $this->assertEquals(0, Tag::find($tag2->id)->records()->count());
    }

    /**
     * When an aliased tag is un-aliased, no side-effects should happen.
     *
     * @test
     * @covers \App\Models\Tag::doAliasSideEffects
     * @covers \App\Observers\TagObserver::updating
     */
    public function unaliased_tag_doesnt_affect_records_or_counts()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $tag1 = factory(Tag::class)->create();
        $tag2 = factory(Tag::class)->create();

        $tag1_records = factory(Record::class, 10)->states('approved')->create();

        $tag1_records_ids = $tag1_records->pluck('id')->toArray();
        $tag1_records_count = $tag1_records->count();

        $tag1->records()->saveMany($tag1_records);

        $tag1->aliases()->save($tag2);

        foreach(Record::whereIn('id', $tag1_records_ids)->get() as $record)
        {
            $this->assertEquals(1, $record->tags()->count());
        }

        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->records()->count());

        $this->assertEquals(0, Tag::find($tag2->id)->records()->count());



        $tag2->aliased_to_tag_id = null;
        $tag2->save();



        foreach(Record::whereIn('id', $tag1_records_ids)->get() as $record)
        {
            // The alias_target tag's records should be unchanged.
            $this->assertEquals(1, $record->tags()->count());
        }

        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->records()->count());

        $this->assertEquals(0, Tag::find($tag2->id)->records()->count());
    }

    /**
     * Updating an aliased tag in an unrelated way shouldn't affect alias.
     *
     * @test
     * @covers \App\Models\Tag::doAliasSideEffects
     */
    public function updating_aliased_tag_does_nothing()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $tag1 = factory(Tag::class)->create();
        $tag2 = factory(Tag::class)->create();

        $tag1_records = factory(Record::class, 10)->states('approved')->create();

        $tag1_records_ids = $tag1_records->pluck('id')->toArray();
        $tag1_records_count = $tag1_records->count();

        $tag1->records()->saveMany($tag1_records);

        $tag1->aliases()->save($tag2);

        foreach(Record::whereIn('id', $tag1_records_ids)->get() as $record)
        {
            $this->assertEquals(1, $record->tags()->count());
        }

        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->records()->count());

        $this->assertEquals(0, Tag::find($tag2->id)->records()->count());



        $tag2->name = 'update_test';
        $tag2->save();
        $tag2->doAliasSideEffects();



        foreach(Record::whereIn('id', $tag1_records_ids)->get() as $record)
        {
            $this->assertEquals(1, $record->tags()->count());
        }

        $this->assertEquals($tag1_records_count, Tag::find($tag1->id)->records()->count());

        $this->assertEquals(0, Tag::find($tag2->id)->records()->count());
    }
}

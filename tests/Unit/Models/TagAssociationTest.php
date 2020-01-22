<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Data;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\SeedsDefaultValues;
use \App\Models\TagAssociation;
use \App\Models\TagAssociationType;
use \App\Models\Tag;

class TagAssociationTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * Tag Associations can find their Tag Association Type.
     *
     * @test
     * @covers \App\Models\TagAssociation::tag_association_type
     */
    public function tag_associations_can_find_their_tag_association_type()
    {
        $type = factory(TagAssociationType::class)->create();

        $association = factory(TagAssociation::class)->create([
            'tag_association_type_id' => $type->id,
        ]);

        $association->refresh();

        $this->assertEquals($type->id, $association->tag_association_type->id);
    }

    /**
     * Tag Associations can find their left Tag.
     *
     * @test
     * @covers \App\Models\TagAssociation::tag1
     */
    public function tag_associations_can_find_their_left_tag()
    {
        $tag1 = factory(Tag::class)->create();
        $tag2 = factory(Tag::class)->create();

        $association = factory(TagAssociation::class)->create([
            'tag_id_1' => $tag1->id,
            'tag_id_2' => $tag2->id,
        ]);

        $association->refresh();

        $this->assertEquals($tag1->id, $association->tag1->id);
    }

    /**
     * Tag Associations can find their right Tag.
     *
     * @test
     * @covers \App\Models\TagAssociation::tag2
     */
    public function tag_associations_can_find_their_right_tag()
    {
        $tag1 = factory(Tag::class)->create();
        $tag2 = factory(Tag::class)->create();

        $association = factory(TagAssociation::class)->create([
            'tag_id_1' => $tag1->id,
            'tag_id_2' => $tag2->id,
        ]);

        $association->refresh();

        $this->assertEquals($tag2->id, $association->tag2->id);
    }

    /**
     * Tag Associations can be limited by their Tag Association Type.
     *
     * @test
     * @covers \App\Models\TagAssociation::scopeAssociationType
     */
    public function tag_associations_can_be_limited_by_their_tag_association_type()
    {
        $type = factory(TagAssociationType::class)->create();

        $association = factory(TagAssociation::class, 10)->create([
            'tag_association_type_id' => $type->id,
        ])->first();

        $association->refresh();

        $this->assertEquals(10, TagAssociation::AssociationType($type->id)->count());
        $this->assertEquals($association->id, TagAssociation::AssociationType($type->id)->first()->id);
    }
}

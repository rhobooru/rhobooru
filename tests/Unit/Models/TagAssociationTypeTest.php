<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Data;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\SeedsDefaultValues;
use \App\Models\TagAssociation;
use \App\Models\TagAssociationType;

class TagAssociationTypeTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * Tag Association Types can find their Tag Associations.
     *
     * @test
     * @covers \App\Models\TagAssociationType::tag_associations
     */
    public function tag_association_types_can_find_their_tag_associations()
    {
        $type = factory(TagAssociationType::class)->create();

        $association = factory(TagAssociation::class, 10)->create([
            'tag_association_type_id' => $type->id,
        ])->first();

        $type->refresh();

        $this->assertEquals(10, $type->tag_associations()->count());
        $this->assertEquals($association->id, $type->tag_associations->first()->id);
    }
}

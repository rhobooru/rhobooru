<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Data;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\SeedsDefaultValues;
use \App\Models\AccessType;

class AccessTypeTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * Access Types can find their folders.
     *
     * @test
     * @covers \App\Models\AccessType::folders
     */
    public function access_types_can_find_folders()
    {
        $access_type = factory(AccessType::class)->create();

        $folder = factory(\App\Models\Folder::class)->create([
            'access_type_id' => $access_type->id,
        ]);

        $access_type->refresh();

        $this->assertEquals(1, $access_type->folders()->count());
        $this->assertEquals($folder->id, $access_type->folders()->first()->id);
    }

    /**
     * Can limit queries to public AccessTypes.
     *
     * @test
     * @covers \App\Models\AccessType::scopePublic
     */
    public function can_find_public_access_type()
    {
        $access_type = AccessType::public();

        $this->assertEquals(1, $access_type->count());
        $this->assertEquals(AccessType::where('static_name', AccessType::PUBLIC_ACCESS)->first()->id, 
            $access_type->first()->id);
    }

    /**
     * Can limit queries to unlisted AccessTypes.
     *
     * @test
     * @covers \App\Models\AccessType::scopeUnlisted
     */
    public function can_find_unlisted_access_type()
    {
        $access_type = AccessType::unlisted();

        $this->assertEquals(1, $access_type->count());
        $this->assertEquals(AccessType::where('static_name', AccessType::UNLISTED_ACCESS)->first()->id, 
            $access_type->first()->id);
    }

    /**
     * Can limit queries to friends AccessTypes.
     *
     * @test
     * @covers \App\Models\AccessType::scopeFriends
     */
    public function can_find_friends_access_type()
    {
        $access_type = AccessType::friends();

        $this->assertEquals(1, $access_type->count());
        $this->assertEquals(AccessType::where('static_name', AccessType::FRIENDS_ACCESS)->first()->id, 
            $access_type->first()->id);
    }

    /**
     * Can limit queries to private AccessTypes.
     *
     * @test
     * @covers \App\Models\AccessType::scopePrivate
     */
    public function can_find_private_access_type()
    {
        $access_type = AccessType::private();

        $this->assertEquals(1, $access_type->count());
        $this->assertEquals(AccessType::where('static_name', AccessType::PRIVATE_ACCESS)->first()->id, 
            $access_type->first()->id);
    }
}

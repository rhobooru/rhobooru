<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Data;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\SeedsDefaultValues;
use \App\Models\Folder;
use \App\Models\FolderType;
use \App\Models\AccessType;

class FolderTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * Folders can find their Access Type.
     *
     * @test
     * @covers \App\Models\Folder::access_type
     */
    public function folders_can_find_their_access_type()
    {
        $access_type = factory(AccessType::class)->create();

        $folder = factory(Folder::class)->create([
            'access_type_id' => $access_type->id,
        ]);

        $folder->refresh();

        $this->assertEquals($access_type->id, $folder->access_type->id);
    }

    /**
     * Folders can find their Folder Type.
     *
     * @test
     * @covers \App\Models\Folder::folder_type
     */
    public function folders_can_find_their_folder_type()
    {
        $folder_type = factory(FolderType::class)->create();

        $folder = factory(Folder::class)->create([
            'folder_type_id' => $folder_type->id,
        ]);

        $folder->refresh();

        $this->assertEquals($folder_type->id, $folder->folder_type->id);
    }

    /**
     * Can limit queries to public folders.
     *
     * @test
     * @covers \App\Models\Folder::scopePublic
     */
    public function can_find_public_folders()
    {   
        foreach(Folder::all() as $folder)
        {
            $folder->forceDelete();
        }

        $public = factory(Folder::class)->create([
            'access_type_id' => AccessType::public()->first()->id,
        ]);

        $private = factory(Folder::class)->create([
            'access_type_id' => AccessType::private()->first()->id,
        ]);

        $friends = factory(Folder::class)->create([
            'access_type_id' => AccessType::friends()->first()->id,
        ]);

        $unlisted = factory(Folder::class)->create([
            'access_type_id' => AccessType::unlisted()->first()->id,
        ]);


        $this->assertEquals(1, Folder::public()->count());
        $this->assertEquals($public->id, Folder::public()->first()->id);
    }

    /**
     * Can limit queries to private folders.
     *
     * @test
     * @covers \App\Models\Folder::scopePrivate
     */
    public function can_find_private_folders()
    {   
        foreach(Folder::all() as $folder)
        {
            $folder->forceDelete();
        }

        $public = factory(Folder::class)->create([
            'access_type_id' => AccessType::public()->first()->id,
        ]);

        $private = factory(Folder::class)->create([
            'access_type_id' => AccessType::private()->first()->id,
        ]);

        $friends = factory(Folder::class)->create([
            'access_type_id' => AccessType::friends()->first()->id,
        ]);

        $unlisted = factory(Folder::class)->create([
            'access_type_id' => AccessType::unlisted()->first()->id,
        ]);


        $this->assertEquals(1, Folder::private()->count());
        $this->assertEquals($private->id, Folder::private()->first()->id);
    }

    /**
     * Can limit queries to friends folders.
     *
     * @test
     * @covers \App\Models\Folder::scopeFriends
     */
    public function can_find_friends_folders()
    {   
        foreach(Folder::all() as $folder)
        {
            $folder->forceDelete();
        }

        $public = factory(Folder::class)->create([
            'access_type_id' => AccessType::public()->first()->id,
        ]);

        $private = factory(Folder::class)->create([
            'access_type_id' => AccessType::private()->first()->id,
        ]);

        $friends = factory(Folder::class)->create([
            'access_type_id' => AccessType::friends()->first()->id,
        ]);

        $unlisted = factory(Folder::class)->create([
            'access_type_id' => AccessType::unlisted()->first()->id,
        ]);


        $this->assertEquals(1, Folder::friends()->count());
        $this->assertEquals($friends->id, Folder::friends()->first()->id);
    }

    /**
     * Can limit queries to unlisted folders.
     *
     * @test
     * @covers \App\Models\Folder::scopeUnlisted
     */
    public function can_find_unlisted_folders()
    {   
        foreach(Folder::all() as $folder)
        {
            $folder->forceDelete();
        }

        $public = factory(Folder::class)->create([
            'access_type_id' => AccessType::public()->first()->id,
        ]);

        $private = factory(Folder::class)->create([
            'access_type_id' => AccessType::private()->first()->id,
        ]);

        $friends = factory(Folder::class)->create([
            'access_type_id' => AccessType::friends()->first()->id,
        ]);

        $unlisted = factory(Folder::class)->create([
            'access_type_id' => AccessType::unlisted()->first()->id,
        ]);


        $this->assertEquals(1, Folder::unlisted()->count());
        $this->assertEquals($unlisted->id, Folder::unlisted()->first()->id);
    }

    /**
     * Can limit queries to favorites folders.
     *
     * @test
     * @covers \App\Models\Folder::scopeFavorites
     */
    public function can_find_favorites_folders()
    {   
        foreach(Folder::all() as $folder)
        {
            $folder->forceDelete();
        }

        $favorites = factory(Folder::class)->create([
            'folder_type_id' => FolderType::favorites()->first()->id,
        ]);

        $quick_list = factory(Folder::class)->create([
            'folder_type_id' => FolderType::quickList()->first()->id,
        ]);

        $book = factory(Folder::class)->create([
            'folder_type_id' => FolderType::book()->first()->id,
        ]);

        $generic = factory(Folder::class)->create([
            'folder_type_id' => FolderType::generic()->first()->id,
        ]);


        $this->assertEquals(1, Folder::favorites()->count());
        $this->assertEquals($favorites->id, Folder::favorites()->first()->id);
    }

    /**
     * Can limit queries to quick list folders.
     *
     * @test
     * @covers \App\Models\Folder::scopeQuickList
     */
    public function can_find_quick_list_folders()
    {   
        foreach(Folder::all() as $folder)
        {
            $folder->forceDelete();
        }

        $favorites = factory(Folder::class)->create([
            'folder_type_id' => FolderType::favorites()->first()->id,
        ]);

        $quick_list = factory(Folder::class)->create([
            'folder_type_id' => FolderType::quickList()->first()->id,
        ]);

        $book = factory(Folder::class)->create([
            'folder_type_id' => FolderType::book()->first()->id,
        ]);

        $generic = factory(Folder::class)->create([
            'folder_type_id' => FolderType::generic()->first()->id,
        ]);


        $this->assertEquals(1, Folder::quickList()->count());
        $this->assertEquals($quick_list->id, Folder::quickList()->first()->id);
    }

    /**
     * Can limit queries to book folders.
     *
     * @test
     * @covers \App\Models\Folder::scopeBooks
     */
    public function can_find_book_folders()
    {   
        foreach(Folder::all() as $folder)
        {
            $folder->forceDelete();
        }

        $favorites = factory(Folder::class)->create([
            'folder_type_id' => FolderType::favorites()->first()->id,
        ]);

        $quick_list = factory(Folder::class)->create([
            'folder_type_id' => FolderType::quickList()->first()->id,
        ]);

        $book = factory(Folder::class)->create([
            'folder_type_id' => FolderType::book()->first()->id,
        ]);

        $generic = factory(Folder::class)->create([
            'folder_type_id' => FolderType::generic()->first()->id,
        ]);


        $this->assertEquals(1, Folder::books()->count());
        $this->assertEquals($book->id, Folder::books()->first()->id);
    }

    /**
     * Can limit queries to generic folders.
     *
     * @test
     * @covers \App\Models\Folder::scopeGeneric
     */
    public function can_find_generic_folders()
    {   
        foreach(Folder::all() as $folder)
        {
            $folder->forceDelete();
        }

        $favorites = factory(Folder::class)->create([
            'folder_type_id' => FolderType::favorites()->first()->id,
        ]);

        $quick_list = factory(Folder::class)->create([
            'folder_type_id' => FolderType::quickList()->first()->id,
        ]);

        $book = factory(Folder::class)->create([
            'folder_type_id' => FolderType::book()->first()->id,
        ]);

        $generic = factory(Folder::class)->create([
            'folder_type_id' => FolderType::generic()->first()->id,
        ]);


        $this->assertEquals(1, Folder::generic()->count());
        $this->assertEquals($generic->id, Folder::generic()->first()->id);
    }
}

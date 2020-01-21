<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Data;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\SeedsDefaultValues;
use \App\Models\FolderType;
use \App\Models\Folder;

class FolderTypeTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * Folder Types can find their folders.
     *
     * @test
     * @covers \App\Models\FolderType::folders
     */
    public function folder_types_can_find_folders()
    {
        $folder_type = factory(FolderType::class)->create();

        $folder = factory(Folder::class)->create([
            'folder_type_id' => $folder_type->id,
        ]);

        $folder_type->refresh();

        $this->assertEquals(1, $folder_type->folders()->count());
        $this->assertEquals($folder->id, $folder_type->folders()->first()->id);
    }

    /**
     * Can limit queries to favorite FolderTypes.
     *
     * @test
     * @covers \App\Models\FolderType::scopeFavorites
     */
    public function can_find_favorites_folder_type()
    {
        $folder_type = FolderType::favorites();

        $this->assertEquals(1, $folder_type->count());
        $this->assertEquals(FolderType::where('static_name', FolderType::FAVORITES_FOLDER_TYPE)->first()->id, 
            $folder_type->first()->id);
    }

    /**
     * Can limit queries to quick list FolderTypes.
     *
     * @test
     * @covers \App\Models\FolderType::scopeQuickList
     */
    public function can_find_quick_list_folder_type()
    {
        $folder_type = FolderType::quickList();

        $this->assertEquals(1, $folder_type->count());
        $this->assertEquals(FolderType::where('static_name', FolderType::QUICK_LIST_FOLDER_TYPE)->first()->id, 
            $folder_type->first()->id);
    }

    /**
     * Can limit queries to book FolderTypes.
     *
     * @test
     * @covers \App\Models\FolderType::scopeBook
     */
    public function can_find_book_folder_type()
    {
        $folder_type = FolderType::book();

        $this->assertEquals(1, $folder_type->count());
        $this->assertEquals(FolderType::where('static_name', FolderType::BOOK_FOLDER_TYPE)->first()->id, 
            $folder_type->first()->id);
    }

    /**
     * Can limit queries to generic FolderTypes.
     *
     * @test
     * @covers \App\Models\FolderType::scopeGeneric
     */
    public function can_find_generic_folder_type()
    {
        $folder_type = FolderType::generic();

        $this->assertEquals(1, $folder_type->count());
        $this->assertEquals(FolderType::where('static_name', FolderType::GENERIC_FOLDER_TYPE)->first()->id, 
            $folder_type->first()->id);
    }

    /**
     * Can limit queries to manually managed FolderTypes.
     *
     * @test
     * @covers \App\Models\FolderType::scopeManagedManually
     */
    public function can_find_manually_managed_folder_type()
    {
        $folder_type = factory(FolderType::class)->create([
            'can_be_managed_manually' => true,
        ]);

        $folder_type_found = FolderType::managedManually()->where('id', $folder_type->id);

        $this->assertEquals($folder_type->id, $folder_type_found->first()->id);
    }
}

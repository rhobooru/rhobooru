<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\SeedsDefaultValues;
use Tests\TestCase;
use \App\Models\User;
use \App\Models\Profile;
use \App\Models\Folder;
use \App\Models\FolderType;
use \App\Models\AccessType;

class UserTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * Profiles must be able to find their site theme.
     *
     * @test
     * @covers \App\Models\Profile::site_theme
     */
    // public function profile_can_find_site_theme()
    // {
    //     $relation_id = \App\Models\SiteTheme::where('is_default', true)->first()->id;

    //     $profile_id = factory(\App\Models\Profile::class)->create([
    //         'site_theme_id' => $relation_id,
    //     ])->user_id;

    //     $this->assertInstanceOf(\App\Models\SiteTheme::class, Profile::find($profile_id)->site_theme);
    //     $this->assertEquals($relation_id, Profile::find($profile_id)->site_theme->id);
    // }

    /**
     * Profiles must be able to find their date format.
     *
     * @test
     * @covers \App\Models\Profile::date_format
     */
    // public function profile_can_find_date_format()
    // {
    //     $relation_id = \App\Models\DateFormat::where('is_default', true)->first()->id;

    //     $profile_id = factory(\App\Models\Profile::class)->create([
    //         'date_format_id' => $relation_id,
    //     ])->user_id;

    //     $this->assertInstanceOf(\App\Models\DateFormat::class, Profile::find($profile_id)->date_format);
    //     $this->assertEquals($relation_id, Profile::find($profile_id)->date_format->id);
    // }

    /**
     * Profiles must be able to find their record fit.
     *
     * @test
     * @covers \App\Models\Profile::record_fit
     */
    // public function profile_can_find_record_fit()
    // {
    //     $relation_id = \App\Models\RecordFit::where('is_default', true)->first()->id;

    //     $profile_id = factory(\App\Models\Profile::class)->create([
    //         'record_fit_id' => $relation_id,
    //     ])->user_id;

    //     $this->assertInstanceOf(\App\Models\RecordFit::class, Profile::find($profile_id)->record_fit);
    //     $this->assertEquals($relation_id, Profile::find($profile_id)->record_fit->id);
    // }

    /**
     * Profiles must be able to find their max content rating.
     *
     * @test
     * @covers \App\Models\Profile::max_content_rating
     */
    // public function profile_can_find_max_content_rating()
    // {
    //     $relation_id = \App\Models\ContentRating::orderBy('order','desc')->first()->id;

    //     $profile_id = factory(\App\Models\Profile::class)->create([
    //         'maximum_content_rating_id' => $relation_id,
    //     ])->user_id;

    //     $this->assertInstanceOf(\App\Models\ContentRating::class, Profile::find($profile_id)->max_content_rating);
    //     $this->assertEquals($relation_id, Profile::find($profile_id)->max_content_rating->id);
    // }

    /**
     * Creating a User should create a favorites folder.
     *
     * @test
     * @covers \App\Observers\UserObserver::created
     * @covers \App\Models\User::createUserRelationships
     * @covers \App\Models\User::createUserRelationships
     * @covers \App\Models\User::createUserFavoritesFolder
     */
    public function creating_user_creates_favorites_folder()
    {
        $user = factory(User::class)->create();

        $user->refresh();

        $this->assertEquals(1, Folder::createdBy($user->id)->favorites()->count());
        $this->assertInstanceOf(Folder::class, $user->favoritesFolder);
        $this->assertEquals(Folder::createdBy($user->id)->favorites()->first()->id, $user->favoritesFolder->id);
    }

    /**
     * Creating a User should create a quick list folder.
     *
     * @test
     * @covers \App\Observers\UserObserver::created
     * @covers \App\Models\User::createUserRelationships
     * @covers \App\Models\User::createUserRelationships
     * @covers \App\Models\User::createUserQuickListFolder
     */
    public function creating_user_creates_quick_list_folder()
    {
        $user = factory(User::class)->create();

        $user->refresh();

        $this->assertEquals(1, Folder::createdBy($user->id)->quickList()->count());
        $this->assertInstanceOf(Folder::class, $user->quickListFolder);
        $this->assertEquals(Folder::createdBy($user->id)->quickList()->first()->id, $user->quickListFolder->id);
    }

    /**
     * Force deleting a User should force delete its favorites folder.
     *
     * @test
     * @covers \App\Observers\UserObserver::deleting
     * @covers \App\Observers\UserObserver::forceDeleting
     * @covers \App\Models\User::forceDeleteUserRelationships
     */
    public function force_deleting_user_deletes_favorites_folder()
    {
        $user = factory(User::class)->create();

        $user->forceDelete();

        $this->assertEquals(0, Folder::withTrashed()->createdBy($user->id)->favorites()->count());
        $this->assertNull($user->favoritesFolder);
    }

    /**
     * Force deleting a User should force delete its quick list folder.
     *
     * @test
     * @covers \App\Observers\UserObserver::deleting
     * @covers \App\Observers\UserObserver::forceDeleting
     * @covers \App\Models\User::forceDeleteUserRelationships
     */
    public function force_deleting_user_deletes_quick_list_folder()
    {
        $user = factory(User::class)->create();

        $user->forceDelete();

        $this->assertEquals(0, Folder::withTrashed()->createdBy($user->id)->quickList()->count());
        $this->assertNull($user->quickListFolder);
    }

    /**
     * Soft deleting a User should soft delete private and friend folders.
     *
     * @test
     * @covers \App\Observers\UserObserver::deleted
     * @covers \App\Models\User::softDeleteUserRelationships
     */
    public function soft_deleting_user_deletes_private_and_friend_folders()
    {
        $user = factory(User::class)->create();

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::generic()->first()->id,
            'access_type_id' => AccessType::private()->first()->id,
            'name' => 'Generic Test',
        ]);

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::generic()->first()->id,
            'access_type_id' => AccessType::friends()->first()->id,
            'name' => 'Generic Test',
        ]);

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::book()->first()->id,
            'access_type_id' => AccessType::private()->first()->id,
            'name' => 'Generic Test',
        ]);

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::book()->first()->id,
            'access_type_id' => AccessType::friends()->first()->id,
            'name' => 'Generic Test',
        ]);

        $user->delete();

        $this->assertEquals(0, Folder::createdBy($user->id)->generic()->count());
        $this->assertEquals(0, Folder::createdBy($user->id)->books()->count());
    }

    /**
     * Soft deleting a User should not soft delete public and unlisted folders.
     *
     * @test
     * @covers \App\Observers\UserObserver::deleted
     * @covers \App\Models\User::softDeleteUserRelationships
     */
    public function soft_deleting_user_does_not_delete_public_and_unlisted_folders()
    {
        $user = factory(User::class)->create();

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::generic()->first()->id,
            'access_type_id' => AccessType::public()->first()->id,
            'name' => 'Generic Test',
        ]);

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::generic()->first()->id,
            'access_type_id' => AccessType::unlisted()->first()->id,
            'name' => 'Generic Test',
        ]);

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::book()->first()->id,
            'access_type_id' => AccessType::public()->first()->id,
            'name' => 'Generic Test',
        ]);

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::book()->first()->id,
            'access_type_id' => AccessType::unlisted()->first()->id,
            'name' => 'Generic Test',
        ]);

        $user->delete();

        $this->assertEquals(2, Folder::createdBy($user->id)->generic()->count());
        $this->assertEquals(2, Folder::createdBy($user->id)->books()->count());
    }

    /**
     * Force deleting a User should force delete all folders.
     *
     * @test
     * @covers \App\Observers\UserObserver::deleting
     * @covers \App\Observers\UserObserver::forceDeleting
     * @covers \App\Models\User::forceDeleteUserRelationships
     */
    public function force_deleting_user_deletes_folders()
    {
        $user = factory(User::class)->create();

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::generic()->first()->id,
            'access_type_id' => AccessType::public()->first()->id,
            'name' => 'Generic Test',
        ]);

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::generic()->first()->id,
            'access_type_id' => AccessType::unlisted()->first()->id,
            'name' => 'Generic Test',
        ]);

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::book()->first()->id,
            'access_type_id' => AccessType::public()->first()->id,
            'name' => 'Generic Test',
        ]);

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::book()->first()->id,
            'access_type_id' => AccessType::unlisted()->first()->id,
            'name' => 'Generic Test',
        ]);

        $user->forceDelete();

        $this->assertEquals(0, Folder::withTrashed()->createdBy($user->id)->count());
    }

    /**
     * Restoring a User should restore private and friend folders.
     *
     * @test
     * @covers \App\Observers\UserObserver::restored
     * @covers \App\Models\User::restoreUserRelationships
     */
    public function restoring_user_restores_private_and_friend_folders()
    {
        $user = factory(User::class)->create();

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::generic()->first()->id,
            'access_type_id' => AccessType::private()->first()->id,
            'name' => 'Generic Test',
        ]);

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::generic()->first()->id,
            'access_type_id' => AccessType::friends()->first()->id,
            'name' => 'Generic Test',
        ]);

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::book()->first()->id,
            'access_type_id' => AccessType::private()->first()->id,
            'name' => 'Generic Test',
        ]);

        Folder::create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::book()->first()->id,
            'access_type_id' => AccessType::friends()->first()->id,
            'name' => 'Generic Test',
        ]);

        $user->delete();

        $user->restore();

        $this->assertEquals(2, Folder::createdBy($user->id)->generic()->count());
        $this->assertEquals(2, Folder::createdBy($user->id)->books()->count());
    }
}

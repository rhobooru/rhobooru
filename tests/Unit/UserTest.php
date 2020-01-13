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
     * Users must be able to find their profile.
     *
     * @test
     * @covers \App\Models\User::profile
     */
    public function user_can_find_profile()
    {
        $user = factory(User::class)->create();
        $user->profile->delete();
        $user_id = $user->id;

        $profile_id = factory(Profile::class)->create([
            'user_id' => $user_id,
        ])->user_id;

        $this->assertInstanceOf(Profile::class, User::find($user_id)->profile);
        $this->assertEquals($profile_id, User::find($user_id)->profile->user_id);
    }

    /**
     * Creating a User should create a Profile
     *
     * @test
     * @covers \App\Observers\UserObserver::created
     * @covers \App\Services\UserObserver::createUserRelationships
     * @covers \App\Services\UserObserver::createUserProfile
     */
    public function creating_user_creates_profile()
    {
        $user = factory(User::class)->create();

        $user->refresh();

        $this->assertEquals(1, Profile::where('user_id', $user->id)->count());
        $this->assertInstanceOf(Profile::class, $user->profile);
    }

    /**
     * Force deleting a user should delete its profile.
     *
     * @test
     * @covers \App\Observers\UserObserver::forceDeleted
     * @covers \App\Services\UserObserver::forceDeleteUserRelationships
     */
    public function force_deleting_user_deletes_profile()
    {
        $user = factory(User::class)->create();
        $user_id = $user->id;

        $profile = $user->profile;
        $profile_id = $profile->id;

        $user->forceDelete();

        $this->assertEquals(0, Profile::where('user_id', $user_id)->count());
        $this->assertNull(Profile::find($profile_id));
    }

    /**
     * Soft deleting a user should not delete its profile.
     *
     * @test
     * @covers \App\Observers\UserObserver::deleted
     * @covers \App\Services\UserObserver::softDeleteUserRelationships
     */
    public function soft_deleting_user_does_not_delete_profile()
    {
        $user = factory(User::class)->create();
        $user_id = $user->id;

        $profile = $user->profile;
        $profile_id = $profile->user_id;

        $user->delete();

        $this->assertEquals(1, Profile::where('user_id', $user_id)->count());
        $this->assertEquals($user_id, Profile::find($profile_id)->user_id);
    }

    /**
     * Creating a User should create a favorites folder.
     *
     * @test
     * @covers \App\Observers\UserObserver::created
     * @covers \App\Services\UserObserver::createUserRelationships
     * @covers \App\Services\UserObserver::createUserFavoritesFolder
     */
    public function creating_user_creates_favorites_folder()
    {
        $user = factory(User::class)->create();

        $user->refresh();

        $this->assertEquals(1, Folder::createdBy($user->id)->favorites()->count());
        $this->assertInstanceOf(Folder::class, $user->favoritesFolder->first());
        $this->assertEquals(Folder::createdBy($user->id)->favorites()->first()->id, $user->favoritesFolder->first()->id);
    }

    /**
     * Creating a User should create a quick list folder.
     *
     * @test
     * @covers \App\Observers\UserObserver::created
     * @covers \App\Services\UserObserver::createUserRelationships
     * @covers \App\Services\UserObserver::createUserQuickListFolder
     */
    public function creating_user_creates_quick_list_folder()
    {
        $user = factory(User::class)->create();

        $user->refresh();

        $this->assertEquals(1, Folder::createdBy($user->id)->quickList()->count());
        $this->assertInstanceOf(Folder::class, $user->quickListFolder->first());
        $this->assertEquals(Folder::createdBy($user->id)->quickList()->first()->id, $user->quickListFolder->first()->id);
    }

    /**
     * Force deleting a User should force delete its favorites folder.
     *
     * @test
     * @covers \App\Observers\UserObserver::forceDeleted
     * @covers \App\Services\UserObserver::forceDeleteUserRelationships
     */
    public function force_deleting_user_deletes_favorites_folder()
    {
        $user = factory(User::class)->create();

        $user->forceDelete();

        $this->assertEquals(0, Folder::withTrashed()->createdBy($user->id)->favorites()->count());
        $this->assertNull($user->favoritesFolder->first());
    }

    /**
     * Force deleting a User should force delete its quick list folder.
     *
     * @test
     * @covers \App\Observers\UserObserver::forceDeleted
     * @covers \App\Services\UserObserver::forceDeleteUserRelationships
     */
    public function force_deleting_user_deletes_quick_list_folder()
    {
        $user = factory(User::class)->create();

        $user->forceDelete();

        $this->assertEquals(0, Folder::withTrashed()->createdBy($user->id)->quickList()->count());
        $this->assertNull($user->quickListFolder->first());
    }

    /**
     * Soft deleting a User should soft delete private and friend folders.
     *
     * @test
     * @covers \App\Observers\UserObserver::deleted
     * @covers \App\Services\UserObserver::softDeleteUserRelationships
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
     * @covers \App\Services\UserObserver::softDeleteUserRelationships
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
     * @covers \App\Observers\UserObserver::forceDeleted
     * @covers \App\Services\UserObserver::forceDeleteUserRelationships
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
     * @covers \App\Observers\UserObserver::restore
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

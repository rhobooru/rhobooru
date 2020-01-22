<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\SeedsDefaultValues;
use Tests\TestCase;
use \App\Models\User;
use \App\Models\Profile;
use \App\Models\Folder;
use \App\Models\FolderType;
use \App\Models\AccessType;
use \App\Models\Record;

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
     * User can't have multiple favorites folders.
     *
     * @test
     * @covers \App\Models\User::createUserFavoritesFolder
     */
    public function user_cant_have_multiple_favorites_folders()
    {
        $user = factory(User::class)->create();

        $user->refresh();
        $user->createUserFavoritesFolder();

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
     * User can't have multiple quick list folders.
     *
     * @test
     * @covers \App\Models\User::createUserQuickListFolder
     */
    public function user_cant_have_multiple_quick_list_folders()
    {
        $user = factory(User::class)->create();

        $user->refresh();
        $user->createUserQuickListFolder();

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

    /**
     * Can find anonymous user.
     *
     * @test
     * @covers \App\Models\User::anonymous
     */
    public function can_find_anonymous_user()
    {
        $user = User::anonymous();

        $this->assertEquals('anonymous', $user->username);
        $this->assertTrue($user->anonymous_account);
    }

    /**
     * Can limit query to anonymous user.
     *
     * @test
     * @covers \App\Models\User::scopeIsAnonymous
     */
    public function can_limit_query_to_anonymous_user()
    {
        $user = User::isAnonymous()->first();

        $this->assertEquals('anonymous', $user->username);
        $this->assertTrue($user->anonymous_account);
    }

    /**
     * Can limit query to not anonymous user.
     *
     * @test
     * @covers \App\Models\User::scopeIsNotAnonymous
     */
    public function can_limit_query_to_not_anonymous_user()
    {
        factory(User::class, 10)->create();

        $users = User::isNotAnonymous()->get();

        foreach($users as $user)
        {
            $this->assertFalse($user->anonymous_account === null ? false : $user->anonymous_account);
        }
    }

    /**
     * Can get all permissions.
     *
     * @test
     * @covers \App\Models\User::getAllPermissionsAttribute
     */
    public function can_get_all_permissions()
    {
        $user = factory(User::class)->create();

        $permission = \Spatie\Permission\Models\Permission::create(['name' => 'test']);

        $user->givePermissionTo(['test']);

        $this->assertTrue(in_array('test', $user->allPermissions->pluck('name')->toArray()));
    }

    /**
     * Can limit query to system accounts.
     *
     * @test
     * @covers \App\Models\User::scopeSystemAccount
     */
    public function can_limit_query_to_system_accounts()
    {
        factory(User::class, 10)->create([
            'system_account' => true,
        ]);

        $users = User::systemAccount()->get();

        foreach($users as $user)
        {
            $this->assertTrue($user->system_account);
        }
    }

    /**
     * Passport can find users.
     *
     * @test
     * @covers \App\Models\User::findForPassport
     */
    public function passport_can_find_users()
    {
        $model = factory(User::class)->create([
            'username' => 'test',
        ]);

        $found = $model->findForPassport('test');

        $this->assertEquals($model->id, $found->id);
    }

    /**
     * User can get avatar URL.
     *
     * @test
     * @covers \App\Models\User::getAvatarUrlAttribute
     */
    public function user_can_get_avatar_url()
    {
        $user = factory(User::class)->create();

        $this->assertNull($user->avatarUrl);

        $user->avatar = 'test.png';
        $user->save();
        $user->refresh();

        $this->assertEquals(asset('storage/avatars/test.png'), 
            $user->avatarUrl);
    }

    /**
     * User can find folders.
     *
     * @test
     * @covers \App\Models\User::folders
     */
    public function user_can_find_folders()
    {
        $user = factory(User::class)->create();

        Folder::where('created_by_user_id', $user->id)->forceDelete();

        factory(Folder::class, 5)->create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
        ]);

        $user->refresh();

        $this->assertEquals(5, $user->folders()->count());
    }

    /**
     * User can find favorites folder.
     *
     * @test
     * @covers \App\Models\User::favoritesFolder
     */
    public function user_can_find_favorites_folder()
    {
        $user = factory(User::class)->create();

        Folder::where('created_by_user_id', $user->id)->forceDelete();

        $folder = factory(Folder::class)->create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::favorites()->first()->id,
        ]);

        $user->refresh();

        $this->assertEquals(1, $user->favoritesFolder()->count());
        $this->assertEquals($folder->id, $user->favoritesFolder->id);
    }

    /**
     * User can find quick list folder.
     *
     * @test
     * @covers \App\Models\User::quickListFolder
     */
    public function user_can_find_quick_list_folder()
    {
        $user = factory(User::class)->create();

        Folder::where('created_by_user_id', $user->id)->forceDelete();

        $folder = factory(Folder::class)->create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::quickList()->first()->id,
        ]);

        $user->refresh();

        $this->assertEquals(1, $user->quickListFolder()->count());
        $this->assertEquals($folder->id, $user->quickListFolder->id);
    }

    /**
     * User can find generic folders.
     *
     * @test
     * @covers \App\Models\User::genericFolders
     */
    public function user_can_find_generic_folders()
    {
        $user = factory(User::class)->create();

        Folder::where('created_by_user_id', $user->id)->forceDelete();

        $folder = factory(Folder::class, 5)->create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::generic()->first()->id,
        ])->first();

        $user->refresh();

        $this->assertEquals(5, $user->genericFolders()->count());
        $this->assertEquals($folder->id, $user->genericFolders()->first()->id);
    }

    /**
     * User can find book folders.
     *
     * @test
     * @covers \App\Models\User::bookFolders
     */
    public function user_can_find_book_folders()
    {
        $user = factory(User::class)->create();

        Folder::where('created_by_user_id', $user->id)->forceDelete();

        $folder = factory(Folder::class, 5)->create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
            'folder_type_id' => FolderType::book()->first()->id,
        ])->first();

        $user->refresh();

        $this->assertEquals(5, $user->bookFolders()->count());
        $this->assertEquals($folder->id, $user->bookFolders()->first()->id);
    }

    /**
     * User can find records.
     *
     * @test
     * @covers \App\Models\User::records
     */
    public function user_can_find_records()
    {
        $user = factory(User::class)->create();

        $record = factory(Record::class, 5)->states('approved')->create([
            'created_by_user_id' => $user->id,
            'updated_by_user_id' => $user->id,
        ])->first();

        $user->refresh();

        $this->assertEquals(5, $user->records()->count());
        $this->assertEquals($record->id, $user->records()->first()->id);
    }
}

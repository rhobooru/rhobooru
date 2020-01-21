<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\SeedsDefaultValues;
use \App\Models\Traits\UserAudits;
use \App\Models\User;
use \App\Models\Record;

class UserAuditsTraitTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * Model's $fillable array gets *by_user_id fields.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::initializeUserAudits
     * @covers \App\Models\Traits\UserAudits::addFillableFields
     */
    public function model_gets_new_fillables()
    {
        $record = factory(Record::class)->create()->refresh();

        $reflector = new \ReflectionProperty(Record::class, 'fillable');
        $reflector->setAccessible(true);

        $this->assertContains('created_by_user_id', $reflector->getValue($record)); 
        $this->assertContains('updated_by_user_id', $reflector->getValue($record));
        $this->assertContains('deleted_by_user_id', $reflector->getValue($record));
    }

    /**
     * Model can get anonymous user.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::getUser
     */
    public function model_can_get_anonymous_user()
    {
        $record = factory(Record::class)->create()->refresh();

        $this->assertEquals(User::anonymous()->username, $record->created_by->username);
    }

    /**
     * Model can get authed user.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::getUser
     */
    public function model_can_get_authed_user()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $record = factory(Record::class)->create()->refresh();

        $this->assertEquals($user->username, $record->created_by->username);
    }

    /**
     * Creating a model sets the created_by_user_id to the authed user.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::bootUserAudits
     * @covers \App\Models\Traits\UserAudits::created_by
     */
    public function creating_model_updates_user_id()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $record = factory(Record::class)->create();

        $record->refresh();

        $this->assertEquals($user->username, $record->created_by->username);
    }

    /**
     * createdBy scope can find user by ID.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::scopeCreatedBy
     */
    public function created_by_scope_can_find_user_by_id()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $record = factory(Record::class)->create();

        $this->assertEquals($user->username, Record::createdBy($user->id)->first()->created_by->username);
    }

    /**
     * createdBy scope can find user by reference.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::scopeCreatedBy
     */
    public function created_by_scope_can_find_user_by_reference()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $record = factory(Record::class)->create();

        $this->assertEquals($user->username, Record::createdBy($user)->first()->created_by->username);
    }

    /**
     * createdBy scope will throw on bad user.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::scopeCreatedBy
     */
    public function created_by_scope_will_throw_on_bad_user()
    {
        $record = factory(Record::class)->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Record::createdBy('trash')->first();
    }

    /**
     * Updating a model sets the updated_by_user_id to the authed user.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::bootUserAudits
     * @covers \App\Models\Traits\UserAudits::updated_by
     */
    public function updating_model_updates_user_id()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $record = factory(Record::class)->create();

        $record->md5 = '1234';
        $record->save();

        $record->refresh();

        $this->assertEquals($user->username, $record->updated_by->username);
    }

    /**
     * updatedBy scope can find user by ID.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::scopeUpdatedBy
     */
    public function updated_by_scope_can_find_user_by_id()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $record = factory(Record::class)->create();

        $record->md5 = '1234';
        $record->save();

        $this->assertEquals($user->username, Record::updatedBy($user->id)->first()->updated_by->username);
    }

    /**
     * updatedBy scope can find user by reference.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::scopeUpdatedBy
     */
    public function updated_by_scope_can_find_user_by_reference()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $record = factory(Record::class)->create();

        $record->md5 = '1234';
        $record->save();

        $this->assertEquals($user->username, Record::updatedBy($user)->first()->updated_by->username);
    }

    /**
     * updatedBy scope will throw on bad user.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::scopeUpdatedBy
     */
    public function updated_by_scope_will_throw_on_bad_user()
    {
        $record = factory(Record::class)->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Record::updatedBy('trash')->first();
    }

    /**
     * Deleting a model sets the deleted_by_user_id to the authed user.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::bootUserAudits
     * @covers \App\Models\Traits\UserAudits::deleted_by
     * @covers \App\Models\Traits\UserAudits::scopeDeletedBy
     */
    public function deleting_model_updates_user_id()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $record = factory(Record::class)->create();

        $id = $record->id;
        $record->delete();

        $this->assertEquals($user->username, Record::withoutGlobalScopes()->withTrashed()->find($id)->deleted_by->username);
    }

    /**
     * deletedBy scope can find user by ID.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::scopeDeletedBy
     */
    public function deleted_by_scope_can_find_user_by_id()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $record = factory(Record::class)->create();

        $id = $record->id;
        $record->delete();

        $this->assertEquals($user->username, Record::withoutGlobalScopes()->withTrashed()->deletedBy($user->id)->first()->deleted_by->username);
    }

    /**
     * deletedBy scope can find user by reference.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::scopeDeletedBy
     */
    public function deleted_by_scope_can_find_user_by_reference()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $record = factory(Record::class)->create();

        $id = $record->id;
        $record->delete();

        $this->assertEquals($user->username, Record::withoutGlobalScopes()->withTrashed()->deletedBy($user)->first()->deleted_by->username);
    }

    /**
     * deletedBy scope will throw on bad user.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::scopeDeletedBy
     */
    public function deleted_by_scope_will_throw_on_bad_user()
    {
        $record = factory(Record::class)->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Record::withoutGlobalScopes()->withTrashed()->deletedBy('trash')->first();
    }
}

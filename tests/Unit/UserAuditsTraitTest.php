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
     * Creating a model sets the created_by_user_id to the authed user.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::boot
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
     * Updating a model sets the updated_by_user_id to the authed user.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::boot
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
     * Deleting a model sets the deleted_by_user_id to the authed user.
     *
     * @test
     * @covers \App\Models\Traits\UserAudits::boot
     * @covers \App\Models\Traits\UserAudits::deleted_by
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
}

<?php

namespace Tests\Unit\Scopes;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\SeedsDefaultValues;
use Tests\TestCase;
use \App\Models\User;
use \App\Scopes\RealUserScope;

class RealUserScopeTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * Scope should find only real users.
     *
     * @test
     * @covers \App\Scopes\RealUserScope::apply
     */
    public function scope_should_find_only_real_users()
    {
        foreach(User::withoutGlobalScopes()->get() as $user)
        {
            $user->forceDelete();
        }

        $anon = factory(User::class)->create([
            'system_account' => true,
            'anonymous_account' => true,
        ]);

        $system = factory(User::class)->create([
            'system_account' => true,
        ]);

        $real = factory(User::class)->create();

        $this->assertEquals(2, User::count());
        $this->assertEquals($real->id, User::where('anonymous_account', null)->first()->id);
        $this->assertEquals($anon->id, User::where('anonymous_account', true)->first()->id);

        $this->assertEquals(3, User::withoutGlobalScopes()->count());
    }
}

<?php

namespace Tests\Unit\Scopes;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\SeedsDefaultValues;
use Tests\TestCase;
use \App\Models\ContentRating;
use \App\Scopes\SortedScope;

class SortedScopeTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues;

    /**
     * Scope should sort by default.
     *
     * @test
     * @covers \App\Scopes\SortedScope::apply
     */
    public function scope_should_sort_by_default()
    {
        $this->assertEquals([1, 2, 3], ContentRating::all()->pluck('id')->toArray());

        ContentRating::swapOrder(ContentRating::find(1), ContentRating::find(3));

        $this->assertEquals([3, 2, 1], ContentRating::all()->pluck('id')->toArray());
        $this->assertEquals([1, 2, 3], ContentRating::withoutGlobalScopes()->get()->pluck('id')->toArray());
    }
}

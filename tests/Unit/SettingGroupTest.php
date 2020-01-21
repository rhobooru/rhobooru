<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\SeedsDefaultValues;
use \App\Models\SettingGroup;
use \App\Models\Setting;

class SettingGroupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * SettingGroups must be able to find their parents.
     *
     * @test
     * @covers \App\Models\SettingGroup::parent
     */
    public function setting_group_can_find_their_parent()
    {
        $parent = factory(SettingGroup::class)->create();
        $group = factory(SettingGroup::class)->create([
            'setting_group_id' => $parent->id,
        ]);

        $group->refresh();

        $this->assertInstanceOf(SettingGroup::class, $group->parent);
        $this->assertEquals($parent->id, $group->parent->id);
    }

    /**
     * SettingGroups must not find empty parents.
     *
     * @test
     * @covers \App\Models\SettingGroup::parent
     */
    public function setting_group_can_have_empty_parents()
    {
        $group = factory(SettingGroup::class)->create();

        $this->assertNull($group->parent);
    }

    /**
     * SettingGroups must not find empty children.
     *
     * @test
     * @covers \App\Models\SettingGroup::children
     */
    public function setting_group_can_have_empty_children()
    {
        $group = factory(SettingGroup::class)->create();

        $this->assertEmpty($group->children);
    }

    /**
     * SettingGroups must be able to find their children.
     *
     * @test
     * @covers \App\Models\SettingGroup::children
     */
    public function setting_group_can_find_their_children()
    {
        $group = factory(SettingGroup::class)->create();
        factory(SettingGroup::class, 10)->create([
            'setting_group_id' => $group->id,
        ]);

        $group->refresh();

        $this->assertEquals(10, $group->children()->count());
    }

    /**
     * SettingGroups should be sorted, by default.
     *
     * @test
     * @covers \App\Models\SettingGroup::boot
     * @covers \App\Models\SettingGroup::buildSortQuery
     */
    public function setting_groups_are_sorted_by_default()
    {
        [$group1, $group2, $group3] = factory(SettingGroup::class, 3)->create();

        $this->assertEquals([$group1->id, $group2->id, $group3->id], 
            SettingGroup::pluck('id')->toArray());

        SettingGroup::swapOrder($group1, $group3);

        $this->assertEquals([$group3->id, $group2->id, $group1->id], 
            SettingGroup::pluck('id')->toArray());
    }

    /**
     * SettingGroups should be sorted by parent.
     *
     * @test
     * @covers \App\Models\SettingGroup::boot
     * @covers \App\Models\SettingGroup::buildSortQuery
     */
    public function setting_groups_are_inserted_with_correct_order()
    {
        [$group1, $group2, $group3] = factory(SettingGroup::class, 3)->create();

        $this->assertEquals([$group1->id, $group2->id, $group3->id], 
            SettingGroup::pluck('id')->toArray());

        [$child1, $child2, $child3] = factory(SettingGroup::class, 3)->create([
            'setting_group_id' => $group2->id
        ]);

        $this->assertEquals([$child1->id, $child2->id, $child3->id], 
            SettingGroup::where('setting_group_id', $group2->id)->pluck('id')->toArray());
    }

    /**
     * SettingGroups can re-sort by parent.
     *
     * @test
     * @covers \App\Models\SettingGroup::boot
     * @covers \App\Models\SettingGroup::buildSortQuery
     */
    public function setting_groups_can_resort_by_parent()
    {
        [$group1, $group2, $group3] = factory(SettingGroup::class, 3)->create();

        $this->assertEquals([$group1->id, $group2->id, $group3->id], 
            SettingGroup::pluck('id')->toArray());

        [$child1, $child2, $child3] = factory(SettingGroup::class, 3)->create([
            'setting_group_id' => $group2->id
        ]);

        $this->assertEquals([$child1->id, $child2->id, $child3->id], 
            SettingGroup::where('setting_group_id', $group2->id)->pluck('id')->toArray());

        SettingGroup::swapOrder($child1, $child3);

        $this->assertEquals([$group1->id, $group2->id, $group3->id], 
            SettingGroup::where('setting_group_id', null)->pluck('id')->toArray());

        $this->assertEquals([$child3->id, $child2->id, $child1->id], 
            SettingGroup::where('setting_group_id', $group2->id)->pluck('id')->toArray());
    }

    /**
     * SettingGroups resort when updating group.
     *
     * @test
     * @covers \App\Models\SettingGroup::boot
     * @covers \App\Models\SettingGroup::buildSortQuery
     */
    public function settings_resort_when_updating_group()
    {
        $parent = factory(SettingGroup::class)->create();

        [$group1, $group2, $group3] = factory(SettingGroup::class, 3)->create();

        [$group4, $group5, $group6] = factory(SettingGroup::class, 3)->create([
            'setting_group_id' => $parent->id,
        ]);

        $group1->refresh();
        $group2->refresh();
        $group3->refresh();
        $group4->refresh();
        $group5->refresh();
        $group6->refresh();

        $this->assertEquals([$parent->id, $group1->id, $group2->id, $group3->id], 
            SettingGroup::where('setting_group_id', null)->pluck('id')->toArray());

        $this->assertEquals([$group4->id, $group5->id, $group6->id], 
            SettingGroup::where('setting_group_id', $parent->id)->pluck('id')->toArray());

        $group2->setting_group_id = $parent->id;
        $group2->save();

        $this->assertEquals([$parent->id, $group1->id, $group3->id], 
            SettingGroup::where('setting_group_id', null)->pluck('id')->toArray());

        $this->assertEquals([$group4->id, $group5->id, $group6->id, $group2->id], 
            SettingGroup::where('setting_group_id', $parent->id)->pluck('id')->toArray());
    }
}

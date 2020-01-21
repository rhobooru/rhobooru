<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\SeedsDefaultValues;
use \App\Models\SettingGroup;
use \App\Models\Setting;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * SettingGroups must be able to find their group.
     *
     * @test
     * @covers \App\Models\Setting::setting_group
     */
    public function setting_can_find_their_group()
    {
        $group = factory(SettingGroup::class)->create();
        $setting = factory(Setting::class)->create([
            'setting_group_id' => $group->id,
        ]);

        $group->refresh();

        $this->assertInstanceOf(SettingGroup::class, $setting->setting_group);
        $this->assertEquals($group->id, $setting->setting_group->id);
    }

    /**
     * SettingGroups must not find empty group.
     *
     * @test
     * @covers \App\Models\Setting::setting_group
     */
    public function setting_can_have_empty_group()
    {
        $setting = factory(Setting::class)->create();

        $this->assertNull($setting->setting_group);
    }

    /**
     * Settings should be sorted, by default.
     *
     * @test
     * @covers \App\Models\Setting::boot
     * @covers \App\Models\Setting::buildSortQuery
     */
    public function settings_are_sorted_by_default()
    {
        $group = factory(SettingGroup::class)->create()->fresh();

        [$setting1, $setting2, $setting3] = 
            factory(Setting::class, 3)->create([
                'setting_group_id' => $group->id,
            ]);

        $setting1->refresh();
        $setting2->refresh();
        $setting3->refresh();

        $this->assertEquals([$setting1->id, $setting2->id, $setting3->id], 
            Setting::where('setting_group_id', $group->id)->pluck('id')->toArray());

        Setting::swapOrder($setting1, $setting3);

        $this->assertEquals([$setting3->id, $setting2->id, $setting1->id], 
            Setting::where('setting_group_id', $group->id)->pluck('id')->toArray());
    }

    /**
     * Settings should be sorted by group.
     *
     * @test
     * @covers \App\Models\Setting::boot
     * @covers \App\Models\Setting::buildSortQuery
     */
    public function settings_are_sorted_by_group()
    {
        $group = factory(SettingGroup::class)->create();


        factory(Setting::class, 3)->create();

        [$setting1, $setting2, $setting3] = 
            factory(Setting::class, 3)->create([
                'setting_group_id' => $group->id,
            ]);

        $setting1->refresh();
        $setting2->refresh();
        $setting3->refresh();

        $this->assertEquals([$setting1->id, $setting2->id, $setting3->id], 
            Setting::where('setting_group_id', $group->id)->pluck('id')->toArray());
    }

    /**
     * Settings can resort by group.
     *
     * @test
     * @covers \App\Models\Setting::boot
     * @covers \App\Models\Setting::buildSortQuery
     */
    public function settings_can_resorted_by_group()
    {
        $group = factory(SettingGroup::class)->create();

        factory(Setting::class, 3)->create();

        [$setting1, $setting2, $setting3] = factory(Setting::class, 3)->create([
            'setting_group_id' => $group->id,
        ]);

        $setting1->refresh();
        $setting2->refresh();
        $setting3->refresh();

        $this->assertEquals([$setting1->id, $setting2->id, $setting3->id], 
            Setting::where('setting_group_id', $group->id)->pluck('id')->toArray());

        Setting::swapOrder($setting1, $setting3);

        $this->assertEquals([$setting3->id, $setting2->id, $setting1->id], 
            Setting::where('setting_group_id', $group->id)->pluck('id')->toArray());
    }

    /**
     * Settings should be sorted by type.
     *
     * @test
     * @covers \App\Models\Setting::boot
     * @covers \App\Models\Setting::buildSortQuery
     */
    public function settings_are_sorted_by_type()
    {
        factory(Setting::class, 3)->states('system')->create();

        [$setting1, $setting2, $setting3] = factory(Setting::class, 3)->create();

        $setting1->refresh();
        $setting2->refresh();
        $setting3->refresh();

        $this->assertEquals([$setting1->id, $setting2->id, $setting3->id], 
            Setting::where('system_setting', false)->pluck('id')->toArray());
    }

    /**
     * Settings can resort by type.
     *
     * @test
     * @covers \App\Models\Setting::boot
     * @covers \App\Models\Setting::buildSortQuery
     */
    public function settings_can_resorted_by_type()
    {
        factory(Setting::class, 3)->states('system')->create();

        [$setting1, $setting2, $setting3] = factory(Setting::class, 3)->create();

        $setting1->refresh();
        $setting2->refresh();
        $setting3->refresh();

        $this->assertEquals([$setting1->id, $setting2->id, $setting3->id], 
            Setting::where('system_setting', false)->pluck('id')->toArray());

        Setting::swapOrder($setting1, $setting3);

        $this->assertEquals([$setting3->id, $setting2->id, $setting1->id], 
            Setting::where('system_setting', false)->pluck('id')->toArray());
    }

    /**
     * Settings resort when updating type.
     *
     * @test
     * @covers \App\Models\Setting::boot
     * @covers \App\Models\Setting::buildSortQuery
     */
    public function settings_resort_when_updating_type()
    {
        [$setting1, $setting2, $setting3] = factory(Setting::class, 3)->states('system')->create();

        [$setting4, $setting5, $setting6] = factory(Setting::class, 3)->create();

        $setting1->refresh();
        $setting2->refresh();
        $setting3->refresh();
        $setting4->refresh();
        $setting5->refresh();
        $setting6->refresh();

        $this->assertEquals([$setting1->id, $setting2->id, $setting3->id], 
            Setting::where('system_setting', true)->pluck('id')->toArray());

        $this->assertEquals([$setting4->id, $setting5->id, $setting6->id], 
            Setting::where('system_setting', false)->pluck('id')->toArray());

        $setting2->system_setting = false;
        $setting2->save();

        $this->assertEquals([$setting1->id, $setting3->id], 
            Setting::where('system_setting', true)->pluck('id')->toArray());

        $this->assertEquals([$setting4->id, $setting5->id, $setting6->id, $setting2->id], 
            Setting::where('system_setting', false)->pluck('id')->toArray());
    }

    /**
     * Settings resort when updating group.
     *
     * @test
     * @covers \App\Models\Setting::boot
     * @covers \App\Models\Setting::buildSortQuery
     */
    public function settings_resort_when_updating_group()
    {
        $group = factory(SettingGroup::class)->create();

        [$setting1, $setting2, $setting3] = factory(Setting::class, 3)->create();

        [$setting4, $setting5, $setting6] = factory(Setting::class, 3)->create([
            'setting_group_id' => $group->id,
        ]);

        $setting1->refresh();
        $setting2->refresh();
        $setting3->refresh();
        $setting4->refresh();
        $setting5->refresh();
        $setting6->refresh();

        $this->assertEquals([$setting1->id, $setting2->id, $setting3->id], 
            Setting::where('setting_group_id', null)->pluck('id')->toArray());

        $this->assertEquals([$setting4->id, $setting5->id, $setting6->id], 
            Setting::where('setting_group_id', $group->id)->pluck('id')->toArray());

        $setting2->setting_group_id = $group->id;
        $setting2->save();

        $this->assertEquals([$setting1->id, $setting3->id], 
            Setting::where('setting_group_id', null)->pluck('id')->toArray());

        $this->assertEquals([$setting4->id, $setting5->id, $setting6->id, $setting2->id], 
            Setting::where('setting_group_id', $group->id)->pluck('id')->toArray());
    }
}

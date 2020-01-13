<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    // protected function setUp(): void
    // {
    //     parent::setUp();

    //     $this->artisan('migrate:refresh');

    //     $this->seed(\DefaultValuesSeeder::class);
    // }

    // protected function tearDown(): void
    // {
    //     $this->artisan('migrate:reset');

    //     parent::tearDown();
    // }
}

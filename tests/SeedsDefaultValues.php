<?php

namespace Tests;

/**
 * Runs the default values seeder for each test.
 */
trait SeedsDefaultValues
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\DefaultValuesSeeder::class);
    }
}

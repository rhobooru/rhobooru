<?php

namespace Tests\Unit\Scopes;

use \App\Providers\BlueprintMacroServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class BlueprintMacroServiceProviderTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    protected $application_mock;

    /**
     * @var ServiceProvider
     */
    protected $service_provider;

    protected function setUp(): void
    {
        $this->application_mock = Mockery::mock(Application::class);

        $this->service_provider = new BlueprintMacroServiceProvider(
            $this->application_mock
        );

        parent::setUp();
    }

    /**
     * The provider should be able to add the timestampUsers macro
     *
     * @test
     * @covers \App\Providers\BlueprintMacroServiceProvider::registerTimestampUsers
     */
    public function can_register_timestamp_users()
    {
        $this->service_provider->registerTimestampUsers();

        $table = new Blueprint($this->application_mock);

        $table->timestampUsers();

        $this->assertEquals(
            ['created_by_user_id', 'updated_by_user_id'],
            array_map(function($element) {
                return $element->getAttributes()["name"];
            }, $table->getColumns())
        );
    }

    /**
     * The provider should be able to add the nullable timestampUsers macro
     *
     * @test
     * @covers \App\Providers\BlueprintMacroServiceProvider::registerTimestampUsers
     */
    public function can_register_nullable_timestamp_users()
    {
        $this->service_provider->registerTimestampUsers();

        $table = new Blueprint($this->application_mock);

        $table->timestampUsers(true);

        $this->assertEquals(
            ['created_by_user_id', 'updated_by_user_id'],
            array_map(function($element) {
                return $element->getAttributes()["name"];
            }, $table->getColumns())
        );

        $this->assertEquals([true, true], array_map(function($element) {
            return $element->getAttributes()["nullable"];
        }, $table->getColumns()));
    }

    /**
     * The provider should be able to add the softDeletesUser macro.
     *
     * @test
     * @covers \App\Providers\BlueprintMacroServiceProvider::registerSoftDeletesUser
     */
    public function can_register_soft_deletes_user()
    {
        $this->service_provider->registerSoftDeletesUser();

        $table = new Blueprint($this->application_mock);

        $table->softDeletesUser();

        $this->assertEquals(
            ['deleted_by_user_id'],
            array_map(function($element) {
                return $element->getAttributes()["name"];
            }, $table->getColumns())
        );

        $this->assertEquals([true], array_map(function($element) {
            return $element->getAttributes()["nullable"];
        }, $table->getColumns()));
    }
}

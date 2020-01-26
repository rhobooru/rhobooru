<?php

namespace Tests\Unit\Scopes;

use \App\Models\SystemSetting;
use \App\Providers\SettingServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\SeedsDefaultValues;
use Tests\TestCase;

class SettingServiceProviderTest extends TestCase
{
    use RefreshDatabase, SeedsDefaultValues {
        SeedsDefaultValues::setUp as seedSetUp;
    }

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

        $this->service_provider = new SettingServiceProvider($this->application_mock);

        parent::setUp();

        $this->seedSetUp();
    }

    /**
     * The provider should be able to load all system settings
     * into the session config.
     *
     * @test
     * @covers \App\Providers\SettingServiceProvider::loadAllSystemSettings
     */
    public function can_load_all_system_settings()
    {
        $this->service_provider->loadAllSystemSettings();

        $system_setting_count = SystemSetting::count();

        $configs = config('rhobooru');
        $config_setting_count = $this->countConfigEntries($configs);

        $this->assertEquals($system_setting_count, $config_setting_count);
    }

    /**
     * recursively count non-array values.
     *
     * @param mixed|array $value
     *
     * @return int
     */
    private function countConfigEntries($value): int
    {
        if (is_array($value)) {
            $count = 0;

            foreach ($value as $inner) {
                $count += $this->countConfigEntries($inner);
            }

            return $count;
        }

        return 1;
    }
}

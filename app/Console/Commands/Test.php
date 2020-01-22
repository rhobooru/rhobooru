<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs unit tests.';

    /**
     * Create a new command instance.
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @codeCoverageIgnore
     */
    public function handle()
    {
        echo shell_exec('phpdbg -qrr -dmemory_limit=4G ./vendor/bin/phpunit');
    }
}

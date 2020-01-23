<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAllCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears every cache imaginable.';

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @codeCoverageIgnore
     */
    public function handle()
    {
        $this->call('route:clear');
        $this->call('view:clear');
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('lighthouse:clear-cache');

        shell_exec('composer dump-autoload');
    }
}

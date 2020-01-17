<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ServeOptimized extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'serve-optimized';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs optimization steps before the `serve` command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('route:cache');
        //$this->call('view:cache');
        $this->call('config:cache');

        $this->call('optimize');

        shell_exec('composer dump-autoload -o');

        $this->call('serve');
    }
}

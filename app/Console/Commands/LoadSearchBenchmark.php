<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LoadSearchBenchmark extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:load-search-benchmark';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes the database and seeds values appropriate for testing the search functionality.';

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
        $this->call('migrate:refresh');

        $seeder = new \SearchBenchmarkSeeder;
        $seeder->loadData();
    }
}

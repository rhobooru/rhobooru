<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WriteSearchBenchmark extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:write-search-benchmark
                            {--load : Tells the job to load the CSVs into the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates CSVs that can be loaded into the DB to test search functionality at scale.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $seeder = new \SearchBenchmarkSeeder;
        $seeder->writeData();
    }
}

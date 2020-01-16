<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DefaultDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'default-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes the database and seeds default values where defined.';

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
        // Reset the database.
        $this->call('migrate:refresh');

        // Seed the default values.
        $seeder = new \DefaultValuesSeeder;
        $seeder->run();

        // Reinstall passport clients.
        $this->call('passport:install');

        // Get the password grant client we just installed.
        $passwordClient = \Laravel\Passport\Client::where('password_client', true)->first();

        $id = $passwordClient->id;
        $secret = $passwordClient->secret;

        // Save the client details to the .env file so laravel-graphql can work.
        \App\Helpers\EnvironmentHelper::setEnvironmentValue('PASSPORT_CLIENT_ID', $id);
        \App\Helpers\EnvironmentHelper::setEnvironmentValue('PASSPORT_CLIENT_SECRET', $secret);

        Storage::deleteDirectory(config('rhobooru.image_processing.originals.storage_path'));
        Storage::deleteDirectory(config('rhobooru.image_processing.previews.storage_path'));
        Storage::deleteDirectory(config('rhobooru.image_processing.thumbnails.storage_path'));
        Storage::deleteDirectory(config('rhobooru.image_processing.staging_path'));
    }
}

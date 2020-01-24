<?php

namespace App\Console\Commands;

use App\Helpers\EnvironmentHelper as Env;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Client;

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

        $this->reinstallPassport();

        $this->deleteMedia();
    }

    /**
     * Reinstalls Passport and saves the new client.
     *
     * @return void
     */
    private function reinstallPassport()
    {
        // Reinstall passport clients.
        $this->call('passport:install');

        // Get the password grant client we just installed.
        $passwordClient = Client::where('password_client', true)->first();

        $id = $passwordClient->id;
        $secret = $passwordClient->secret;

        // Save the client details to the .env file so laravel-graphql can work.
        Env::saveEnvironmentValue('PASSPORT_CLIENT_ID', $id);
        Env::saveEnvironmentValue('PASSPORT_CLIENT_SECRET', $secret);
    }

    /**
     * Deletes uploaded media.
     *
     * @return void
     */
    private function deleteMedia()
    {
        $root = 'rhobooru.media.images';

        Storage::deleteDirectory(config("${root}.originals.storage_path"));
        Storage::deleteDirectory(config("${root}.previews.storage_path"));
        Storage::deleteDirectory(config("${root}.thumbnails.storage_path"));
        Storage::deleteDirectory(config("${root}.staging_path"));
    }
}

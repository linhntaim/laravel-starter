<?php

namespace App\Console\Commands;

use App\Utils\ConfigHelper;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SetupMigrationWithPassportCommand extends SetupMigrationCommand
{
    protected $signature = 'setup:migration:passport {--u} {--dummy-data}';

    protected function setup()
    {
        parent::setup();

        $this->setupPassport();
    }

    protected function seed()
    {
        parent::seed();

        $this->seedPassportData();
    }

    protected function uninstall()
    {
        parent::uninstall();

        $this->uninstallPassport();
    }

    private function setupPassport()
    {
        $this->warn('Passport...');
        Artisan::call('passport:install', [
            '--force' => true,
        ], $this->output);
        $this->warn('Passport!!!');
    }

    private function seedPassportData()
    {
        $clientId = ConfigHelper::get('passport.password.client_id');
        $clientSecret = ConfigHelper::get('passport.password.client_secret');

        if (empty($clientId) || empty($clientSecret)) return;

        DB::table('oauth_clients')
            ->where('id', $clientId)
            ->update([
                'secret' => $clientSecret,
            ]);
        $this->info(sprintf('The client ID %d was updated to %s', $clientId, $clientSecret));
    }

    private function uninstallPassport()
    {
        $this->warn('Removing passport...');
        @unlink(storage_path('oauth-private.key'));
        @unlink(storage_path('oauth-public.key'));
        $this->warn('Passport removed!!!');
    }
}

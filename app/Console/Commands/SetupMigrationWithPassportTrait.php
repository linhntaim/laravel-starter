<?php

namespace App\Console\Commands;

use App\Utils\ConfigHelper;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

trait SetupMigrationWithPassportTrait
{
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
        $this->warn('Seeding passport...');
        $clientId = ConfigHelper::get('passport.password.client_id');
        $clientSecret = ConfigHelper::get('passport.password.client_secret');

        if (empty($clientId) || empty($clientSecret)) return;

        DB::table('oauth_clients')
            ->where('id', $clientId)
            ->update([
                'secret' => $clientSecret,
            ]);
        $this->info(sprintf('The client ID %d was updated to %s', $clientId, $clientSecret));
        $this->warn('Passport seeded!!!');
    }

    private function uninstallPassport()
    {
        $this->warn('Removing passport...');
        @unlink(storage_path('oauth-private.key'));
        @unlink(storage_path('oauth-public.key'));
        $this->warn('Passport removed!!!');
    }
}

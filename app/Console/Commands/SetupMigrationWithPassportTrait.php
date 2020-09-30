<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands;

use App\Utils\ConfigHelper;
use App\Utils\EnvironmentFileHelper;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;

trait SetupMigrationWithPassportTrait
{
    private function setupPassport()
    {
        $this->warn('Passport...');
        $this->call('passport:install', [
            '--force' => true,
        ]);
        $this->info('Passport!!!');
    }

    private function seedPassportData()
    {
        $this->warn('Seeding passport...');
        $clientId = ConfigHelper::get('passport.password.client_id');
        $clientSecret = ConfigHelper::get('passport.password.client_secret');

        $environmentFileHelper = new EnvironmentFileHelper();
        if ($oAuthClient = (empty($clientId) ? Client::query()
            ->where('password_client', 1)
            ->first() : Client::query()
            ->where('id', $clientId)
            ->first())) {
            if (empty($clientId)) {
                $clientId = $oAuthClient->id;
                $environmentFileHelper->fill('PASSPORT_PASSWORD_CLIENT_ID', $oAuthClient->id);
            }
            if (empty($clientSecret)) {
                $clientSecret = $oAuthClient->secret;
                $environmentFileHelper->fill('PASSPORT_PASSWORD_CLIENT_SECRET', $oAuthClient->secret);
            } else {
                // update with no-need-to-hash secret
                $old = Passport::$hashesClientSecrets;
                Passport::$hashesClientSecrets = false;
                $oAuthClient->update([
                    'secret' => $clientSecret,
                ]);
                Passport::$hashesClientSecrets = $old;
                $this->info(sprintf('The client ID %d was updated to %s', $clientId, $clientSecret));
            }
        } else {
            $this->error('No password client exists');
        }
        $environmentFileHelper->save();

        $this->info('Passport seeded!!!');
    }

    private function uninstallPassport()
    {
        $this->warn('Removing passport...');
        @unlink(storage_path('oauth-private.key'));
        @unlink(storage_path('oauth-public.key'));
        $this->info('Passport removed!!!');
    }
}

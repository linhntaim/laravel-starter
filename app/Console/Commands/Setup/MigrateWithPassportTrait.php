<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Setup;

use App\Utils\ConfigHelper;
use App\Utils\EnvironmentFileHelper;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;

trait MigrateWithPassportTrait
{
    protected $passportTables = [
        'oauth_access_tokens',
        'oauth_auth_codes',
        'oauth_clients',
        'oauth_personal_access_clients',
        'oauth_refresh_tokens',
    ];

    private function migratePassport()
    {
        $this->warn('Migrating passport...');

        $migrated = true;
        if ($migrated && $this->forced()) $migrated = false;
        if ($migrated && !file_exists(storage_path('oauth-private.key'))) $migrated = false;
        if ($migrated && !file_exists(storage_path('oauth-public.key'))) $migrated = false;
        if ($migrated) {
            $tablePrefix = DB::getTablePrefix();
            $database = config(sprintf('database.connections.%s.database', config('database.default')));
            $tables = DB::select('select table_name from information_schema.tables where table_schema = ? and table_name <> ? and table_name like ?', [
                $database,
                $tablePrefix . 'oauth_impersonates',
                $tablePrefix . 'oauth_%',
            ]);
            if (count($tables) <= 0) $migrated = false;
            if ($migrated &&
                !collect($tables)->pluck('table_name')
                    ->map(function ($table) use ($tablePrefix) {
                        return $tablePrefix ? substr($table, 0, strlen($tablePrefix)) : $table;
                    })
                    ->every(function ($table) {
                        return in_array($table, $this->passportTables);
                    })) $migrated = false;
            if ($migrated) {
                $clients = DB::select('select * from ' . $tablePrefix . 'oauth_clients');
                if (count($clients) < 2) $migrated = false;
            }
        }

        if (!$migrated) {
            $this->lineBreak();
            $this->rollbackPassport();
            $this->call('passport:install', [
                '--force' => true,
            ]);
            $this->lineBreak();
        }

        $this->info('Passport migrated!!!');
        $this->lineBreak();
    }

    private function seedPassport()
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
                $environmentFileHelper->fill('PASSPORT_PASSWORD_CLIENT_SECRET', $oAuthClient->secret);
            } else {
                // update with no-need-to-hash secret
                if ($clientSecret != $oAuthClient->secret) {
                    $old = Passport::$hashesClientSecrets;
                    Passport::$hashesClientSecrets = false;
                    $oAuthClient->update([
                        'secret' => $clientSecret,
                    ]);
                    Passport::$hashesClientSecrets = $old;
                    $this->info(sprintf('The client ID [%d] has secret updated to [%s]', $clientId, $clientSecret));
                } else {
                    $this->info(sprintf('The client ID [%d] has secret of [%s]', $clientId, $clientSecret));
                }
            }
        } else {
            $this->error('No password client exists');
        }
        $environmentFileHelper->save();

        $this->info('Passport seeded!!!');
        $this->lineBreak();
    }

    private function rollbackPassport()
    {
        $this->warn('Immigrating passport...');

        $tablePrefix = DB::getTablePrefix();
        $database = config(sprintf('database.connections.%s.database', config('database.default')));
        $tables = DB::select('select table_name from information_schema.tables where table_schema = ? and table_name like ?', [
            $database,
            $tablePrefix . 'oauth_%',
        ]);
        DB::statement('set foreign_key_checks = 0');
        foreach ($tables as $table) {
            DB::statement(sprintf('truncate table %s', $table->table_name));
        }
        DB::statement('set foreign_key_checks = 1');

        @unlink(storage_path('oauth-private.key'));
        @unlink(storage_path('oauth-public.key'));

        $this->info('Passport immigrated!!!');
        $this->lineBreak();
    }
}

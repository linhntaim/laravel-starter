<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Setup;

use Illuminate\Support\Facades\DB;

class MigrateCommand extends Command
{
    use MigrateWithPassportTrait;

    protected $signature = 'setup:migrate {--u} {--f}';

    protected $hasPassport = false;

    protected function go()
    {
        $this->hasPassport = class_exists('Laravel\\Passport\\Passport');

        parent::go();
    }

    protected function goForcingToInstall()
    {
        $this->goUninstalling();

        parent::goForcingToInstall();
    }

    protected function goInstalling()
    {
        $this->migrateDatabase();
        $this->migrateTables();
        if ($this->hasPassport) {
            $this->migratePassport();
        }
        $this->migrateSeed();
    }

    protected function goUninstalling()
    {
        $this->rollbackSeed();
        if ($this->hasPassport) {
            $this->rollbackPassport();
        }
        $this->rollbackTables();
        $this->rollbackDatabase();
    }

    protected function migrateDatabase()
    {
        $this->warn('Migrating database...');
        $databaseConnection = config('database.connections.' . config('database.default'));
        switch ($databaseConnection['driver']) {
            case 'mysql':
            default:
                $databaseConnectionWrite = $databaseConnection;
                if (isset($databaseConnection['write'])) {
                    $databaseConnectionWrite = $databaseConnection['write'];
                }
                $get = function ($key) use ($databaseConnectionWrite, $databaseConnection) {
                    return isset($databaseConnectionWrite[$key]) ? $databaseConnectionWrite[$key] : $databaseConnection[$key];
                };
                $pdo = new \PDO(
                    sprintf(
                        'mysql:host=%s;port:%d',
                        $get('host'),
                        $get('port')
                    ),
                    $get('username'),
                    $get('password'),
                    $get('options')
                );
                $pdo->query(sprintf('CREATE DATABASE IF NOT EXISTS `%s`', $get('database')));
                break;
        }
        $this->info('Database migrated!');
        $this->lineBreak();
    }

    private function migrateTables()
    {
        $this->warn('Migrating tables...');
        $this->call('migrate', [
            '--force' => true,
        ]);
        $this->info('Tables migrated!!!');
        $this->lineBreak();
    }

    protected function migrateSeed()
    {
        if ($this->hasPassport) {
            $this->seedPassport();
        }
        $this->call('setup:seed:default');
        $this->lineBreak();
    }

    private function rollbackSeed()
    {
    }

    private function rollbackTables()
    {
        $this->warn('Migrating tables...');
        $database = config(sprintf('database.connections.%s.database', config('database.default')));
        $tables = DB::select('select table_name from information_schema.tables where table_schema = ?', [$database]);
        DB::statement('set foreign_key_checks = 0');
        foreach ($tables as $table) {
            DB::statement(sprintf('drop table %s', $table->table_name));
        }
        DB::statement('set foreign_key_checks = 1');
        $this->info('Tables migrated!!!');
        $this->lineBreak();
    }

    private function rollbackDatabase()
    {

    }
}

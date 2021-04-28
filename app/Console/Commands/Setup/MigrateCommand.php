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

    protected $databaseName = null;

    protected $databaseExisted = false;

    /**
     * @var \PDO|null
     */
    protected $pdo = null;

    protected function go()
    {
        $this->hasPassport = class_exists('Laravel\\Passport\\Passport');

        $this->connectToDatabase();
        parent::go();
        $this->disconnectToDatabase();
    }

    protected function connectToDatabase()
    {
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
                $this->databaseName = $get('database');
                $this->pdo = new \PDO(
                    sprintf(
                        'mysql:host=%s;port:%d',
                        $get('host'),
                        $get('port')
                    ),
                    $get('username'),
                    $get('password'),
                    $get('options')
                );
                break;
        }

        $this->checkIfDatabaseExists();
    }

    protected function checkIfDatabaseExists()
    {
        $query = $this->pdo->prepare('show databases like ?');
        $query->bindValue(1, $this->databaseName);
        $query->execute();
        $this->databaseExisted = count($query->fetchAll()) > 0;
        return $this->databaseExisted;
    }

    protected function disconnectToDatabase()
    {
        $this->pdo = null;
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
        if ($this->databaseExisted) {
            $this->rollbackSeed();
            if ($this->hasPassport) {
                $this->rollbackPassport();
            }
            $this->rollbackTables();
            $this->rollbackDatabase();
        }
    }

    protected function migrateDatabase()
    {
        $this->warn('Migrating database...');
        if (!$this->databaseExisted) {
            $this->pdo
                ->prepare(sprintf('create database if not exists `%s`', $this->databaseName))
                ->execute();
        }
        $this->info('Database migrated!');
        $this->newLine();
    }

    private function migrateTables()
    {
        $this->warn('Migrating tables...');
        $this->call('migrate', [
            '--force' => true,
        ]);
        $this->info('Tables migrated!!!');
        $this->newLine();
    }

    protected function migrateSeed()
    {
        if ($this->hasPassport) {
            $this->seedPassport();
        }
        $this->call('setup:seed:default');
        $this->newLine();
    }

    private function rollbackSeed()
    {
    }

    private function rollbackTables()
    {
        $this->warn('Immigrating tables...');
        $database = config(sprintf('database.connections.%s.database', config('database.default')));
        $tables = DB::select('select table_name from information_schema.tables where table_schema = ?', [$database]);
        DB::statement('set foreign_key_checks = 0');
        foreach ($tables as $table) {
            DB::statement(sprintf('drop table %s', $table->table_name));
        }
        DB::statement('set foreign_key_checks = 1');
        $this->info('Tables immigrated!!!');
        $this->newLine();
    }

    private function rollbackDatabase()
    {

    }
}

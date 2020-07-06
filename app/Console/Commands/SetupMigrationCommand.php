<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SetupMigrationCommand extends Command
{
    use SetupMigrationWithPassportTrait;

    protected $signature = 'setup:migration {--u} {--dummy-data}';

    protected $defaultSeeders = [
        'DefaultSeeder',
    ];

    protected $hasPassport = false;

    protected function go()
    {
        $this->hasPassport = class_exists('Laravel\\Passport\\Passport');
        $this->uninstall();
        if (!$this->option('u')) {
            $this->setup();
            $this->seed();
        }
    }

    protected function setup()
    {
        $this->setupMigration();

        if ($this->hasPassport) {
            $this->setupPassport();
        }
    }

    protected function seed()
    {
        $this->seedDefaultData();
        $this->seedDummyData();

        if ($this->hasPassport) {
            $this->seedPassportData();
        }
    }

    protected function uninstall()
    {
        $this->createDatabaseIfNotExists();

        $this->uninstallMigration();

        if ($this->hasPassport) {
            $this->uninstallPassport();
        }
    }

    protected function createDatabaseIfNotExists()
    {
        $databaseConnection = config('database.connections.' . config('database.default'));
        switch ($databaseConnection['driver']) {
            case 'mysql':
            default:
                $pdo = new \PDO(
                    sprintf('mysql:host=%s;port:%d', $databaseConnection['host'], $databaseConnection['port']),
                    $databaseConnection['username'],
                    $databaseConnection['password']
                );
                $pdo->query(sprintf('CREATE DATABASE IF NOT EXISTS `%s`', $databaseConnection['database']));
                break;
        }
    }

    private function setupMigration()
    {
        $this->warn('Migrating...');
        Artisan::call('migrate', [
            '--force' => true,
        ], $this->output);
        $this->warn('Migrated!!!');
    }

    private function seedDefaultData()
    {
        $this->warn('Seeding default data...');
        foreach ($this->defaultSeeders as $seeder) {
            Artisan::call('db:seed', [
                '--class' => $seeder,
                '--force' => true,
            ], $this->output);
        }
        $this->warn('Seeded!!!');
    }

    private function seedDummyData()
    {
        if ($this->option('dummy-data')) {
            Artisan::call('setup:dummy-data', [], $this->output);
        }
    }

    private function uninstallMigration()
    {
        $this->warn('Removing migration...');
        $database = config(sprintf('database.connections.%s.database', config('database.default')));
        $tables = DB::select('select table_name from information_schema.tables where table_schema = ?', [$database]);
        DB::statement('set foreign_key_checks = 0');
        foreach ($tables as $table) {
            DB::statement(sprintf('drop table %s', $table->table_name));
        }
        DB::statement('set foreign_key_checks = 1');
        $this->warn('Migration removed!!!');
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SetupMigrationCommand extends Command
{
    protected $signature = 'setup:migration {--u} {--dummy-data}';

    protected $seeders = [];

    protected function go()
    {
        $this->uninstall();
        if (!$this->option('u')) {
            $this->setup();
            $this->seed();
        }
    }

    protected function setup()
    {
        $this->setupMigration();
    }

    protected function seed()
    {
        $this->seedDefaultData();
        $this->seedDummyData();
    }

    protected function uninstall()
    {
        $this->uninstallMigration();
    }

    private function setupMigration()
    {
        $this->warn('Migrating...');
        Artisan::call('migrate', [
            '--seed' => true,
        ], $this->output);
        $this->warn('Migrated!!!');
    }

    private function seedDefaultData()
    {
        foreach ($this->seeders as $seeder) {
            Artisan::call('db:seed', [
                '--class' => $seeder,
            ], $this->output);
        }
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

<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Artisan;

class SetupDummyDataCommand extends Command
{
    protected $signature = 'setup:dummy-data {--u}';

    protected function go()
    {
        $this->warn('Seeding dummy data...');
        Artisan::call('db:seed', [
            '--class' => 'EventTruncateSeeder',
        ], $this->output);
        if (!$this->option('u')) {
            Artisan::call('db:seed', [
                '--class' => 'DummyAppInfoSeeder',
            ], $this->output);
            Artisan::call('db:seed', [
                '--class' => 'DummyEventSeeder',
            ], $this->output);
        }
        $this->warn('Dummy data seeded!!!');
    }
}

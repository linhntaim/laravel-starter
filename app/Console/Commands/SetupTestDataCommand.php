<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Artisan;

class SetupTestDataCommand extends Command
{
    protected $signature = 'setup:test-data {--u}';

    protected function go()
    {
        Artisan::call('db:seed', [
            '--class' => 'EventTruncateSeeder',
        ], $this->output);
        if (!$this->option('u')) {
            Artisan::call('db:seed', [
                '--class' => 'TestEventSeeder',
            ], $this->output);
            Artisan::call('db:seed', [
                '--class' => 'TestMembershipSeeder',
            ], $this->output);
            Artisan::call('db:seed', [
                '--class' => 'TestDeviceSeeder',
            ], $this->output);
        }
    }
}

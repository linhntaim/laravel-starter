<?php

namespace App\Console\Commands\Setup;

use App\Utils\EnvironmentFileHelper;

class KeyGenerateCommand extends Command
{
    protected $signature = 'setup:key:generate {--u}';

    protected function goInstalling()
    {
        if (!(new EnvironmentFileHelper())->hasKey('APP_KEY', true)) {
            $this->call('key:generate');
        }
    }

    protected function goUninstalling()
    {
        (new EnvironmentFileHelper())->clear('APP_KEY')->save();
    }
}
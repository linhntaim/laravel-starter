<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Setup;

use App\Utils\EnvironmentFileHelper;

class KeyGenerateCommand extends Command
{
    protected $signature = 'setup:key:generate {--u} {--f}';

    protected function goInstalling()
    {
        if ($this->forced()
            || !(new EnvironmentFileHelper())->hasKey('APP_KEY', true)) {
            $this->call('key:generate');
            $this->newLine();
        }
    }

    protected function goUninstalling()
    {
        (new EnvironmentFileHelper())->clear('APP_KEY')->save();
    }
}

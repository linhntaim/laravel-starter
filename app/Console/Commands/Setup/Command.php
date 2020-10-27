<?php

namespace App\Console\Commands\Setup;

use App\Console\Commands\Base\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    protected function uninstalled()
    {
        return !!$this->option('u');
    }

    protected function go()
    {
        if ($this->uninstalled()) {
            $this->info('Uninstalling...');
            $this->lineBreak();
            $this->goUninstalling();
            $this->info('Uninstalled!');
        } else {
            $this->info('Installing...');
            $this->lineBreak();
            $this->goInstalling();
            $this->info('Installed!');
        }
    }

    protected abstract function goInstalling();

    protected abstract function goUninstalling();
}
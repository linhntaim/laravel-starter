<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Setup;

use App\Console\Commands\Base\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    protected function uninstalled()
    {
        return !!$this->option('u');
    }

    protected function forced()
    {
        return !!$this->option('f');
    }

    protected function go()
    {
        if ($this->uninstalled()) {
            $this->warn(sprintf('Uninstalling [%s]...', $this->__friendlyName()));
            $this->lineBreak();
            $this->goUninstalling();
            $this->info(sprintf('[%s] uninstalled!!!', $this->__friendlyName()));
        } else {
            $this->warn(sprintf('Setting up [%s]...', $this->__friendlyName()));
            $this->lineBreak();
            if ($this->forced()) {
                $this->goForcingToInstall();
            } else {
                $this->goInstalling();
            }
            $this->info(sprintf('[%s] set up!!!', $this->__friendlyName()));
        }
    }

    protected function goForcingToInstall()
    {
        $this->goInstalling();
    }

    protected abstract function goInstalling();

    protected abstract function goUninstalling();
}
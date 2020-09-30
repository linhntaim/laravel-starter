<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\Utils\ConfigHelper;

class SetupPackagesCommand extends Command
{
    protected $signature = 'setup:packages';

    protected $requiredPackages = [];
    protected $removedPackages = [];

    protected function go()
    {
        if (ConfigHelper::get('handled_file.cloud.service.s3')) {
            $this->requirePackages('league/flysystem-aws-s3-v3');
        } else {
            $this->removePackages('league/flysystem-aws-s3-v3');
        }
        if (ConfigHelper::get('handled_file.cloud.service.azure')) {
            $this->requirePackages('matthewbdaly/laravel-azure-storage');
        } else {
            $this->removePackages('matthewbdaly/laravel-azure-storage');
        }
        $this->uninstall()->install();
    }

    protected function requirePackages($package)
    {
        $this->requiredPackages[] = $package;
        return $this;
    }

    protected function removePackages($package)
    {
        $this->removedPackages[] = $package;
        return $this;
    }

    protected function install()
    {
        if (!empty($this->requiredPackages)) {
            $this->goShell('composer require ' . implode(' ', $this->requiredPackages));
        }
        return $this;
    }

    protected function uninstall()
    {
        if (!empty($this->removedPackages)) {
            $this->goShell('composer remove ' . implode(' ', $this->removedPackages));
        }
        return $this;
    }
}
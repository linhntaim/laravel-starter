<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Setup;

use App\Utils\ConfigHelper;

class PackagesCommand extends Command
{
    const PACKAGE_AWS_S3 = 'league/flysystem-aws-s3-v3';
    const PACKAGE_AZURE_BLOB = 'matthewbdaly/laravel-azure-storage';

    protected $signature = 'setup:packages {--u} {--f}';

    protected $composer = [];

    protected function fetchComposer()
    {
        $this->composer = json_decode(file_get_contents(base_path('composer.json')), true);
    }

    protected function existed($package)
    {
        return isset($this->composer['require'][$package]);
    }

    protected function goInstalling()
    {
        $this->fetchComposer();

        $requiredPackages = [];
        $removedPackages = [];
        if (ConfigHelper::get('handled_file.cloud.service.s3')) {
            if ($this->forced() || !$this->existed(static::PACKAGE_AWS_S3)) {
                $requiredPackages[] = static::PACKAGE_AWS_S3;
            }
        } else {
            if ($this->forced() || $this->existed(static::PACKAGE_AWS_S3)) {
                $removedPackages[] = static::PACKAGE_AWS_S3;
            }
        }
        if (ConfigHelper::get('handled_file.cloud.service.azure')) {
            if ($this->forced() || !$this->existed(static::PACKAGE_AZURE_BLOB)) {
                $requiredPackages[] = static::PACKAGE_AZURE_BLOB;
            }
        } else {
            if ($this->forced() || $this->existed(static::PACKAGE_AZURE_BLOB)) {
                $removedPackages[] = static::PACKAGE_AZURE_BLOB;
            }
        }

        if (!empty($removedPackages)) {
            $this->goShell('composer remove ' . implode(' ', $removedPackages));
        }
        if (!empty($requiredPackages)) {
            $this->goShell('composer require ' . implode(' ', $requiredPackages));
        }
    }

    protected function goUninstalling()
    {
        $this->fetchComposer();

        $removedPackages = [];
        if (ConfigHelper::get('handled_file.cloud.service.s3')) {
            if ($this->existed(static::PACKAGE_AWS_S3)) {
                $removedPackages[] = static::PACKAGE_AWS_S3;
            }
        }
        if (ConfigHelper::get('handled_file.cloud.service.azure')) {
            if ($this->existed(static::PACKAGE_AZURE_BLOB)) {
                $removedPackages[] = static::PACKAGE_AZURE_BLOB;
            }
        }

        if (!empty($removedPackages)) {
            $this->goShell('composer remove ' . implode(' ', $removedPackages));
        }
    }
}

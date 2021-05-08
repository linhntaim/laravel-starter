<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Setup;

use App\Utils\ConfigHelper;

class PackagesCommand extends Command
{
    public const PACKAGE_AWS = 'aws/aws-sdk-php';
    public const PACKAGE_AWS_S3 = 'league/flysystem-aws-s3-v3';
    public const PACKAGE_SFTP = 'league/flysystem-sftp';
    public const PACKAGE_AZURE_BLOB = 'matthewbdaly/laravel-azure-storage';
    public const PACKAGE_FILE_VAULT = 'soarecostin/file-vault';

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
        $checkPackage = function ($check, $package) use (&$requiredPackages, &$removedPackages) {
            if ($check) {
                if ($this->forced() || !$this->existed($package)) {
                    $requiredPackages[] = $package;
                }
            }
            else {
                if ($this->forced() || $this->existed($package)) {
                    $removedPackages[] = $package;
                }
            }
        };

        $checkPackage(
            config('services.ses.key') && config('services.ses.secret'),
            static::PACKAGE_AWS
        );
        $checkPackage(
            ConfigHelper::get('handled_file.cloud.service.s3'),
            static::PACKAGE_AWS_S3
        );
        $checkPackage(
            config('filesystems.disks.sftp.host'),
            static::PACKAGE_SFTP
        );
        $checkPackage(
            ConfigHelper::get('handled_file.cloud.service.azure'),
            static::PACKAGE_AZURE_BLOB
        );
        $checkPackage(
            ConfigHelper::get('handled_file.encryption.enabled'),
            static::PACKAGE_FILE_VAULT
        );

        if (!empty($removedPackages)) {
            $this->goShell('composer remove ' . implode(' ', $removedPackages));
        }
        if (!empty($requiredPackages)) {
            $this->goShell('composer require ' . implode(' ', $requiredPackages) . ' --with-all-dependencies');
        }
    }

    protected function goUninstalling()
    {
        $this->fetchComposer();

        $removedPackages = [];
        $checkPackage = function ($check, $package) use (&$removedPackages) {
            if ($check && $this->existed($package)) {
                $removedPackages[] = $package;
            }
        };

        $checkPackage(
            config('services.ses.key') && config('services.ses.secret'),
            static::PACKAGE_AWS
        );
        $checkPackage(
            ConfigHelper::get('handled_file.cloud.service.s3'),
            static::PACKAGE_AWS_S3
        );
        $checkPackage(
            config('filesystems.disks.sftp.host'),
            static::PACKAGE_SFTP
        );
        $checkPackage(
            ConfigHelper::get('handled_file.cloud.service.azure'),
            static::PACKAGE_AZURE_BLOB
        );
        $checkPackage(
            ConfigHelper::get('handled_file.encryption.enabled'),
            static::PACKAGE_FILE_VAULT
        );

        if (!empty($removedPackages)) {
            $this->goShell('composer remove ' . implode(' ', $removedPackages));
        }
    }
}

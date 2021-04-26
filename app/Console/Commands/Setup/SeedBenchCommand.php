<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Setup;

use Database\Seeders\BenchRoleSeeder;

class SeedBenchCommand extends SeedCommand
{
    protected $signature = 'setup:seed:bench {--u} {--f}';

    protected $seeders = [
        // TODO:
        BenchRoleSeeder::class,

        // TODO
    ];

    protected function goInstalling()
    {
        ini_set('memory_limit', -1);
        parent::goInstalling();
    }
}

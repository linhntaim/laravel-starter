<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Setup;

class SeedDefaultCommand extends SeedCommand
{
    protected $signature = 'setup:seed:default {--u} {--f}';

    protected $seeders = [
        'DefaultSeeder',
        // TODO:

        // TODO
    ];
}

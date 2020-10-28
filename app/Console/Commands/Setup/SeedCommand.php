<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Setup;

abstract class SeedCommand extends Command
{
    protected $seeders = [];

    protected function goInstalling()
    {
        foreach ($this->seeders as $seeder) {
            $this->warn(sprintf('Seeding [%s]...', $seeder));
            $this->call('db:seed', [
                '--class' => $seeder,
                '--force' => true,
            ]);
            $this->lineBreak();
        }
    }

    protected function goUninstalling()
    {
    }
}
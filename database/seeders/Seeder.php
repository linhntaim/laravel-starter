<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder as BaseSeeder;

abstract class Seeder extends BaseSeeder
{
    public function output()
    {
        return $this->command->getOutput();
    }

    public abstract function run();
}

<?php

use Illuminate\Database\Seeder as BaseSeeder;

class Seeder extends BaseSeeder
{
    public function output()
    {
        return $this->command->getOutput();
    }
}

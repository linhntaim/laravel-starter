<?php

namespace App\Console\Commands\Base;

abstract class ShellCommand extends Command
{
    protected abstract function getShell();

    protected function go()
    {
        $this->goShell($this->getShell());
    }
}

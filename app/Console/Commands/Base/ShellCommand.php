<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Base;

abstract class ShellCommand extends Command
{
    protected abstract function getShell();

    protected function go()
    {
        $this->goShell($this->getShell());
    }
}

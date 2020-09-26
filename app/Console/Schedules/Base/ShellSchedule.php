<?php

namespace App\Console\Schedules\Base;

abstract class ShellSchedule extends CommandSchedule
{
    protected abstract function getShell();

    protected final function getCommand()
    {
        return 'shell:manual';
    }

    protected final function getParameters()
    {
        return [
            'shell' => $this->getShell(),
        ];
    }
}

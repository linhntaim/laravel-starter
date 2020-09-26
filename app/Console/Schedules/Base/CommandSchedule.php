<?php

namespace App\Console\Schedules\Base;

abstract class CommandSchedule extends Schedule
{
    protected abstract function getCommand();

    protected function getParameters()
    {
        return [];
    }

    protected function go()
    {
        $this->kernel->call($this->getCommand(), $this->getParameters());
    }
}

<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Make;

use App\Console\Commands\Base\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    protected function forced()
    {
        return !!$this->option('f');
    }

    protected function go()
    {
            $this->warn(sprintf('Making [%s]...', $this->__friendlyName()));
            $this->newLine();
            if ($this->forced()) {
                $this->goForcingToMake();
            } else {
                $this->goMaking();
            }
            $this->info(sprintf('[%s] made!!!', $this->__friendlyName()));
    }

    protected function goForcingToMake()
    {
        $this->goMaking();
    }

    protected abstract function goMaking();
}

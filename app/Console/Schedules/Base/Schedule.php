<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Schedules\Base;

use App\Utils\LogHelper;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

abstract class Schedule
{
    /**
     * @var ConsoleKernel
     */
    protected $kernel;

    public function withKernel(ConsoleKernel $kernel)
    {
        $this->kernel = $kernel;
        return $this;
    }

    public function handle()
    {
        try {
            $this->go();
        } catch (\Exception $exception) {
            LogHelper::error($exception);
        }
    }

    protected abstract function go();
}

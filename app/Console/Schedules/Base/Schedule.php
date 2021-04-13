<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Schedules\Base;

use App\Utils\ClientSettings\Traits\ConsoleClientTrait;
use App\Utils\LogHelper;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

abstract class Schedule
{
    use ConsoleClientTrait;

    /**
     * @var ConsoleKernel
     */
    protected $kernel;

    public function __construct()
    {
        $this->consoleClientApply();
    }

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

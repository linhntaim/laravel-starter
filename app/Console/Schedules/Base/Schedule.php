<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Schedules\Base;

use App\Utils\ClassTrait;
use App\Utils\ClientSettings\Traits\ConsoleClientTrait;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

abstract class Schedule
{
    use ClassTrait, ConsoleClientTrait;

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
            $this->handleException($exception);
        }
    }

    protected function handleException($exception)
    {
        Log::error($exception);
    }

    protected abstract function go();
}

<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Schedules\Base;

use App\Utils\ClassTrait;
use App\Utils\ClientSettings\Traits\ConsoleClientTrait;
use App\Utils\Database\Transaction\TransactionTrait;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class Schedule
{
    use ClassTrait, ConsoleClientTrait, TransactionTrait;

    /**
     * @var ConsoleKernel
     */
    protected $kernel;

    public function withKernel(ConsoleKernel $kernel)
    {
        $this->kernel = $kernel;
        return $this;
    }

    public function start()
    {
        Log::info(sprintf('%s scheduling...', static::class));
        return $this;
    }

    public function end()
    {
        Log::info(sprintf('%s scheduled!', static::class));
        return $this;
    }

    public function fails()
    {
        Log::info(sprintf('%s failed!', static::class));
        return $this;
    }

    public function handle()
    {
        $this->consoleClientApply();
        try {
            $this->start();
            $this->go();
            $this->end();
        }
        catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    protected function handleException(Throwable $e)
    {
        report($e);
        $this->fails();
    }

    protected abstract function go();
}

<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Schedules\Base;

use App\Console\Schedules\Middleware\Client;
use App\Utils\LogHelper;
use App\Utils\SelfMiddleware\SelfMiddlewareTrait;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

abstract class Schedule
{
    use SelfMiddlewareTrait;

    /**
     * @var ConsoleKernel
     */
    protected $kernel;

    protected $selfMiddlewares = [
        Client::class,
    ];

    public function getClientId()
    {
        return null;
    }

    public function withKernel(ConsoleKernel $kernel)
    {
        $this->kernel = $kernel;
        return $this;
    }

    public function handle()
    {
        try {
            $this->selfMiddleware();
            $this->go();
        } catch (\Exception $exception) {
            LogHelper::error($exception);
        }
    }

    protected abstract function go();
}

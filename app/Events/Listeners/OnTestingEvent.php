<?php

namespace App\Events\Listeners;

use App\Events\TestingEvent;
use App\Events\Listeners\Base\NowListener;
use App\Utils\LogHelper;

class OnTestingEvent extends NowListener
{
    /**
     * @param TestingEvent $event
     */
    protected function go($event)
    {
        LogHelper::info(sprintf('%s executed.', static::class));
    }
}

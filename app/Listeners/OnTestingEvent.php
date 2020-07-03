<?php

namespace App\Listeners;

use App\Events\TestingEvent;
use App\Listeners\Base\NowListener;
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

<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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

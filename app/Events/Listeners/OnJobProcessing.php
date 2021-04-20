<?php

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use Illuminate\Queue\Events\JobProcessing;

class OnJobProcessing extends NowListener
{
    /**
     * @param JobProcessing $event
     */
    protected function go($event)
    {
    }
}

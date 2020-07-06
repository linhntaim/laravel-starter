<?php

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use Illuminate\Mail\Events\MessageSent;

class OnMessageSent extends NowListener
{
    /**
     * @param MessageSent $event
     */
    protected function go($event)
    {
    }
}
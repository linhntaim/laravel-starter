<?php

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use Illuminate\Mail\Events\MessageSending;

class OnMessageSending extends NowListener
{
    /**
     * @param MessageSending $event
     */
    protected function go($event)
    {
    }
}
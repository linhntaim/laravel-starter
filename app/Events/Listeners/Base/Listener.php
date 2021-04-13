<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners\Base;

use App\Events\Event;
use App\Utils\ClientSettings\Capture;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Listener extends NowListener implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Capture;

    public function __construct()
    {
        if (!$this->independentClientId()) {
            $this->settingsCapture();
        }
    }

    /**
     * @param Event $event
     */
    public function handle($event)
    {
        if ((app()->runningInConsole() || app()->runningUnitTests()) && !$this->independentClientId()) {
            $event->settingsTemporary(function () use ($event) {
                parent::handle($event);
            });
        } else {
            parent::handle($event);
        }
    }
}

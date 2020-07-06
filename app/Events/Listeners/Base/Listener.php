<?php

namespace App\Events\Listeners\Base;

use App\Events\Event;
use App\Utils\LogHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Listener extends NowListener implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * @param Event $event
     */
    public function handle($event)
    {
        if (app()->runningInConsole()) {
            try {
                $event->settingsTemporary(function () use ($event) {
                    parent::handle($event);
                });
            } catch (\Exception $exception) {
                LogHelper::error($exception);
            }
        } else {
            parent::handle($event);
        }
    }
}

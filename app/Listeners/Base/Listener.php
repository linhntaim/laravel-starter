<?php

namespace App\Listeners\Base;

use App\Utils\LogHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Listener extends NowListener implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public function handle($event)
    {
        if (app()->runningInConsole()) {
            try {
                parent::handle($event);
            } catch (\Exception $exception) {
                LogHelper::error($exception);
            }
        } else {
            parent::handle($event);
        }
    }
}

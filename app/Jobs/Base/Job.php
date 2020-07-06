<?php

namespace App\Jobs\Base;

use App\Utils\ClientSettings\Capture;
use App\Utils\LogHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Job extends NowJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Capture;

    public function handle()
    {
        if (app()->runningInConsole()) {
            try {
                $this->settingsTemporary(function () {
                    parent::handle();
                });
            } catch (\Exception $exception) {
                LogHelper::error($exception);
            }
        } else {
            parent::handle();
        }
    }
}

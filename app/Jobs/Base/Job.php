<?php

namespace App\Jobs\Base;

use App\Utils\LogHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Job extends NowJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public function handle()
    {
        if (app()->runningInConsole()) {
            try {
                parent::handle();
            } catch (\Exception $exception) {
                LogHelper::error($exception);
            }
        } else {
            parent::handle();
        }
    }
}

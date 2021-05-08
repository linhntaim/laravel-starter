<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Jobs\Base;

use App\Utils\ClientSettings\Capture;
use App\Vendors\Illuminate\Support\Facades\App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Job extends NowJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Capture;

    public function __construct()
    {
        if (!$this->independentClientId()) {
            $this->settingsCapture();
        }
    }

    public function handle()
    {
        if (App::notRunningFromRequest() && !$this->independentClientId()) {
            $this->settingsTemporary(function () {
                parent::handle();
            });
        }
        else {
            parent::handle();
        }
    }
}

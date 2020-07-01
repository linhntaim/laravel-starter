<?php

namespace App\Listeners\Base;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Listener extends NowListener implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;
}

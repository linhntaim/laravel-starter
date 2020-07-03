<?php

namespace App\Notifications\Base;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Notification extends NowNotification implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    const NAME = 'notification';
}

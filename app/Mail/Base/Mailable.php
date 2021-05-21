<?php

namespace App\Mail\Base;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Mailable extends NowMailable implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;
}
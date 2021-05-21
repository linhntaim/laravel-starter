<?php

namespace App\Mail\Base;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class TemplateMailable extends TemplateNowMailable implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;
}
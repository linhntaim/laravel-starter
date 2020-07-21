<?php

namespace App\Utils\Mail;

use App\Utils\ClientSettings\Capture;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TemplateMailable extends TemplateNowMailable implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Capture;

    public function build()
    {
        $this->settingsTemporary(function () {
            parent::build();
        });
    }
}

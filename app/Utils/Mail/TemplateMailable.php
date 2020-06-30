<?php

namespace App\Utils\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class TemplateMailable extends TemplateNowMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct($templateName, $templateParams = [], $useLocalizedTemplate = true, $locale = null)
    {
        parent::__construct($templateName, $templateParams, $useLocalizedTemplate, $locale);
    }
}

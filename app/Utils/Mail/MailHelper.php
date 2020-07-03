<?php

namespace App\Utils\Mail;

use App\Exceptions\AppException;
use App\Utils\ConfigHelper;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class MailHelper
{
    public static function send(Mailable $mailable)
    {
        if (ConfigHelper::get('emails.send_off')) return true;

        try {
            if ($mailable instanceof ShouldQueue) {
                Mail::queue($mailable);
            } else {
                Mail::send($mailable);
            }
            return true;
        } catch (Exception $ex) {
            throw AppException::from($ex);
        }
    }

    public static function sendWithTemplate($templatePath, $templateParams, $useLocalizedTemplate = true, $locale = null)
    {
        return static::send(new TemplateMailable($templatePath, $templateParams, $useLocalizedTemplate, $locale));
    }

    public static function sendNowWithTemplate($templatePath, $templateParams, $useLocalizedTemplate = true, $locale = null)
    {
        return static::send(new TemplateNowMailable($templatePath, $templateParams, $useLocalizedTemplate, $locale));
    }

    public static function sendTestMail()
    {
        $emailTested = ConfigHelper::getTestedMail();
        return static::sendWithTemplate(
            'test',
            [
                TemplateMailable::EMAIL_SUBJECT => 'Tested',
                TemplateMailable::EMAIL_TO => $emailTested['address'],
                TemplateMailable::EMAIL_TO_NAME => $emailTested['name'],
            ],
            false
        );
    }

    public static function sendTestMailNow()
    {
        $emailTested = ConfigHelper::getTestedMail();
        return static::sendNowWithTemplate(
            'test',
            [
                TemplateMailable::EMAIL_SUBJECT => 'Tested',
                TemplateMailable::EMAIL_TO => $emailTested['address'],
                TemplateMailable::EMAIL_TO_NAME => $emailTested['name'],
            ],
            false
        );
    }
}

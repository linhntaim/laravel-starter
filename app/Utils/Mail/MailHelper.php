<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Mail;

use App\Utils\ConfigHelper;
use Illuminate\Support\Facades\Mail;

class MailHelper
{
    /**
     * @param TemplateNowMailable $mailable
     * @return bool
     * @throws
     */
    public static function send(TemplateNowMailable $mailable)
    {
        if (ConfigHelper::get('emails.send_off')) return true;

        Mail::send($mailable);
        return true;
    }

    /**
     * @param string $templatePath
     * @param array $templateParams
     * @param bool $templateLocalized
     * @return bool
     * @throws
     */
    public static function sendWithTemplate($templatePath, array $templateParams = [], $templateLocalized = true)
    {
        return static::send(new TemplateMailable($templatePath, $templateParams, $templateLocalized));
    }

    /**
     * @param string $templatePath
     * @param array $templateParams
     * @param bool $templateLocalized
     * @return bool
     * @throws
     */
    public static function sendNowWithTemplate($templatePath, array $templateParams = [], $templateLocalized = true)
    {
        return static::send(new TemplateNowMailable($templatePath, $templateParams, $templateLocalized));
    }

    public static function sendTestMail($subject = 'Tested', $templatePath = 'test')
    {
        $emailTested = ConfigHelper::getTestedMail();
        return static::sendWithTemplate(
            $templatePath,
            [
                TemplateMailable::EMAIL_SUBJECT => $subject,
                TemplateMailable::EMAIL_TO => $emailTested['address'],
                TemplateMailable::EMAIL_TO_NAME => $emailTested['name'],
            ],
            false
        );
    }

    public static function sendTestMailNow($subject = 'Tested', $templatePath = 'test')
    {
        $emailTested = ConfigHelper::getTestedMail();
        return static::sendNowWithTemplate(
            $templatePath,
            [
                TemplateMailable::EMAIL_SUBJECT => $subject,
                TemplateMailable::EMAIL_TO => $emailTested['address'],
                TemplateMailable::EMAIL_TO_NAME => $emailTested['name'],
            ],
            false
        );
    }
}

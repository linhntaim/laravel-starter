<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Mail;

use App\Utils\ConfigHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailHelper
{
    /**
     * @param TemplateNowMailable $mailable
     * @param bool $exceptionSkipped
     * @return bool
     * @throws
     */
    public static function send(TemplateNowMailable $mailable, $exceptionSkipped = false)
    {
        if (ConfigHelper::get('emails.send_off')) return true;

        if ($mailable instanceof TemplateMailable) {
            $exceptionSkipped = false;
        }

        if ($exceptionSkipped) {
            try {
                return static::sendRaw($mailable);
            } catch (Throwable $e) {
                Log::error($e);
                return false;
            }
        }

        return static::sendRaw($mailable);
    }

    protected static function sendRaw(TemplateNowMailable $mailable)
    {
        Mail::send($mailable);
        return true;
    }

    /**
     * @param string $templatePath
     * @param array $templateParams
     * @param bool $templateLocalized
     * @param string|null $templateLocale
     * @param bool $exceptionSkipped
     * @param string|null $templateNamespace
     * @param string|array|null $charset
     * @return bool
     * @throws
     */
    public static function sendWithTemplate($templatePath, array $templateParams = [], $templateLocalized = true, $templateLocale = null, $exceptionSkipped = false, $templateNamespace = null, $charset = null)
    {
        return static::send(new TemplateMailable($templatePath, $templateParams, $templateLocalized, $templateLocale, $templateNamespace, $charset), $exceptionSkipped);
    }

    /**
     * @param string $templatePath
     * @param array $templateParams
     * @param bool $templateLocalized
     * @param string|null $templateLocale
     * @param bool $exceptionSkipped
     * @param string|null $templateNamespace
     * @param string|array|null $charset
     * @return bool
     * @throws
     */
    public static function sendNowWithTemplate($templatePath, array $templateParams = [], $templateLocalized = true, $templateLocale = null, $exceptionSkipped = false, $templateNamespace = null, $charset = null)
    {
        return static::send(new TemplateNowMailable($templatePath, $templateParams, $templateLocalized, $templateLocale, $templateNamespace, $charset), $exceptionSkipped);
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

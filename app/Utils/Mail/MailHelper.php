<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Mail;

use App\Exceptions\AppException;
use App\Utils\ConfigHelper;
use Exception;
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

        try {
            if ($mailable instanceof TemplateMailable) {
                Mail::queue($mailable);
            } else {
                Mail::send($mailable);
            }
            return true;
        } catch (Exception $ex) {
            throw AppException::from($ex);
        }
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

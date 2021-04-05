<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Mail;

use App\Exceptions\AppException;
use App\Utils\ClassTrait;
use App\Utils\ClientSettings\Capture;
use App\Utils\ConfigHelper;
use App\Utils\ClientSettings\Facade;
use App\Utils\LogHelper;
use App\Utils\RateLimiterTrait;
use Illuminate\Mail\Mailable;

class TemplateNowMailable extends Mailable
{
    use ClassTrait, RateLimiterTrait, Capture;

    const DEFAULT_CHARSET = 'utf-8';

    const EMAIL_FROM = 'x_email_from';
    const EMAIL_FROM_NAME = 'x_email_from_name';
    const EMAIL_TO = 'x_email_to';
    const EMAIL_TO_NAME = 'x_email_to_name';
    const EMAIL_SUBJECT = 'x_email_subject';

    protected $charset;

    protected $templateName;

    protected $templateParams;

    protected $templateLocalized;

    protected $templateNamespace;

    public function __construct($templateName, array $templateParams = [], $templateLocalized = true, $templateLocale = null, $templateNamespace = null, $charset = null)
    {
        $this->charset = is_null($charset) ? ConfigHelper::get('emails.send_charset') : $charset;
        $this->templateName = $templateName;
        $this->templateParams = $templateParams;
        $this->templateLocalized = $templateLocalized;
        $this->templateNamespace = $templateNamespace;

        $this->settingsCapture();

        if ($templateLocale) {
            $this->setLocale($templateLocale);
        }
    }

    protected function notDefaultCharset()
    {
        return $this->charset != TemplateNowMailable::DEFAULT_CHARSET;
    }

    protected function convertCharset($text)
    {
        return !is_null($text) && $this->notDefaultCharset() ?
            mb_convert_encoding($text, $this->charset, TemplateNowMailable::DEFAULT_CHARSET) : $text;
    }

    public function subject($subject)
    {
        return parent::subject($this->convertCharset($subject));
    }

    protected function getTemplatePath()
    {
        return sprintf('%semails.%s%s',
            $this->templateNamespace ? $this->templateNamespace . '::' : '',
            $this->templateName,
            ($this->templateLocalized ? '.' . $this->locale : '')
        );
    }

    protected function getTemplateParams()
    {
        return array_merge($this->templateParams, [
            'locale' => $this->locale,
            'charset' => $this->charset,
        ]);
    }

    public function build()
    {
        if (isset($this->templateParams[static::EMAIL_FROM])) {
            if (empty($this->templateParams[static::EMAIL_FROM])) {
                throw new AppException('From email has been not set');
            }
            if (isset($this->templateParams[static::EMAIL_FROM_NAME])) {
                $this->from($this->templateParams[static::EMAIL_FROM], $this->convertCharset($this->templateParams[static::EMAIL_FROM_NAME]));
            } else {
                $this->from($this->templateParams[static::EMAIL_FROM]);
            }
        } else {
            $noReplyMail = ConfigHelper::getNoReplyMail();
            if (empty($noReplyMail['address'])) {
                throw new AppException('No-reply email has been not set');
            }
            $this->from($noReplyMail['address'], $this->convertCharset($noReplyMail['name']));
        }

        $emailTested = ConfigHelper::getTestedMail();
        if ($emailTested['used']) {
            if (empty($emailTested['address'])) {
                throw new AppException('Tested email has been not set');
            }
            $this->to($emailTested['address'], $this->convertCharset($emailTested['name']));
        } else {
            if (empty($this->templateParams[static::EMAIL_TO])) {
                throw new AppException('To email has been not set');
            }
            if (isset($this->templateParams[static::EMAIL_TO_NAME])) {
                $this->to($this->templateParams[static::EMAIL_TO], $this->convertCharset($this->templateParams[static::EMAIL_TO_NAME]));
            } else {
                $this->to($this->templateParams[static::EMAIL_TO]);
            }
        }

        if ($this->notDefaultCharset()) {
            $this->callbacks[] = function ($message) {
                $message->setCharset($this->charset);
            };
        }

        $this->subject(
            isset($this->templateParams[static::EMAIL_SUBJECT]) ?
                $this->templateParams[static::EMAIL_SUBJECT]
                : $this->__transWithModule('default_subject', 'label', ['app_name' => Facade::getAppName()])
        );

        $this->view($this->getTemplatePath(), $this->getTemplateParams());
    }

    public function send($mailer)
    {
        $maxAttempts = ConfigHelper::get('emails.send_rate_per_second');
        if ($maxAttempts) {
            $this->getLimiter();

            $key = ConfigHelper::get('emails.send_rate_key');
            if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
                LogHelper::info(sprintf('Delay mailing: %s - %s - %s', static::class, $this->templateName, json_encode($this->templateParams)));
                sleep(ConfigHelper::get('emails.send_rate_wait_for_seconds'));
                $this->send($mailer);
                return;
            }

            $this->limiter->hit($key, 1);
        }

        parent::send($mailer);
    }
}

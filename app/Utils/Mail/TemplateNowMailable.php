<?php

namespace App\Utils\Mail;

use App\Exceptions\AppException;
use App\Utils\ClassTrait;
use App\Utils\ConfigHelper;
use App\Utils\Facades\ClientSettings;
use Illuminate\Mail\Mailable;

class TemplateNowMailable extends Mailable
{
    use ClassTrait;

    const EMAIL_FROM = 'x_email_from';
    const EMAIL_FROM_NAME = 'x_email_from_name';
    const EMAIL_TO = 'x_email_to';
    const EMAIL_TO_NAME = 'x_email_to_name';
    const EMAIL_SUBJECT = 'x_email_subject';

    protected $templateName;

    protected $templateParams;

    protected $templateLocalized;

    public function __construct($templateName, array $templateParams = [], $templateLocalized = true)
    {
        $this->templateName = $templateName;
        $this->templateParams = $templateParams;
        $this->templateLocalized = $templateLocalized;
    }

    protected function getTemplatePath()
    {
        return 'emails.' . $this->templateName . ($this->templateLocalized ? '.' . $this->locale : '');
    }

    public function build()
    {
        $this->settingsTemporary(function () {
            if (isset($this->templateParams[static::EMAIL_FROM])) {
                if (empty($this->templateParams[static::EMAIL_FROM])) {
                    throw new AppException('From email has been not set');
                }
                if (isset($this->templateParams[static::EMAIL_FROM_NAME])) {
                    $this->from($this->templateParams[static::EMAIL_FROM], $this->templateParams[static::EMAIL_FROM_NAME]);
                } else {
                    $this->from($this->templateParams[static::EMAIL_FROM]);
                }
            } else {
                $noReplyMail = ConfigHelper::getNoReplyMail();
                if (empty($noReplyMail['address'])) {
                    throw new AppException('No-reply email has been not set');
                }
                $this->from($noReplyMail['address'], $noReplyMail['name']);
            }

            $emailTested = ConfigHelper::getTestedMail();
            if ($emailTested['used']) {
                if (empty($emailTested['address'])) {
                    throw new AppException('Tested email has been not set');
                }
                $this->to($emailTested['address'], $emailTested['name']);
            } else {
                if (empty($this->templateParams[static::EMAIL_TO])) {
                    throw new AppException('To email has been not set');
                }
                if (isset($this->templateParams[static::EMAIL_TO_NAME])) {
                    $this->to($this->templateParams[static::EMAIL_TO], $this->templateParams[static::EMAIL_TO_NAME]);
                } else {
                    $this->to($this->templateParams[static::EMAIL_TO]);
                }
            }

            $this->subject(
                isset($this->templateParams[static::EMAIL_SUBJECT]) ?
                    $this->templateParams[static::EMAIL_SUBJECT]
                    : $this->__transWithModule('default_subject', 'label', ['app_name' => ClientSettings::getAppName()])
            );

            $this->view($this->getTemplatePath(), $this->templateParams);
        });
    }
}

<?php

namespace App\Notifications;

use App\Models\Base\IUser;
use App\Notifications\Base\AdminNowNotification;
use App\Utils\ClientSettings\Facade;

class ResetPasswordNotification extends AdminNowNotification
{
    protected $token;
    protected $appResetPasswordPath;

    public function __construct($token, IUser $notifier = null)
    {
        parent::__construct($notifier);

        $this->token = $token;

        $this->appResetPasswordPath = request()->input('app_reset_password_path');

        $this->shouldMail();
    }

    protected function getMailTemplate(IUser $notifiable)
    {
        return 'user_password_reset';
    }

    protected function getMailSubject(IUser $notifiable)
    {
        return $this->__transWithCurrentModule('mail_subject', [
            'app_name' => Facade::getAppName(),
        ]);
    }

    protected function getMailParams(IUser $notifiable)
    {
        return [
            'url_reset_password' => $this->getAppResetPasswordUrl($notifiable),
            'expired_at' => $notifiable->getPasswordResetExpiredAt(),
        ];
    }

    public function getAppResetPasswordUrl(IUser $notifiable)
    {
        return implode('/', [
            Facade::getAppUrl(),
            trim($this->appResetPasswordPath, '/'),
            $this->token,
        ]);
    }
}

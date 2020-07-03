<?php

namespace App\Notifications;

use App\Notifications\Base\AdminNowNotification;
use App\Utils\Facades\ClientSettings;

class ResetPasswordNotification extends AdminNowNotification
{
    protected $token;
    protected $appResetPasswordPath;

    public function __construct($token, $fromUser = null)
    {
        parent::__construct($fromUser);

        $this->token = $token;

        $this->appResetPasswordPath = request()->input('app_reset_password_path');

        $this->shouldMail();
    }

    protected function getMailTemplate($notifiable)
    {
        return 'user_password_reset';
    }

    protected function getMailSubject($notifiable)
    {
        return $this->__transWithCurrentModule('mail_subject', [
            'app_name' => ClientSettings::getAppName(),
        ]);
    }

    protected function getMailParams($notifiable)
    {
        return [
            'url_reset_password' => $this->getAppResetPasswordUrl($notifiable),
            'expired_at' => $notifiable->passwordResetExpiredAt, // user model
        ];
    }

    public function getAppResetPasswordUrl($notifiable)
    {
        return implode('/', [
            ClientSettings::getAppUrl(),
            trim($this->appResetPasswordPath, '/'),
            $this->token,
        ]);
    }
}

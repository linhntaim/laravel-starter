<?php

namespace App\Notifications;

use App\Notifications\Base\NowNotification;

class ResetPasswordNotification extends NowNotification
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
        return $this->__transNotification('mail_subject', [
            'app_name' => $this->getClientAppName(),
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
            $this->clientAppUrl,
            trim($this->appResetPasswordPath, '/'),
            // $notifiable->preferredEmail(),
            $this->token,
        ]);
    }
}

<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications;

use App\Models\Base\IUser;
use App\Notifications\Base\AdminNowNotification;
use App\Utils\ClientSettings\Facade;

class AdminResetPasswordNotification extends AdminNowNotification
{
    protected $token;
    protected $appResetPasswordPath;

    public function __construct($token, IUser $notifier = null)
    {
        parent::__construct($notifier);

        $this->token = $token;

        $this->appResetPasswordPath = request()->input('app_reset_password_path');
    }

    public function shouldMail()
    {
        return true;
    }

    protected function getMailTemplate(IUser $notifiable)
    {
        return 'admin_password_reset';
    }

    protected function getMailSubject(IUser $notifiable)
    {
        return $this->__transMailWithModule('mail_subject', [
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

<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications;

use App\Mail\PasswordResetMailable;
use App\Models\Base\INotifiable;
use App\Models\Base\INotifier;
use App\Models\Base\IUser;
use App\Notifications\Base\NowNotification;
use App\Utils\ClientSettings\Facade;

class UserResetPasswordNotification extends NowNotification
{
    protected $token;

    protected $appResetPasswordPath;

    public function __construct($token, INotifier $notifier = null)
    {
        parent::__construct($notifier);

        $this->token = $token;

        $this->appResetPasswordPath = request()->input('app_reset_password_path');
    }

    public function shouldMail()
    {
        return true;
    }

    /**
     * @param INotifiable|IUser $notifiable
     * @return PasswordResetMailable
     */
    protected function getMailable($notifiable)
    {
        return new PasswordResetMailable();
    }

    /**
     * @param INotifiable|IUser $notifiable
     * @return array
     */
    protected function getMailParams($notifiable)
    {
        return [
            'url_reset_password' => $this->getAppResetPasswordUrl($notifiable),
            'expired_at' => $notifiable->getPasswordResetExpiredAt(),
            'name' => $notifiable->preferredName(),
        ];
    }

    /**
     * @param INotifiable|IUser $notifiable
     * @return string
     */
    public function getAppResetPasswordUrl($notifiable)
    {
        return implode('/', [
            Facade::getAppUrl(),
            trim($this->appResetPasswordPath, '/'),
            $this->token,
        ]);
    }
}

<?php

namespace App\Notifications;

use App\Mail\EmailVerificationMailable;
use App\Models\Base\INotifiable;
use App\Models\Base\INotifier;
use App\Models\Base\IUser;
use App\Models\Base\IUserVerifyEmail;
use App\Notifications\Base\NowNotification;
use App\Utils\ClientSettings\Facade;

class UserEmailVerificationNotification extends NowNotification
{
    protected $token;

    protected $appVerifyEmailPath;

    public function __construct($token, INotifier $notifier = null)
    {
        parent::__construct($notifier);

        $this->token = $token;

        $this->appVerifyEmailPath = request()->input('app_verify_email_path');
    }

    public function shouldMail()
    {
        return true;
    }

    /**
     * @param INotifiable|IUser $notifiable
     * @return EmailVerificationMailable
     */
    protected function getMailable($notifiable)
    {
        return new EmailVerificationMailable();
    }

    /**
     * @param INotifiable|IUser|IUserVerifyEmail $notifiable
     * @return array
     */
    protected function getMailParams($notifiable)
    {
        return [
            'url_verify_email' => $this->getAppVerifyEmailUrl($notifiable),
            'expired_at' => ($expiredAt = $notifiable->getEmailVerificationExpiredAt()) ? Facade::dateTimer()->compound(
                'shortDate',
                ' ',
                'shortTime',
                $expiredAt
            ) : null,
            'name' => $notifiable->preferredName(),
        ];
    }

    /**
     * @param INotifiable|IUser $notifiable
     * @return string
     */
    public function getAppVerifyEmailUrl($notifiable)
    {
        return implode('/', [
            Facade::getAppUrl(),
            trim($this->appVerifyEmailPath, '/'),
            $this->token,
        ]);
    }
}
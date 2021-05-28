<?php

namespace App\Notifications;

use App\Mail\EmailVerificationMailable;
use App\Models\Base\INotifiable;
use App\Models\Base\INotifier;
use App\Models\Base\IUser;
use App\Models\Base\IUserVerifyEmail;
use App\Notifications\Base\NowNotification;
use App\Utils\ClientSettings\Facade;

class EmailVerificationNotification extends NowNotification
{
    public static $defaultAppVerifyEmailPath = 'auth/verify-email';

    protected $appVerifyEmailPath;

    public function __construct(INotifier $notifier = null)
    {
        parent::__construct($notifier);

        $this->appVerifyEmailPath =
            request()->input(
                'app_verify_email_path',
                Facade::getPath('verify_email') ?: static::$defaultAppVerifyEmailPath
            );
    }

    public function shouldMail()
    {
        return true;
    }

    /**
     * @param INotifiable|IUser|IUserVerifyEmail $notifiable
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
            'expired_at' => ($expiredAt = $notifiable->getEmailVerificationExpiredAt()) ?
                $this->formatExpiredAt($expiredAt) : null,
            'name' => $notifiable->preferredName(),
        ];
    }

    protected function formatExpiredAt($expiredAt)
    {
        return Facade::dateTimer()->compound(
            'shortDate',
            ' ',
            'shortTime',
            $expiredAt
        );
    }

    /**
     * @param INotifiable|IUser|IUserVerifyEmail $notifiable
     * @return string
     */
    public function getAppVerifyEmailUrl($notifiable)
    {
        return implode('/', [
            Facade::getAppUrl(),
            trim($this->appVerifyEmailPath, '/'),
            $notifiable->email_verified_code,
        ]);
    }
}
<?php

namespace App\Notifications\Base;

use App\Configuration;
use App\ModelRepositories\UserRepository;
use App\Models\Base\IUser;
use App\Utils\ClassTrait;
use App\Utils\ClientSettings\DateTimer;
use App\Utils\Facades\ClientSettings;
use App\Utils\Mail\TemplateMailable;
use App\Utils\Mail\TemplateNowMailable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Notification;

abstract class NowNotification extends BaseNotification
{
    use ClassTrait;

    const VIA_DATABASE = 'database';
    const VIA_WEB = 'broadcast';
    const VIA_MAIL = 'mail';
    const VIA_IOS = 'ios';
    const VIA_ANDROID = 'android';

    const NAME = 'now_notification';

    protected static function __transCurrentModule()
    {
        return 'notification';
    }

    public $shouldStore;
    public $shouldWeb;
    public $shouldMail;
    public $shouldIos;
    public $shouldAndroid;

    /**
     * @var IUser
     */
    public $notifier;

    public function __construct(IUser $notifier = null)
    {
        $this->shouldWeb = false;
        $this->shouldStore = false;
        $this->shouldMail = false;
        $this->shouldIos = false;
        $this->shouldAndroid = false;

        $this->setNotifier($notifier);
    }

    public function setNotifier(IUser $notifier = null)
    {
        $this->notifier = empty($notifier) ?
            (new UserRepository())->getById(Configuration::USER_SYSTEM_ID)
            : $notifier;

        return $this;
    }

    public function shouldWeb()
    {
        $this->shouldWeb = true;
        return $this;
    }

    public function shouldStore()
    {
        $this->shouldStore = true;
        return $this;
    }

    public function shouldIos()
    {
        $this->shouldIos = true;
        return $this;
    }

    public function shouldAndroid()
    {
        $this->shouldAndroid = true;
        return $this;
    }

    public function shouldMail()
    {
        $this->shouldMail = true;
        return $this;
    }

    protected function shouldSomething()
    {
        return $this->shouldMail || $this->shouldWeb || $this->shouldStore
            || $this->shouldIos || $this->shouldAndroid;
    }

    public function via(IUser $notifiable)
    {
        $via = [];
        if ($this->shouldStore) {
            $via[] = static::VIA_DATABASE;
        }
        if ($this->shouldWeb) {
            $via[] = static::VIA_WEB;
        }
        if ($this->shouldMail) {
            $via[] = static::VIA_MAIL;
        }
        if ($this->shouldIos) {
            $via[] = static::VIA_IOS;
        }
        if ($this->shouldAndroid) {
            $via[] = static::VIA_ANDROID;
        }
        return $via;
    }

    public function beforeNotifying($via, IUser $notifiable)
    {

    }

    public function afterNotifying($via, IUser $notifiable)
    {

    }

    protected function resolveData($via, IUser $notifiable, $dataCallback)
    {
        return ClientSettings::temporaryFromUser($notifiable, function () use ($via, $notifiable, $dataCallback) {
            return $dataCallback($notifiable);
        });
    }

    public function toBroadcast(IUser $notifiable)
    {
        return $this->resolveData(static::VIA_WEB, $notifiable, function (IUser $notifiable) {
            return $this->dataBroadcast($notifiable);
        });
    }

    protected function dataBroadcast(IUser $notifiable)
    {
        return (new BroadcastMessage([
            'id' => $this->id,
            'data' => $this->dataArray($notifiable),
            'created_at' => DateTimer::syncNow(),
            'shown_created_at' => ClientSettings::dateTimer()->compound('shortDate', ' ', 'shortTime'),
            'is_read' => false,
        ]));
    }

    public function toDatabase(IUser $notifiable)
    {
        return $this->resolveData(static::VIA_DATABASE, $notifiable, function ($notifiable) {
            return $this->dataDatabase($notifiable);
        });
    }

    protected function dataDatabase(IUser $notifiable)
    {
        return [
            'sender_id' => $this->notifier->id,
        ];
    }

    public function toMail(IUser $notifiable)
    {
        return $this->resolveData(static::VIA_MAIL, $notifiable, function ($notifiable) {
            return $this->dataMail($notifiable);
        });
    }

    protected function dataMail(IUser $notifiable)
    {
        $mailable = $this->getMailNow($notifiable) ? TemplateNowMailable::class : TemplateMailable::class;
        return new ($mailable)(
            $this->getMailTemplate($notifiable),
            array_merge([
                TemplateMailable::EMAIL_TO => $notifiable->preferredEmail(),
                TemplateMailable::EMAIL_TO_NAME => $notifiable->preferredName(),
                TemplateMailable::EMAIL_SUBJECT => $this->getMailSubject($notifiable),
            ], $this->getMailParams($notifiable)),
            $this->getMailUseLocalizedTemplate($notifiable),
            $this->locale
        );
    }

    public function toIos(IUser $notifiable)
    {
        return $this->resolveData(static::VIA_IOS, $notifiable, function ($notifiable) {
            return $this->dataIos($notifiable);
        });
    }

    protected function dataIos(IUser $notifiable)
    {
        return [
            'aps' => [
                'alert' => [
                    'title' => $this->getTitle($notifiable),
                    'body' => $this->getContent($notifiable, false),
                ],
                'sound' => 'default',
                'badge' => 1,
            ],
            'extraPayLoad' => [
                'action' => $this->getAction($notifiable),
            ],
        ];
    }

    public function toAndroid(IUser $notifiable)
    {
        return $this->resolveData(static::VIA_ANDROID, $notifiable, function ($notifiable) {
            return $this->dataAndroid($notifiable);
        });
    }

    protected function dataAndroid(IUser $notifiable)
    {
        return [
            'notification' => [
                'title' => $this->getTitle($notifiable),
                'body' => $this->getContent($notifiable, false),
                'sound' => 'default',
            ],
            'data' => [
                'action' => $this->getAction($notifiable),
            ],
        ];
    }

    protected function dataArray(IUser $notifiable)
    {
        return [
            'name' => $this::NAME,
            'image' => $this->notifier->preferredAvatarUrl(),
            'content' => $this->getContent($notifiable, false),
            'html_content' => $this->getContent($notifiable),
            'action' => $this->getAction($notifiable),
        ];
    }

    protected function getTitle(IUser $notifiable)
    {
        return null;
    }

    protected function getContent(IUser $notifiable, $html = true)
    {
        return null;
    }

    protected function getAction(IUser $notifiable)
    {
        return null;
    }

    protected function getMailTemplate(IUser $notifiable)
    {
        return null;
    }

    protected function getMailSubject(IUser $notifiable)
    {
        return null;
    }

    protected function getMailParams(IUser $notifiable)
    {
        return [];
    }

    protected function getMailUseLocalizedTemplate(IUser $notifiable)
    {
        return true;
    }

    protected function getMailNow(IUser $notifiable)
    {
        return true;
    }

    protected function getNotifiables()
    {
        return null;
    }

    public function cannotSend($notifiables)
    {
        return empty($notifiables)
            || ($notifiables instanceof Collection && $notifiables->count() <= 0)
            || !$this->shouldSomething();
    }

    public function send()
    {
        $notifiables = $this->getNotifiables();

        if ($this->cannotSend($notifiables)) return false;

        Notification::send($notifiables, $this);

        return true;
    }
}

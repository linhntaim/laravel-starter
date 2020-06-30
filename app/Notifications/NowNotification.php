<?php

namespace App\Notifications;

use App\Configuration;
use App\ModelRepositories\UserRepository;
use App\Models\User;
use App\Utils\AppOptionHelper;
use App\Utils\ClassTrait;
use App\Utils\DateTimeHelper;
use App\Utils\Mail\TemplateMailable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

abstract class NowNotification extends BaseNotification
{
    use SerializesModels, ClassTrait;

    const VIA_DATABASE = 'database';
    const VIA_WEB = 'broadcast';
    const VIA_MAIL = 'mail';
    const VIA_IOS = 'ios';
    const VIA_ANDROID = 'android';

    const NAME = 'now_notification';

    protected static function __transNotification($name, $replace = [], $locale = null)
    {
        return static::__transWithSpecificModule($name, 'notification', $replace, $locale);
    }

    protected static function __transWithAppInfoNotification($name, $replace = [], $locale = null)
    {
        $appOptions = AppOptionHelper::getInstance();
        $replace = array_merge($replace, [
            'company_short_name' => $appOptions->getBy('company_short_name'),
        ]);
        return static::__transWithSpecificModule($name, 'notification', $replace, $locale);
    }

    public $shouldStore;
    public $shouldWeb;
    public $shouldMail;
    public $shouldIos;
    public $shouldAndroid;

    protected $currentVia;

    /**
     * @var User
     */
    public $fromUser;

    protected $prepared;

    public function __construct($fromUser = null)
    {
        $this->shouldWeb = false;
        $this->shouldStore = false;
        $this->shouldMail = false;
        $this->shouldIos = false;
        $this->shouldAndroid = false;

        $this->prepared = false;

        $this->setFromUser($fromUser);
    }

    public function __destruct()
    {
        $this->destroyClientApp();
    }

    public function setFromUser(User $user = null)
    {
        $this->fromUser = empty($user) ?
            (new UserRepository())->getById(Configuration::USER_SYSTEM_ID) : $user;

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

    public function via($notifiable)
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

    protected function beforeNotifying($notifiable)
    {

    }

    protected function afterNotifying($notifiable)
    {

    }

    protected function notify($via, $notifiable, $dataCallback)
    {
        $this->currentVia = $via;

        if (!$this->prepared) {
            $this->beforeNotifying($notifiable);
            $this->prepared = true;
        }
        $data = $dataCallback($notifiable);
        $this->afterNotifying($notifiable);
        return $data;
    }

    public function toBroadcast($notifiable)
    {
        return $this->notify(static::VIA_WEB, $notifiable, function ($notifiable) {
            return $this->dataBroadcast($notifiable);
        });
    }

    protected function dataBroadcast($notifiable)
    {
        $dateTimeHelper = DateTimeHelper::fromUser($notifiable);
        return (new BroadcastMessage([
            'id' => $this->id,
            'data' => $this->dataArray($notifiable),
            'created_at' => DateTimeHelper::syncNow(),
            'shown_created_at' => $dateTimeHelper->compound('shortDate', ' ', 'shortTime'),
            'is_read' => false,
        ]));
    }

    public function toDatabase($notifiable)
    {
        return $this->notify(static::VIA_DATABASE, $notifiable, function ($notifiable) {
            return $this->dataDatabase($notifiable);
        });
    }

    protected function dataDatabase($notifiable)
    {
        return [
            'sender_id' => $this->fromUser->id,
        ];
    }

    public function toMail($notifiable)
    {
        return $this->notify(static::VIA_MAIL, $notifiable, function ($notifiable) {
            return $this->dataMail($notifiable);
        });
    }

    protected function dataMail($notifiable)
    {
        return new TemplateMailable(
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

    public function toIos($notifiable)
    {
        return $this->notify(static::VIA_IOS, $notifiable, function ($notifiable) {
            return $this->dataIos($notifiable);
        });
    }

    protected function dataIos($notifiable)
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

    public function toAndroid($notifiable)
    {
        return $this->notify(static::VIA_ANDROID, $notifiable, function ($notifiable) {
            return $this->dataAndroid($notifiable);
        });
    }

    protected function dataAndroid($notifiable)
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

    protected function dataArray($notifiable)
    {
        return [
            'name' => $this::NAME,
            'image' => $this->fromUser->url_avatar,
            'content' => $this->getContent($notifiable, false),
            'html_content' => $this->getContent($notifiable),
            'action' => $this->getAction($notifiable),
        ];
    }

    protected function getTitle($notifiable)
    {
        return null;
    }

    protected function getContent($notifiable, $html = true)
    {
        return null;
    }

    protected function getAction($notifiable)
    {
        return null;
    }

    protected function getMailTemplate($notifiable)
    {
        return null;
    }

    protected function getMailSubject($notifiable)
    {
        return null;
    }

    protected function getMailParams($notifiable)
    {
        return [];
    }

    protected function getMailUseLocalizedTemplate($notifiable)
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

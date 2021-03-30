<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications\Base;

use App\Exceptions\AppException;
use App\ModelRepositories\AdminRepository;
use App\Models\Base\IUser;
use App\Models\User;
use App\Utils\ClassTrait;
use App\Utils\ClientSettings\DateTimer;
use App\Utils\ClientSettings\Facade;
use App\Utils\ConfigHelper;
use App\Utils\Mail\TemplateMailable;
use App\Utils\Mail\TemplateNowMailable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Notification;

abstract class NowNotification extends BaseNotification
{
    use ClassTrait;

    const VIA_DATABASE = 'database';
    const VIA_BROADCAST = 'broadcast';
    const VIA_MAIL = 'mail';
    const VIA_IOS = 'ios';
    const VIA_ANDROID = 'android';

    const NAME = 'now_notification';

    protected static function __transCurrentModule()
    {
        return 'notification';
    }

    /**
     * @var IUser|Model
     */
    public $notifier;

    public function __construct(IUser $notifier = null)
    {
        $this->setNotifier($notifier);
    }

    /**
     * @return IUser
     */
    public function getNotifier()
    {
        return $this->notifier;
    }

    public function setNotifier(IUser $notifier = null)
    {
        $this->notifier = empty($notifier) ?
            (new AdminRepository())->getById(User::USER_SYSTEM_ID)
            : $notifier;

        return $this;
    }

    public function shouldDatabase()
    {
        return false;
    }

    public function shouldBroadcast()
    {
        return false;
    }

    public function shouldMail()
    {
        return false;
    }

    public function shouldIos()
    {
        return false;
    }

    public function shouldAndroid()
    {
        return false;
    }

    protected function shouldSomething()
    {
        return $this->shouldDatabase() || $this->shouldBroadcast() || $this->shouldMail()
            || $this->shouldIos() || $this->shouldAndroid();
    }

    public function via(IUser $notifiable)
    {
        $via = [];
        if ($this->shouldDatabase()) {
            if (!ConfigHelper::get('notification.via.database')) {
                throw new AppException('Notification via database is not enabled');
            }
            $via[] = static::VIA_DATABASE;
        }
        if ($this->shouldBroadcast()) {
            $via[] = static::VIA_BROADCAST;
        }
        if ($this->shouldMail()) {
            $via[] = static::VIA_MAIL;
        }
        if ($this->shouldIos()) {
            $via[] = static::VIA_IOS;
        }
        if ($this->shouldAndroid()) {
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
        return Facade::temporaryFromUser($notifiable, function () use ($via, $notifiable, $dataCallback) {
            return $dataCallback($notifiable);
        });
    }

    public function toBroadcast(IUser $notifiable)
    {
        return $this->resolveData(static::VIA_BROADCAST, $notifiable, function (IUser $notifiable) {
            return $this->dataBroadcast($notifiable);
        });
    }

    protected function dataBroadcast(IUser $notifiable)
    {
        return (new BroadcastMessage([
            'id' => $this->id,
            'data' => $this->dataArray($notifiable),
            'created_at' => DateTimer::syncNow(),
            'shown_created_at' => Facade::dateTimer()->compound('shortDate', ' ', 'shortTime'),
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
            'notifier_id' => $this->notifier->getKey(),
            'notifier_type' => get_class($this->notifier),
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
        $mailable = $this->getMailNow($notifiable) ? $this->getNowMailable($notifiable) : $this->getMailable($notifiable);
        return new $mailable(
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
            'name' => $this->getName(),
            'image' => $this->getImage($notifiable),
            'content' => $this->getContent($notifiable, false),
            'html_content' => $this->getContent($notifiable),
            'action' => $this->getAction($notifiable),
        ];
    }

    public function getName()
    {
        return static::NAME;
    }

    public function getImage(IUser $notifiable)
    {
        return $this->notifier->preferredAvatarUrl();
    }

    public function getTitle(IUser $notifiable)
    {
        return static::__transWithCurrentModule('title');
    }

    public function getContent(IUser $notifiable, $html = true)
    {
        return static::__transWithCurrentModule('content');
    }

    public function getAction(IUser $notifiable)
    {
        return null;
    }

    protected function getNowMailable(IUser $notifiable)
    {
        return TemplateNowMailable::class;
    }

    protected function getMailable(IUser $notifiable)
    {
        return TemplateMailable::class;
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

    /**
     * @return Collection|Model[]|array|Model|null
     */
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

    /**
     * @param Collection|Model[]|array|Model|null $notifiables
     * @return bool
     */
    public function send($notifiables = null)
    {
        $notifiables = $notifiables ? $notifiables : $this->getNotifiables();

        if ($notifiables instanceof Collection) {
            $notifiables = $notifiables->toArray();
        } elseif ($notifiables instanceof Model) {
            $notifiables = [$notifiables];
        }

        if ($this->cannotSend($notifiables)) return false;

        Notification::send($notifiables, $this);

        return true;
    }
}

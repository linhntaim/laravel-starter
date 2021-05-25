<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications\Base;

use App\Exceptions\AppException;
use App\Mail\Base\MailAddress;
use App\Mail\Base\NowMailable;
use App\ModelRepositories\AdminRepository;
use App\Models\Base\ILocalizable;
use App\Models\Base\INotifiable;
use App\Models\Base\INotifier;
use App\Models\User;
use App\Utils\ClassTrait;
use App\Utils\ClientSettings\DateTimer;
use App\Utils\ClientSettings\Facade;
use App\Utils\ClientSettings\Traits\IndependentClientTrait;
use App\Utils\ConfigHelper;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Throwable;

abstract class NowNotification extends BaseNotification
{
    use ClassTrait, IndependentClientTrait;

    public const NAME = 'now_notification';
    public const VIA_DATABASE = 'database';
    public const VIA_BROADCAST = 'broadcast';
    public const VIA_MAIL = 'mail';
    public const VIA_IOS = 'ios';
    public const VIA_ANDROID = 'android';

    protected static function __transCurrentModule()
    {
        return 'notification';
    }

    /**
     * @var INotifier
     */
    public $notifier;

    public function __construct(INotifier $notifier = null)
    {
        $this->locale(Facade::getLocale())
            ->setNotifier($notifier);
    }

    /**
     * @return INotifier
     */
    public function getNotifier()
    {
        return $this->notifier;
    }

    public function setNotifier(INotifier $notifier = null)
    {
        $this->notifier = is_null($notifier) ?
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
            || $this->shouldIos()
            || $this->shouldAndroid();
    }

    public function via(INotifiable $notifiable)
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

    public function beforeNotifying($via, INotifiable $notifiable)
    {
    }

    public function afterNotifying($via, INotifiable $notifiable)
    {
    }

    protected function resolveData($via, INotifiable $notifiable, $dataCallback)
    {
        $this->independentClientApply();
        return Facade::temporary(
            $notifiable instanceof ILocalizable ? $notifiable->preferredSettings() : [],
            function () use ($via, $notifiable, $dataCallback) {
                try {
                    return $dataCallback($notifiable);
                }
                catch (Throwable $e) {
                    if (!($this instanceof Notification)) {
                        $this->failed($e);
                    }
                    throw $e;
                }
            }
        );
    }

    public function failed(Throwable $e)
    {
    }

    public function toBroadcast(INotifiable $notifiable)
    {
        return $this->resolveData(static::VIA_BROADCAST, $notifiable, function (INotifiable $notifiable) {
            return $this->dataBroadcast($notifiable);
        });
    }

    protected function dataBroadcast(INotifiable $notifiable)
    {
        return (new BroadcastMessage([
            'id' => $this->id,
            'data' => $this->dataArray($notifiable),
            'created_at' => DateTimer::syncNow(),
            'shown_created_at' => Facade::dateTimer()->compound('shortDate', ' ', 'shortTime'),
            'is_read' => false,
        ]));
    }

    public function toDatabase(INotifiable $notifiable)
    {
        return $this->resolveData(static::VIA_DATABASE, $notifiable, function ($notifiable) {
            return $this->dataDatabase($notifiable);
        });
    }

    protected function dataDatabase(INotifiable $notifiable)
    {
        return [
            'notifier_id' => $this->notifier->getKey(),
            'notifier_type' => get_class($this->notifier),
        ];
    }

    public function toMail(INotifiable $notifiable)
    {
        return $this->resolveData(static::VIA_MAIL, $notifiable, function ($notifiable) {
            return $this->dataMail($notifiable);
        });
    }

    protected function dataMail(INotifiable $notifiable)
    {
        $mail = MailAddress::from($notifiable);
        $mailable = $this->getMailable($notifiable)
            ->locale($this->getMailLocale($notifiable))
            ->with($this->getMailParams($notifiable));
        return $mail ? $mailable->clearTos()->to($mail->address, $mail->name) : $mailable;
    }

    /**
     * @param INotifiable $notifiable
     * @return NowMailable|null
     */
    protected function getMailable(INotifiable $notifiable)
    {
        return null;
    }

    protected function getMailLocale(INotifiable $notifiable)
    {
        return $notifiable instanceof HasLocalePreference ?
            $notifiable->preferredLocale() : $this->locale;
    }

    protected function getMailParams(INotifiable $notifiable)
    {
        return [];
    }

    public function toIos(INotifiable $notifiable)
    {
        return $this->resolveData(static::VIA_IOS, $notifiable, function ($notifiable) {
            return $this->dataIos($notifiable);
        });
    }

    protected function dataIos(INotifiable $notifiable)
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

    public function toAndroid(INotifiable $notifiable)
    {
        return $this->resolveData(static::VIA_ANDROID, $notifiable, function ($notifiable) {
            return $this->dataAndroid($notifiable);
        });
    }

    protected function dataAndroid(INotifiable $notifiable)
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

    protected function dataArray(INotifiable $notifiable)
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

    public function getImage(INotifiable $notifiable)
    {
        return $this->notifier->preferredAvatarUrl();
    }

    public function getTitle(INotifiable $notifiable)
    {
        return static::__transWithCurrentModule('title');
    }

    public function getContent(INotifiable $notifiable, $html = true)
    {
        return static::__transWithCurrentModule('content');
    }

    public function getAction(INotifiable $notifiable)
    {
        return null;
    }

    /**
     * @return Collection|Model[]|array|Model|null
     */
    protected function getNotifiables()
    {
        return null;
    }

    /**
     * @param Collection|null $notifiables
     * @return bool
     */
    public function cannotSend($notifiables)
    {
        return is_null($notifiables)
            || $notifiables->count() <= 0
            || !$this->shouldSomething();
    }

    /**
     * @param Collection|Model[]|array|Model|null $notifiables
     * @return bool
     */
    public function send($notifiables = null)
    {
        $notifiables = $notifiables ?: $this->getNotifiables();

        if (!($notifiables instanceof Collection)) {
            if (is_array($notifiables)) {
                $notifiables = new Collection($notifiables);
            }
            else {
                $notifiables = new Collection([$notifiables]);
            }
        }

        if ($this->cannotSend($notifiables)) {
            return false;
        }

        NotificationFacade::send($notifiables, $this);

        return true;
    }

    /**
     * @return array
     */
    protected function getAnyRoutes()
    {
        return [];
    }

    /**
     * @param array $routes
     * @return bool
     */
    public function cannotSendAny(array $routes = [])
    {
        return !$this->shouldSomething();
    }

    /**
     * @param array|null $routes
     * @return bool
     */
    public function sendAny(array $routes = null)
    {
        $routes = $routes ?: $this->getAnyRoutes();

        if ($this->cannotSendAny($routes)) {
            return false;
        }

        $notifiable = new AnonymousNotifiable;
        foreach ($routes as $channel => $to) {
            $notifiable->route($channel, $to);
        }
        $notifiable->notify($this);

        return true;
    }
}

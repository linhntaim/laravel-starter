<?php

namespace App\Providers;

use App\Events\Listeners\OnMessageSending;
use App\Events\Listeners\OnMessageSent;
use App\Events\Listeners\OnNotificationSending;
use App\Events\Listeners\OnNotificationSent;
use App\Events\Listeners\OnQueryExecuted;
use App\Events\MailTestingEvent;
use App\Events\TestingEvent;
use App\Events\Listeners\OnMailTestingEvent;
use App\Events\Listeners\OnTestingEvent;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        QueryExecuted::class => [
            OnQueryExecuted::class,
        ],
        NotificationSending::class => [
            OnNotificationSending::class,
        ],
        NotificationSent::class => [
            OnNotificationSent::class,
        ],
        MessageSending::class => [
            OnMessageSending::class,
        ],
        MessageSent::class => [
            OnMessageSent::class,
        ],
        TestingEvent::class => [
            OnTestingEvent::class,
        ],
        MailTestingEvent::class => [
            OnMailTestingEvent::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}

<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Providers;

use App\Events\Listeners\OnJobProcessing;
use App\Events\Listeners\OnMailTestingEvent;
use App\Events\Listeners\OnMessageSending;
use App\Events\Listeners\OnMessageSent;
use App\Events\Listeners\OnNotificationSending;
use App\Events\Listeners\OnNotificationSent;
use App\Events\Listeners\OnPasswordResetAutomatically;
use App\Events\Listeners\OnQueryExecuted;
use App\Events\Listeners\OnTestingEvent;
use App\Events\TestMailEvent;
use App\Events\PasswordResetAutomaticallyEvent;
use App\Events\TestEvent;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

// TODO: Extra Events

// TODO
// TODO: Extra Listeners

// TODO

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
        JobProcessing::class => [
            OnJobProcessing::class,
        ],
        JobProcessed::class => [
            OnJobProcessing::class,
        ],
        TestEvent::class => [
            OnTestingEvent::class,
        ],
        TestMailEvent::class => [
            OnMailTestingEvent::class,
        ],
        PasswordResetAutomaticallyEvent::class => [
            OnPasswordResetAutomatically::class,
        ],
        // TODO: Register Events with Listeners

        // TODO
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

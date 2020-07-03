<?php

namespace App\Providers;

use App\Events\MailTestingEvent;
use App\Events\TestingEvent;
use App\Listeners\OnMailTestingEvent;
use App\Listeners\OnTestingEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
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

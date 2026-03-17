<?php

namespace App\Providers;

use App\Models\ClientQuestion;
use App\Models\ClientQuestion2;
use App\Models\Ticket;
use App\Observers\ClientQuestion2Observer;
use App\Observers\ClientQuestionObserver;
use App\Observers\TicketObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        ClientQuestion::observe(ClientQuestionObserver::class);
        ClientQuestion2::observe(ClientQuestion2Observer::class);
        Ticket::observe(TicketObserver::class);
    }
}

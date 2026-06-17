<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Listeners\NotifyAdmin;
use App\Listeners\SendPaymentReceipt;
use App\Listeners\SendTicketNotifications;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        OrderCreated::class => [
            SendTicketNotifications::class,
            SendPaymentReceipt::class,
            NotifyAdmin::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->booting(function () {
            $events = $this->app['events'];

            foreach ($this->listen as $event => $listeners) {
                $existing = $events->getListeners($event);

                foreach ($listeners as $listener) {
                    $found = false;
                    foreach ($existing as $handler) {
                        if ($handler instanceof $listener) {
                            $found = true;
                            break;
                        }
                    }

                    if (! $found) {
                        $events->listen($event, $listener);
                    }
                }
            }
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

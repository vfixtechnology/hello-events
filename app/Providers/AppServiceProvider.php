<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (class_exists(\Vfixtechnology\RazorpayPayment\RazorpayPaymentServiceProvider::class)) {
            $this->app->register(\Vfixtechnology\RazorpayPayment\RazorpayPaymentServiceProvider::class);
        }

        if (class_exists(\Vfixtechnology\StripePayment\StripePaymentServiceProvider::class)) {
            $this->app->register(\Vfixtechnology\StripePayment\StripePaymentServiceProvider::class);
        }

        if (class_exists(\Vfixtechnology\TicketScanner\TicketScannerServiceProvider::class)) {
            $this->app->register(\Vfixtechnology\TicketScanner\TicketScannerServiceProvider::class);
        }
    }

    public function boot(): void
    {
        Paginator::useBootstrapFour();

        if (Schema::hasTable('settings')) {
            View::share('setting', Setting::first() ?? new Setting());
        } else {
            View::share('setting', new Setting());
        }

        Gate::before(function ($user, $ability) {
            return $user->id == 1 ? true : null;
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Xendit\Xendit;
use App\Models\Subscription\Subscription;
use App\Observers\SubscriptionObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
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
        if (!app()->runningInConsole()) {
            if (class_exists(\Xendit\Xendit::class)) {
                \Xendit\Xendit::setApiKey(env('XENDIT_SECRET_KEY'));
            }
        }

        Subscription::observe(SubscriptionObserver::class);
    }
}

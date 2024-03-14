<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class WooCommerceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WooCommerceService::class, function ($app) {
            return new WooCommerceService(
                config('services.woocommerce.url'),
                config('services.woocommerce.consumer_key'),
                config('services.woocommerce.consumer_secret')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

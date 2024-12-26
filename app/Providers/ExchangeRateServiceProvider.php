<?php

namespace App\Providers;

use App\Service\ExchangeRateInterface;
use App\Service\ExchangeRateProvider;
use Illuminate\Support\ServiceProvider;

class ExchangeRateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ExchangeRateInterface::class, ExchangeRateProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

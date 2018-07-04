<?php

namespace App\Providers;

use App\Repository\ExchangeRateDbRepository;
use App\Service\ExchangeRateInterface;
use App\Service\ExchangeRateService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        app()->when(ExchangeRateService::class)->needs(ExchangeRateInterface::class)->give(ExchangeRateDbRepository::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

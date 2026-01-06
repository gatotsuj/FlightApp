<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(
            \App\Interfaces\TransactionRepositoryInterface::class,
            \App\Repositories\TransactionRepository::class
        );
        $this->app->bind(
            \App\Interfaces\FlightRepositoryInterface::class,
            \App\Repositories\FlightRepository::class
        );
        $this->app->bind(
            \App\Interfaces\AirlineRepositoryInterface::class,
            \App\Repositories\AirlineRepository::class
        );

        $this->app->bind(
            \App\Interfaces\AirportRepositoryInterface::class,
            \App\Repositories\AirportRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

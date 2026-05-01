<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\RestaurantRepositoryInterface::class,
            \App\Repositories\MockRestaurantRepository::class
            // TODO: swap to RealZomatoRepository when API key available
        );
    }

    public function boot(): void
    {
        //
    }
}
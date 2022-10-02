<?php

namespace NawaGrow\Contractor;

use Illuminate\Support\ServiceProvider;



class ContractorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(ResponseServiceProvider::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function provides()
    {
        return [
            ResponseServiceProvider::class,
            RouteServiceProvider::class,
        ];
    }
}

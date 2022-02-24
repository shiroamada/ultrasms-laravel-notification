<?php

namespace NotificationChannels\UltraSms;

use Illuminate\Support\ServiceProvider;

class UltraSmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(UltraSmsApi::class, function () {
            $config = config('services.ultrasms');

            return new UltraSmsApi($config);
        });
    }
}

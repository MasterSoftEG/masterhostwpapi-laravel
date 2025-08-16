<?php

namespace masterhosteg;

use Illuminate\Support\ServiceProvider;

class masterhostegServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/masterhosteg.php' => config_path('masterhosteg.php'),
        ], 'masterhosteg-config');

        if (file_exists(__DIR__.'/../routes/webhooks.php')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/webhooks.php');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/masterhosteg.php', 'masterhosteg'
        );
        $this->app->singleton('masterhosteg.client', function ($app) {
            return new \masterhosteg\masterhostClient();
        });
    }
} 
<?php

namespace BrandStudio\Bonus;

use Illuminate\Support\ServiceProvider;
use BrandStudio\Bonus\BonusService;

class BonusServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/bonus.php', 'bonus');
        $this->loadRoutesFrom(__DIR__.'/routes/bonus.php');

        if ($this->app->runningInConsole()) {
            $this->publish();
        }

        $this->app->bind('brandstudio_bonus',function() {
            return new BonusService(config('bonus'));
        });

    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publish();
        }
    }

    public function publish()
    {
        $this->publishes([
            __DIR__.'/config/bonus.php' => config_path('bonus.php')
        ], 'config');
    }

}

<?php

namespace Nsd7\LaravelActivitylogUi;

use Illuminate\Support\ServiceProvider;

class LaravelActivitylogUiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/activitylog-ui.php', 'activitylog-ui');
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'activitylog-ui');

        if (config('activitylog-ui.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/activitylog-ui.php' => config_path('activitylog-ui.php'),
            ], 'activitylog-ui-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/activitylog-ui'),
            ], 'activitylog-ui-views');
        }
    }
}

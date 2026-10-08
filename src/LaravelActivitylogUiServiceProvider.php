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
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'activitylog-ui');

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

            $this->publishes([
                __DIR__ . '/../resources/lang' => $this->app->langPath('vendor/activitylog-ui'),
            ], 'activitylog-ui-lang');

            $this->publishes([
                __DIR__ . '/../database/migrations/add_indexes_to_activity_log_table.php.stub'
                    => $this->migrationPath('add_indexes_to_activity_log_table'),
                __DIR__ . '/../database/migrations/create_activitylog_ui_saved_views_table.php.stub'
                    => $this->migrationPath('create_activitylog_ui_saved_views_table'),
            ], 'activitylog-ui-migrations');
        }
    }

    /**
     * Timestamped destination path for a published migration.
     */
    protected function migrationPath(string $name): string
    {
        return database_path('migrations/' . date('Y_m_d_His') . '_' . $name . '.php');
    }
}

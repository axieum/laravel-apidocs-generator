<?php

namespace Axieum\ApiDocs;

use Axieum\ApiDocs\Commands\GenerateCommand;
use Illuminate\Support\ServiceProvider;

class ApiDocsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Artisan Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateCommand::class
            ]);
        }

        // Views
        $this->loadViewsFrom(__DIR__ . '/views', 'apidocs');
        $this->publishes([
            __DIR__ . '/views' => resource_path('views/vendor/apidocs')
        ], ['apidocs', 'views']);

        // Config
        $this->mergeConfigFrom(__DIR__ . '/config/apidocs.php', 'apidocs');
        $this->publishes([
            __DIR__ . '/config' => config_path()
        ], ['apidocs', 'config']);

        // Localisation
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'apidocs');
        $this->publishes([
            __DIR__ . '/lang' => resource_path('lang/vendor/apidocs')
        ], ['apidocs', 'lang']);
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
}

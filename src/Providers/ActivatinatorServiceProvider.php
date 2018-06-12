<?php

namespace Codepunk\Activatinator\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Codepunk\Activatinator\ActivatinatorBrokerManager;

class ActivatinatorServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    //protected $defer = true;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/activatinator.php');

        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'codepunk');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'codepunk');

        $this->publishes([
            __DIR__ . '/../../config/activatinator.php' => config_path('codepunk/activatinator.php')],
            'config');

        /*
        $this->publishes([
            __DIR__ . '/../../resources/lang/' => resource_path('lang/vendor/codepunk')],
            'lang');
        */

        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('/migrations')],
            'migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerActivatinatorBroker();
    }

    /**
     * Register the activation broker instance.
     *
     * @return void
     */
    protected function registerActivatinatorBroker() {
        $this->app->singleton('auth.activatinator', function (Application $app) {
            return new ActivatinatorBrokerManager($app);
        });

        $this->app->bind('auth.activatinator.broker', function (Application $app) {
            return $app->make('auth.activatinator')->broker();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['auth.activatinator', 'auth.activatinator.broker'];
    }
}

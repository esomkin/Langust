<?php namespace Goodvin\Langust;

use Illuminate\Support\ServiceProvider;

/**
 * Register the Langust package with the Laravel framework
 */
class LangustServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish a config file
        $this->publishes([

            __DIR__.'/config/langust.php' => config_path('langust.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {

    }
}
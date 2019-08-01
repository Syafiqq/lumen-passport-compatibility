<?php


namespace Syafiqq\Passport;


use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class PassportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(Connection::class, function () {
            return $this->app['db.connection'];
        });

        $this->app->singleton(\Illuminate\Hashing\HashManager::class, function ($app) {
            return new \Illuminate\Hashing\HashManager($app);
        });
    }

    /**
     * @return void
     */
    public function register()
    {
    }
}
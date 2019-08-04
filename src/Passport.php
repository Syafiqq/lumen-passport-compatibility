<?php


namespace Syafiqq\Passport;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Laravel\Lumen\Routing\Router as Router;


class Passport extends \Laravel\Passport\Passport
{
    /**
     * Binds the Passport routes into the controller.
     *
     * @param  callable|null  $callback
     * @param  array  $options
     * @return void
     */
    public static function routes($callback = null, array $options = [])
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };

        $defaultOptions = [
            'prefix' => 'oauth',
            'namespace' => '\Laravel\Passport\Http\Controllers',
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group(Arr::except($options, ['namespace']), function (Router $router) use ($callback, $options) {
            $routes = new RouteRegistrar($router, $options);
            $routes->all();
        });
    }
}
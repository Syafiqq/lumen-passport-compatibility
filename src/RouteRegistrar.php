<?php

namespace Syafiqq\Passport;

use Laravel\Lumen\Routing\Router as Router;

class RouteRegistrar
{
    /**
     * The router implementation.
     *
     * @var \Laravel\Lumen\Routing\Router
     */
    protected $router;

    /**
     * @var array
     */
    private $options;

    /**
     * Create a new route registrar instance.
     *
     * @param  \Laravel\Lumen\Routing\Router  $router
     * @param  array $options
     * @return void
     */
    public function __construct(Router $router, array $options = [])
    {
        $this->router = $router;
        $this->options = $options;
    }

    /**
     * Register routes for transient tokens, clients, and personal access tokens.
     *
     * @return void
     */
    public function all()
    {
        $this->forAccessTokens();
        $this->forTransientTokens();
        $this->forClients();
        $this->forPersonalAccessTokens();
    }

    /**
     * @param string $path
     * @return string
     */
    private function prefix($path)
    {
        if (strstr($path, '\\') === false && isset($this->options['namespace'])) return $this->options['namespace'] . '\\' . $path;

        return $path;
    }

    /**
     * Register the routes for retrieving and issuing access tokens.
     *
     * @return void
     */
    public function forAccessTokens()
    {
        $this->router->post('/token', [
            'uses' => $this->prefix('AccessTokenController@issueToken'),
            'as' => 'passport.token',
        ]);

        $this->router->group(['middleware' => ['auth']], function (Router $router) {
            $router->get('/tokens', [
                'uses' => $this->prefix('AuthorizedAccessTokenController@forUser'),
                'as' => 'passport.tokens.index',
            ]);

            $router->delete('/tokens/{token_id}', [
                'uses' => $this->prefix('AuthorizedAccessTokenController@destroy'),
                'as' => 'passport.tokens.destroy',
            ]);
        });
    }

    /**
     * Register the routes needed for refreshing transient tokens.
     *
     * @return void
     */
    public function forTransientTokens()
    {
        $this->router->post('/token/refresh', [
            'middleware' => ['auth'],
            'uses' => $this->prefix('TransientTokenController@refresh'),
            'as' => 'passport.token.refresh',
        ]);
    }

    /**
     * Register the routes needed for managing clients.
     *
     * @return void
     */
    public function forClients()
    {
        $this->router->group(['middleware' => ['auth']], function (Router $router) {
            $router->get('/clients', [
                'uses' => $this->prefix('ClientController@forUser'),
                'as' => 'passport.clients.index',
            ]);

            $router->post('/clients', [
                'uses' => $this->prefix('ClientController@store'),
                'as' => 'passport.clients.store',
            ]);

            $router->put('/clients/{client_id}', [
                'uses' => $this->prefix('ClientController@update'),
                'as' => 'passport.clients.update',
            ]);

            $router->delete('/clients/{client_id}', [
                'uses' => $this->prefix('ClientController@destroy'),
                'as' => 'passport.clients.destroy',
            ]);
        });
    }

    /**
     * Register the routes needed for managing personal access tokens.
     *
     * @return void
     */
    public function forPersonalAccessTokens()
    {
        $this->router->group(['middleware' => ['auth']], function (Router $router) {
            $router->get('/scopes', [
                'uses' => $this->prefix('ScopeController@all'),
                'as' => 'passport.scopes.index',
            ]);

            $router->get('/personal-access-tokens', [
                'uses' => $this->prefix('PersonalAccessTokenController@forUser'),
                'as' => 'passport.personal.tokens.index',
            ]);

            $router->post('/personal-access-tokens', [
                'uses' => $this->prefix('PersonalAccessTokenController@store'),
                'as' => 'passport.personal.tokens.store',
            ]);

            $router->delete('/personal-access-tokens/{token_id}', [
                'uses' => $this->prefix('PersonalAccessTokenController@destroy'),
                'as' => 'passport.personal.tokens.destroy',
            ]);
        });
    }
}

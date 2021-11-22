<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


$router->group([
    'prefix'     => 'api/v1',
    'namespace'  => 'Api',
], function () use ($router) {

    $router->post('/auth/register', 'AuthController@Register');
    $router->post('/auth/login', 'AuthController@Login');


    $router->post('/github/auto-pull', 'GitHubController@gitHubPullRequest');


    $router->group([
        'prefix'     => 'authenticate',
        'middleware' => 'auth'
    ], function() use($router) {

        $router->get('/me', 'AuthController@Me');
        $router->get('/refresh', 'AuthController@Refresh');

    });

});

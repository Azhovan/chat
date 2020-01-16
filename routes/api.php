<?php

use Illuminate\Routing\Router as Route;

/**
 * @var $router Route
 */
$router->group(['namespace' => 'Chat\Controllers', 'prefix' => 'users'], function (Route $router) {

    // create a user api
    $router->post('/', 'UserController@create');
});

/*
 * Catch undefined routes.
 */
$router->any(
    '{any}', function () {
    return '404';
}
)->where('any', '(.*)');

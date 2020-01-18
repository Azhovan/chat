<?php

use Illuminate\Http\Response;
use Illuminate\Routing\Router as Route;


$router->group(['namespace' => 'Chat\Controllers', 'prefix' => 'users'], function (Route $router) {

    // Create a user api
    $router->post('/', 'UserController@create')
        ->name('users.create');

    // Get user api
    // id can be user id in or user uuid
    $router->get('/{user_id}', 'UserController@show')
        ->where('user_id', '[0-9]+')
        ->name('users.get');

    // GET /users
    // GET /users_by_ids
    // Send a Message to an other user
    //$router->post('/message')
});

$router->group(['namespace' => 'Chat\Controllers', 'prefix' => 'conversations'], function (Route $router) {

    // Create a new conversation
    $router->post('/', 'ConversationController@create')
        ->name('conversations.create');

    // Send message to a conversation
    $router->post('/{id}/messages', 'ConversationController@sendMessage')
        ->name('conversations.send.message');

});

/*
 * Catch undefined routes.
 */
$router->any('{any}', function () {
    return Response::create(
        'Your resource does not exist', Response::HTTP_NOT_FOUND
    );
})->where('any', '(.*)');

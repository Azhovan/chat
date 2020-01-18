<?php

use Illuminate\Http\Response;
use Illuminate\Routing\Router as Route;

/*
 *
 * User API Routes
 *
 */
$router->group(['namespace' => 'Chat\Controllers', 'prefix' => 'users'], function (Route $router) {
    // Create user
    $router->post('/', 'UserController@create');

    // Fetch user
    $router->get('/', 'UserController@show');

    // Fetch all user's conversations
    // result is grouped based on conversations
    $router->get('/conversations', 'UserController@conversations');
});

/*
 *
 * Conversation API
 *
 */
$router->group(['namespace' => 'Chat\Controllers', 'prefix' => 'conversations'], function (Route $router) {
    // Create a new conversation
    $router->post('/', 'ConversationController@create');

    // Send message to a conversation
    $router->post('/{conversation_id}/messages', 'ConversationController@sendMessage')
        ->where('user_id', '[0-9]+');

    // Get messages from a specific conversation
   $router->get('{conversation_id}/messages', 'ConversationController@readMessage')
       ->where('user_id', '[0-9]+');
});

/*
 * Catch undefined routes.
 */
$router->any('{any}', function () {
    return Response::create(
        'Your resource does not exist', Response::HTTP_NOT_FOUND
    );
})->where('any', '(.*)');

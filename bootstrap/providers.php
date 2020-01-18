<?php

use Chat\Database\Database;
use Chat\Transformers\ConversationTransformer;
use Chat\Transformers\MessageTransformer;
use Chat\Transformers\UserTransformer;


$providers = [
    'Chat\Transformers\MessageTransformer' => MessageTransformer::class,
    'Chat\Database\Database' => Database::class,
    'Chat\Transformers\UserTransformer' => UserTransformer::class,
    'Chat\Transformers\ConversationTransformer' => ConversationTransformer::class,
];

// Binding services into the container
// all of these services will be auto-injected during the runtime
// as a dependency inside of the controller's constructor
foreach ($providers as $alias => $concrete) {
    $container->instance($alias, new $concrete);
}

// Load database configuration
$database = new Database();
$database->registerConfiguration();

// Loads environment variables from .env
$env = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$env->load();

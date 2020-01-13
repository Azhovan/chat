<?php
require __DIR__ . "/vendor/autoload.php";

use Chat\Database\Database;

// Loads environment variables from .env
$env = \Dotenv\Dotenv::createImmutable(__DIR__);
$env->load();

// Establish database connection

$database = new Database();
$database->establishConnection();


$c = new \Chat\Config\Migrations\CreateUserTable();
$c->up();



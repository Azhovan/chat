<?php

use Chat\Database\Database;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

// Loads environment variables from .env
// by this line all environment variables will be
// available when getenv() triggered
$env = Dotenv::createImmutable(__DIR__ . '/../');
$env->load();

// Load database configuration
$database = new Database();
$database->registerConfiguration();

// make sure all errors are cached
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
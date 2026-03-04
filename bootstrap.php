<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// Composer autoloader
require BASE_PATH . '/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Karachi');

// Error reporting based on environment
if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', BASE_PATH . '/storage/logs/error.log');
}

// Boot the database singleton
App\Core\Database::getInstance();

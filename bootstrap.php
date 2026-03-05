<?php

declare(strict_types=1);

define('BASE_PATH', __DIR__);

// Load .env first so we can use it for APP_URL
require BASE_PATH . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Define APP_URL helper map
$appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/pats/public', '/');
define('APP_URL', $appUrl);
// Timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Karachi');

// Aggressive error logging for debugging 500s
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/error.log');

// Error reporting based on environment
if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Boot the database singleton
App\Core\Database::getInstance();

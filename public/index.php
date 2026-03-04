<?php

declare(strict_types=1);

/**
 * ============================================================
 * PATS Front Controller
 * All HTTP requests are routed through here.
 * ============================================================
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/bootstrap.php';

use App\Core\Request;
use App\Core\Router;
use App\Core\Session;

Session::start();

$request = new Request();
Router::load($request);

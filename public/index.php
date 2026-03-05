<?php

declare(strict_types=1);

/**
 * ============================================================
 * PATS Front Controller
 * All HTTP requests are routed through here.
 * ============================================================
 */

require __DIR__ . '/../bootstrap.php';

use App\Core\Request;
use App\Core\Router;
use App\Core\Session;

Session::start();

$request = new Request();
Router::load($request);

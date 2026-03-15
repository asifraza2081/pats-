<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Middleware\SecurityHeaders;

$middleware = new SecurityHeaders();
$request = Request::create('/', 'GET');

$response = $middleware->handle($request, function ($req) {
    return new \Illuminate\Http\Response('OK');
});

echo "CSP Header Output:\n";
echo $response->headers->get('Content-Security-Policy');
echo "\n";

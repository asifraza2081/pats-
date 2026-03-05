<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

class ErrorsController
{
    public function notFound(): void
    {
        http_response_code(404);
        View::render('errors/404', ['pageTitle' => '404 - Not Found']);
    }

    public function unauthorized(): void
    {
        http_response_code(403);
        View::render('errors/403', ['pageTitle' => '403 - Unauthorized']);
    }
}

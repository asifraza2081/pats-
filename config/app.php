<?php

return [
    'name'     => $_ENV['APP_NAME']     ?? 'PATS',
    'url'      => $_ENV['APP_URL']      ?? 'http://localhost/pats/public',
    'env'      => $_ENV['APP_ENV']      ?? 'local',
    'debug'    => ($_ENV['APP_DEBUG']   ?? 'false') === 'true',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'Asia/Karachi',
];

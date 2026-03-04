<?php

return [
    'driver'      => $_ENV['SMS_DRIVER']    ?? 'http',
    'endpoint'    => $_ENV['SMS_ENDPOINT']  ?? '',
    'api_key'     => $_ENV['SMS_API_KEY']   ?? '',
    'sender_id'   => $_ENV['SMS_SENDER_ID'] ?? 'PATS',

    // Payload template — {phone} and {message} are replaced at send time
    // Adjust keys to match your provider's API schema
    'payload'     => [
        'to'      => '{phone}',
        'message' => '{message}',
        'from'    => '{sender_id}',
    ],

    // Optional custom headers (e.g. ['Authorization' => 'Bearer {api_key}'])
    'headers'     => [
        'Authorization' => 'Bearer {api_key}',
        'Content-Type'  => 'application/json',
    ],
];

<?php

return [
    'host' => env('RABBITMQ_HOST') ? : '',
    'port' => env('RABBITMQ_PORT') ? : '',
    'user' => env('RABBITMQ_USER') ? : '',
    'pass' => env('RABBITMQ_PASSWORD') ? : '',
    'exchange' => env('RABBITMQ_EXCHANGE') ? : '',
    'order_status' => [
        '0' => 'INIT',
        '1' => 'FULL',
        '2' => 'PARTIAL',
        '3' => 'PENDING',
        '4' => 'CANCEL',
    ]
];

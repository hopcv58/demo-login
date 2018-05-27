<?php

return [
    'host' => env('RABBITMQ_HOST') ?: '',
    'port' => env('RABBITMQ_PORT') ?: '',
    'user' => env('RABBITMQ_USER') ?: '',
    'pass' => env('RABBITMQ_PASSWORD') ?: '',
    'exchange' => env('RABBITMQ_EXCHANGE') ?: '',
    'order_status' => [
        '0' => 'INIT',
        '1' => 'FULL',
        '2' => 'PARTIAL',
        '3' => 'PENDING',
        '4' => 'CANCEL',
    ],
    'bitcoin_pubkey' => 'tpubDBWd2P7pnNifiVHMp9LNqzCZP4Mhu1KK5xt1bRbPsS1jfpq2QUNk8h3N8wteY1mXPHaChjwHfaQjp8BLgHRKGURf55s9icvbUh6aNAfEabz'
];

<?php

return [
    'default' => env('GW_AUTH_DEFAULT_GATEWAY', 'default'),

    'gateways' => [
        'default' => [
            'private-request' => [
                'app' => env('GW_AUTH_PRIVATE_APP'),
                'ticket' => env('GW_AUTH_PRIVATE_TICKET'),
                'ignore-check' => env('APP_DEBUG', false),  // 是否忽略校验，缺省是false
            ],
        ],

    ],
];
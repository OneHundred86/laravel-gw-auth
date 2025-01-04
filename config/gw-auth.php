<?php

return [
    'private-request' => [
        'app' => env('GW_AUTH_PRIVATE_APP'),
        'ticket' => env('GW_AUTH_PRIVATE_TICKET'),
        'ignore-check' => env('APP_DEBUG', false),  // 是否忽略校验，缺省是false
    ],

    'permission-codes-header' => env('GW_AUTH_PERMISSION_CODES_HEADER', 'GW-Permission-Codes'),
];
<?php

return [
    'private-requests' => [
        'admin' => [
            'app' => env('GW_AUTH_PRIVATE_APP'),
            'ticket' => env('GW_AUTH_PRIVATE_TICKET'),
        ],
    ],
];
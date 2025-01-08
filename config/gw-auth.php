<?php

return [
    'default' => env('GW_AUTH_DEFAULT_GATEWAY', 'default'),

    'gateways' => [
        'default' => [
            // 网关分配的配置，用于校验来自其他应用的请求是否合法
            'private-request' => [
                'app' => env('GW_AUTH_PRIVATE_APP'),
                'ticket' => env('GW_AUTH_PRIVATE_TICKET'),
                'ignore-check' => env('APP_DEBUG', false),  // 是否忽略校验，缺省是false
            ],

            // 服务发现配置，用于自动获取其他应用服务的private-request配置
            'service-discovery' => [
                'private-request' => [
                    'baseUrl' => env('GW_AUTH_SERVICE_DISCOVERY_BASE_URL'),
                    'app' => env('GW_AUTH_SERVICE_DISCOVERY_APP'),
                    'ticket' => env('GW_AUTH_SERVICE_DISCOVERY_TICKET'),
                ],
                'service-cache-ttl' => env('GW_AUTH_SERVICE_DISCOVERY_CACHE_TTL', 60),
            ],
        ],

    ],
];
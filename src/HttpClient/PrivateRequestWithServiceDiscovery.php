<?php

namespace Oh86\GW\Auth\HttpClient;

use Oh86\GW\Auth\Facades\ServiceDiscovery;

class PrivateRequestWithServiceDiscovery extends PrivateRequest
{
    /**
     * @param string $appTag
     * @param null|string $gw
     * @param  \Illuminate\Http\Client\Factory|null  $factory
     */
    public function __construct(string $appTag, $gw = null, $factory = null)
    {
        $config = ServiceDiscovery::driver($gw)->getCachedServiceConfig($appTag);
        if (!$config) {
            throw new \RuntimeException("{$appTag}服务配置不存在");
        }

        parent::__construct([
            'baseUrl' => $config['baseUrl'],
            'app' => $config['app'],
            'ticket' => $config['ticket'],
        ], $factory);
    }
}
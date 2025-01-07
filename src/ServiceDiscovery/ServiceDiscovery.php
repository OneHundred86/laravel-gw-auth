<?php

namespace Oh86\GW\Auth\ServiceDiscovery;

use Illuminate\Support\Facades\Cache;
use Oh86\GW\Auth\HttpClient\PrivateRequest;
use Oh86\Http\Exceptions\HttpRequestException;

class ServiceDiscovery
{
    private PrivateRequest $http;

    /** @var array{baseUrl: string, app: string, ticket: string} */
    private array $config;
    private string $gwName;

    /**
     * @param array $config
     * @param string $gwName
     */
    public function __construct(array $config, string $gwName)
    {
        $this->config = $config;
        $this->gwName = $gwName;
        $this->http = new PrivateRequest($config);
    }

    public function getHttpClient(): PrivateRequest
    {
        return $this->http;
    }

    /**
     * @param string $appTag
     * @throws \Oh86\Http\Exceptions\HttpRequestException
     * @return null | array{baseUrl:string, app:string, ticket:string}
     */
    public function getServiceConfig(string $appTag): ?array
    {
        $datas = ['appTag' => $appTag];
        $response = $this->http
            // ->withOptions(['debug' => true])
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get('gw/service/config', $datas);

        if ($response->json('code') !== 0) {
            throw new HttpRequestException($response->status(), $response->body(), $this->config['baseUrl'] . '/gw/service/config', $datas);
        }

        return $response->json('data');
    }

    /**
     * @param string $appTag
     * @throws \Oh86\Http\Exceptions\HttpRequestException
     * @throws \Illuminate\Contracts\Cache\LockTimeoutException
     * @return null | array{baseUrl:string, app:string, ticket:string}
     */
    public function getCachedServiceConfig(string $appTag): ?array
    {
        $key = "ServiceDiscovery:{$this->gwName}:" . $appTag;
        $config = Cache::get($key);
        if ($config == 'NULL') {
            return null;
        } elseif ($config) {
            return $config;
        }

        // 加互斥锁
        $config = Cache::lock("mutexGetServiceConfig:{$this->gwName}:$appTag", 60)
            ->block(
                10,
                function () use ($appTag) {
                    return $this->getServiceConfig($appTag);
                }
            );

        if (!$config) {
            Cache::put($key, 'NULL', 60);
            return null;
        } else {
            Cache::put($key, $config, 60 * 5);
            return $config;
        }
    }

    public function clearCachedServiceConfig(string $appTag)
    {
        $key = "ServiceDiscovery:{$this->gwName}:" . $appTag;
        return Cache::forget($key);
    }
}
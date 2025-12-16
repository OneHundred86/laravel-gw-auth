<?php

namespace Oh86\GW\Auth\ServiceDiscovery;

use Illuminate\Support\Facades\Cache;
use Oh86\GW\Auth\HttpClient\PrivateRequest;
use Oh86\Http\Exceptions\HttpRequestException;

class ServiceDiscovery
{
    /** @var array{baseUrl: string, app: string, ticket: string} */
    private array $privateRequestConfig;
    private int $serviceCacheTTL;
    private string $gwName;

    /**
     * @param array{private-request: array, service-cache-ttl:int} $config
     * @param string $gwName
     */
    public function __construct(array $config, string $gwName)
    {
        $this->privateRequestConfig = $config['private-request'];
        $this->serviceCacheTTL = $config['service-cache-ttl'];

        $this->gwName = $gwName;
    }

    public function createHttpClient(): PrivateRequest
    {
        return new PrivateRequest($this->privateRequestConfig);
    }

    /**
     * @param string $appTag
     * @throws \Oh86\Http\Exceptions\HttpRequestException
     * @return null | array{baseUrl:string, app:string, ticket:string}
     */
    public function getServiceConfig(string $appTag): ?array
    {
        $datas = ['appTag' => $appTag];
        $response = $this->createHttpClient()
            // ->withOptions(['debug' => true])
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get('gw/service/config', $datas);

        if ($response->json('code') !== 0) {
            throw new HttpRequestException($response->status(), $response->body(), $this->privateRequestConfig['baseUrl'] . '/gw/service/config', $datas);
        }

        return $response->json('data');
    }

    /**
     * @param string $appTag
     * @throws \Oh86\Http\Exceptions\HttpRequestException
     * @throws \Illuminate\Contracts\Cache\LockTimeoutException
     * @return null | array{baseUrl:string, app:string, ticket:string}
     */
    public function getCachedServiceConfig(string $appTag)
    {
        $key = "ServiceDiscovery:{$this->gwName}:" . $appTag;
        $config = Cache::get($key);
        if ($config == 'NULL') {
            return null;
        } elseif ($config) {
            return $config;
        }

        // 加互斥锁
        return Cache::lock("mutexGetServiceConfig:{$this->gwName}:$appTag", 60)
            ->block(
                10,
                function () use ($appTag, $key) {
                    // 获取锁后，再次检查缓存
                    $config = Cache::get($key);
                    if ($config == 'NULL') {
                        return null;
                    } elseif ($config) {
                        return $config;
                    }

                    $config = $this->getServiceConfig($appTag);

                    if (!$config) {
                        Cache::put($key, 'NULL', $this->serviceCacheTTL);
                    } else {
                        Cache::put($key, $config, $this->serviceCacheTTL);
                    }

                    return $config;
                }
            );
    }

    public function clearCachedServiceConfig(string $appTag)
    {
        $key = "ServiceDiscovery:{$this->gwName}:" . $appTag;
        return Cache::forget($key);
    }
}
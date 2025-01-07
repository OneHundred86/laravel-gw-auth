<?php

namespace Oh86\GW\Auth\Facades;

use Illuminate\Support\Facades\Facade;
use Oh86\GW\Auth\ServiceDiscovery\ServiceDiscoveryManager;

/**
 * @method static \Oh86\GW\Auth\ServiceDiscovery\ServiceDiscovery driver(string $driver = null)
 * @method static null | array{baseUrl:string, app:string, ticket:string} getServiceConfig(string $appTag)
 * @method static null | array{baseUrl:string, app:string, ticket:string} getCachedServiceConfig(string $appTag)
 * @method static bool clearCachedServiceConfig(string $appTag)
 */
class ServiceDiscovery extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ServiceDiscoveryManager::class;
    }
}
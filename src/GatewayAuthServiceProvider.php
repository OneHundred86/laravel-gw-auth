<?php

namespace Oh86\GW\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Oh86\GW\Auth\Guard\RequestGuard;
use Oh86\GW\Auth\ServiceDiscovery\ServiceDiscovery;
use Oh86\GW\Auth\ServiceDiscovery\ServiceDiscoveryManager;

class GatewayAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ServiceDiscoveryManager::class, function ($app) {
            return new ServiceDiscoveryManager($app);
        });

        /** @var ServiceDiscoveryManager $serviceDiscoveryManager */
        $serviceDiscoveryManager = $this->app->make(ServiceDiscoveryManager::class);
        foreach (config('gw-auth.gateways') ?? [] as $gwName => $config) {
            if ($config['service-discovery'] ?? false) {
                $serviceDiscoveryManager->extend($gwName, function () use ($gwName, $config) {
                    return new ServiceDiscovery($config['service-discovery'], $gwName);
                });
            }
        }
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/gw-auth.php' => config_path('gw-auth.php'),
        ]);

        Auth::extend('gw', function ($app, $name, array $config) {
            return new RequestGuard($app->get('request'), $config);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Oh86\GW\Auth\Commands\RefreshServiceDiscoveryCache::class,
            ]);
        }
    }
}
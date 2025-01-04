<?php

namespace Oh86\GW\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Oh86\GW\Auth\Guard\RequestGuard;
use Oh86\GW\Auth\Permission\PermissionCode;

class GatewayAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PermissionCode::class, function ($app) {
            return new PermissionCode();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/gw-auth.php' => config_path('gw-auth.php'),
        ]);

        Auth::extend('gw', function ($app, $name, array $config) {
            return new RequestGuard($app->get('request'), $config);
        });
    }
}
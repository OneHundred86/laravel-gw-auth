<?php

namespace Oh86\GW\Auth\ServiceDiscovery;

use Illuminate\Support\Manager;

class ServiceDiscoveryManager extends Manager
{
    public function getDefaultDriver()
    {
        return $this->config->get('gw-auth.default');
    }
}
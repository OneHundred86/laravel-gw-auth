<?php

namespace Oh86\GW\Auth\Commands;

use Illuminate\Console\Command;
use Oh86\GW\Auth\Facades\ServiceDiscovery;

class RefreshServiceDiscoveryCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gw:refresh-service-discovery-cached {appTag} {gw?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '刷新服务的配置缓存';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $appTag = $this->argument('appTag');
        $gw = $this->argument('gw');
        $serviceDiscovery = ServiceDiscovery::driver($gw);
        $serviceDiscovery->clearCachedServiceConfig($appTag);
        $config = $serviceDiscovery->getCachedServiceConfig($appTag);

        $this->info(json_encode([
            'appTag' => $appTag,
            'gw' => $gw,
            'config' => $config,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        return 0;
    }
}
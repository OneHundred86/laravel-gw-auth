<?php

namespace Oh86\GW\Auth\HttpClient;

use Illuminate\Http\Client\PendingRequest;

class PrivateRequest extends PendingRequest
{
    protected string $app;
    protected string $ticket;

    /**
     * @param array{baseUrl: string, app: string, ticket: string} $config
     * @param  \Illuminate\Http\Client\Factory|null  $factory
     */
    public function __construct(array $config, $factory = null)
    {
        $this->app = $config['app'];
        $this->ticket = $config['ticket'];

        $this->baseUrl = $config['baseUrl'];

        parent::__construct($factory);
    }

    public function genSignature(int $timestamp): string
    {
        return sm3(sprintf("%s%s%s", $this->app, $timestamp, $this->ticket));
    }

    public function send(string $method, string $url, array $options = [])
    {
        $now = time();
        $this->withHeaders([
            'GW-Private-App' => $this->app,
            'GW-Private-Time' => $now,
            'GW-Private-Sign' => $this->genSignature($now),
        ]);

        return parent::send($method, $url, $options);
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getRequestUrl(string $url): string
    {
        return ltrim(rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/'), '/');
    }
}
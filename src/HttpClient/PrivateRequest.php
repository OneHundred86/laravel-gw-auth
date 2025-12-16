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

    /**
     * 替换或者增加请求头
     * @param array $headers
     * @return $this
     */
    public function upsertHeaders(array $headers)
    {
        $this->options['headers'] = array_merge(
            $this->options['headers'] ?? [],
            $headers,
        );
        return $this;
    }

    public function send(string $method, string $url, array $options = [])
    {
        $now = time();
        $this->upsertHeaders([
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

    public function getFullRequestUrl(string $url): string
    {
        return ltrim(rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/'), '/');
    }
}
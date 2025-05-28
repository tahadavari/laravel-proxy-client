<?php
namespace Vendor\ProxyClient;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response as ClientResponse;
use Psr\Http\Message\ResponseInterface;

class ProxyClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('proxy.url');
    }

    public function __call(string $method, array $arguments): ClientResponse
    {
        $url     = $arguments[0] ?? '';
        $options = $arguments[1] ?? [];

        $payload = [
            'url'     => $url,
            'method'  => strtoupper($method),
            'headers' => $options['headers'] ?? [],
            'params'  => $options['query']   ?? [],
            'body'    => $options['body']    ?? null,
        ];

        $resp = Http::withBody(
            json_encode($payload),
            'application/json'
        )->post($this->baseUrl.'/proxy');

        return new ClientResponse($resp->toPsrResponse());
    }
}

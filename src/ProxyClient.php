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

        // ساخت payload برای FastAPI
        $payload = [
            'url'     => $url,
            'method'  => strtoupper($method),
            'headers' => $options['headers'] ?? [],
            'params'  => $options['query']   ?? [],
            'body'    => $options['body']    ?? null,
        ];

        $resp = Http::post($this->baseUrl.'/proxy', $payload);

        $psr = $resp->toPsrResponse();
        return new ClientResponse($psr);
    }
}

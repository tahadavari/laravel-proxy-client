<?php
namespace Vendor\ProxyClient\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Http;
use Vendor\ProxyClient\ProxyClient;
use Vendor\ProxyClient\ProxyServiceProvider;

class ProxyClientTest extends TestCase
{
    /**
     register service
     */
    protected function getPackageProviders($app)
    {
        return [
            ProxyServiceProvider::class,
        ];
    }

    /**
     set config
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set('proxy.url', 'http://127.0.0.1:8000');
    }

    /** @test */
    public function macro_proxy_is_registered()
    {
        $client = Http::proxy();
        $this->assertInstanceOf(ProxyClient::class, $client);
    }

    /** @test */
    public function it_forwards_request_and_returns_response()
    {
        Http::fake([
            'http://127.0.0.1:8000/proxy' => Http::response('{"hello":"world"}', 200, [
                'Content-Type' => 'application/json'
            ]),
        ]);

        $response = Http::proxy()->post('https://api.test/endpoint', [
            'headers' => ['X-Test' => 'test'],
            'query'   => ['foo'    => 'bar'],
            'body'    => 'raw-body',
        ]);

        $this->assertTrue($response->successful());
        $this->assertEquals(['hello' => 'world'], $response->json());

        Http::assertSent(function ($request) {
            $sent = json_decode($request->body(), true);

            return
                $request->url() === 'http://127.0.0.1:8000/proxy' &&
                $sent['url']     === 'https://api.test/endpoint' &&
                $sent['method']  === 'POST' &&
                $sent['headers']['X-Test'] === ['test'] &&
                $sent['params']  === ['foo' => 'bar'] &&
                $sent['body']    === 'raw-body';
        });
    }
}

<?php
namespace Vendor\ProxyClient;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class ProxyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/proxy.php' => config_path('proxy.php'),
        ], 'proxy-config');

        Http::macro('proxy', function () {
            return new ProxyClient();
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/proxy.php',
            'proxy'
        );
    }
}

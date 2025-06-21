<?php

namespace HyssaDev\HibikenAsynqClient\Tests;

// use Ramsey\Uuid\Uuid;
use HyssaDev\HibikenAsynqClient\Client;
use Illuminate\Support\Facades\Redis;
// use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Redis\Connections\PhpRedisConnection;
// use Lunaweb\RedisMock\MockPredisConnection;
use Lunaweb\RedisMock\Providers\RedisMockServiceProvider;

class AsynqClientTest extends \PHPUnit\Framework\TestCase
{
    public $app;

    public function getEnvironmentSetUp($app)
    {

        $app['config']->set('app.debug', true);
        $app['config']->set('database.redis.client', 'mock');


        $app->register(RedisMockServiceProvider::class);

        $this->app = $app;
    }

    public function testRedisConnectionInstance()
    {

        $this->assertInstanceOf(PhpRedisConnection::class, Redis::connection());

    }

    public function testSetAndGet()
    {

        Redis::set('key', 'test');
        $this->assertEquals('test', Redis::get('key'));

    }

    public function testEnqueue()
    {
        /**
        * Set $app as FacadeApplication handler
        */
        Facade::setFacadeApplication($this->app);

        $redis = new Redis();

        /**
        * Set $app as FacadeApplication handler
        */
        Facade::setFacadeApplication($this->app);

        $redis = new Redis($this->app);

        $clinet = new Client($redis);
        $res = $clinet->Enqueue([
            'typename' => 'newtest:user:xxxx',
            'payload' => [
                'test' => 'xxxx',
                'user' => 1111
            ]
        ]);
        $this->assertTrue($res);
    }
}

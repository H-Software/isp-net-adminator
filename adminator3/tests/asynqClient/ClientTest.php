<?php

namespace HyssaDev\HibikenAsynqClient\Tests;

// use Ramsey\Uuid\Uuid;
use HyssaDev\HibikenAsynqClient\Client;
use Illuminate\Support\Facades\Redis;
// use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Lunaweb\RedisMock\MockPredisConnection;
use Lunaweb\RedisMock\Providers\RedisMockServiceProvider;
use Illuminate\Container\Container;
use Lunaweb\RedisMock\MockPredisConnector;

class AsynqClientTest extends \PHPUnit\Framework\TestCase
{
    public $appMock;

    protected function setUp(): void
    {
        Facade::clearResolvedInstances();

        /**
        * Setup a new app instance container
        *
        * @var \Illuminate\Container\Container
        */
        $app = new Container();

        $app->singleton('redis', function () use ($app) {

            $r = new \Illuminate\Redis\RedisManager($app, 'predis', [
                'cluster' => false,
                'default' => [
                    'host'     => '127.0.0.1',
                    'port'     => '116379',
                    'database' => 0,
                    'timeout'  => 2,
                ],
            ]);

            $r->extend('mock', function () { return new MockPredisConnector(); });
            $r->setDriver('mock');

            return $r;
        });

        $this->appMock = $app;

        /**
        * Set $app as FacadeApplication handler
        */
        Facade::setFacadeApplication($this->appMock);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
    }

    public function testRedisConnectionInstance()
    {

        $this->assertInstanceOf('Lunaweb\RedisMock\MockPredisConnection', Redis::connection());

    }

    public function testSetAndGet()
    {

        $faker = \Faker\Factory::create();

        $key = $faker->randomNumber(3);

        Redis::set($key, 'test');
        $this->assertEquals('test', Redis::get($key));

    }

    public function testEnqueue()
    {
        $redis = new Redis();

        $clinet = new Client($redis);

        $faker = \Faker\Factory::create();

        $key = $faker->randomNumber(3);

        $res = $clinet->Enqueue([
            'typename' => 'newtest:user:xxxx',
            'payload' => [
                'test' => 'xxxx',
                'user' => $key
            ]
        ]);
        $this->assertTrue($res);
    }
}

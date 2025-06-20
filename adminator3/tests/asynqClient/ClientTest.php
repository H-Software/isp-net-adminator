<?php

namespace HyssaDev\HibikenAsynqClient\Tests;

use Ramsey\Uuid\Uuid;
use HyssaDev\HibikenAsynqClient\Client;
use Illuminate\Support\Facades\Redis;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;

// TODO: enable this
// class AsynqClientTest extends \PHPUnit\Framework\TestCase
// {
// public function testEnqueue()
// {
//     $app = new Container();
//     $app->singleton('app', 'Illuminate\Container\Container');

//     /**
//     * Set $app as FacadeApplication handler
//     */
//     Facade::setFacadeApplication($app);

//     $redis = new Redis($app, 'phpredis', [
//         'cluster' => false,
//         'default' => [
//             'host'     => '127.0.0.1',
//             'port'     => 6379,
//             'database' => 0,
//         ],
//     ]);

//     $clinet = new Client($redis);
//     $res = $clinet->Enqueue([
//         'typename' => 'newtest:user:xxxx',
//         'payload' => [
//             'test' => 'xxxx',
//             'user' => 1111
//         ]
//     ]);
//     $this->assertTrue($res);
// }
// }

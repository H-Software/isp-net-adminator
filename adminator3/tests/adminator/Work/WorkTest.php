<?php

declare(strict_types=1);

namespace App\Tests;

use App\Core\work;
use HyssaDev\HibikenAsynqClient\Client;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Facade;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Lunaweb\RedisMock\Providers\RedisMockServiceProvider;

final class WorkTest extends AdminatorTestCase
{
    public $app;

    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    public static function setDownAfterClass(): void
    {
        Facade::clearResolvedInstances();
    }

    public function testRedisConnectionInstance()
    {
        $this->assertInstanceOf(PhpRedisConnection::class, Redis::connection());
    }

    public function testTaskEnqueue()
    {
        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $work = new work($container);

        $this->assertInstanceOf(Work::class, $work);

        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {

            $task = $work->taskEnqueue($faker->numberBetween(1, 30));

            $this->assertTrue($task);
        }
    }
}

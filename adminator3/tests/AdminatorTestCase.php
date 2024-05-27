<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use DI\CompiledContainer;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Csrf\Guard;

abstract class AdminatorTestCase extends TestCase
{
    public static $pdoMysql;

    public static $pdoPgsql;

    public static function setUpBeforeClass(): void
    {
        $settings = require __DIR__ . '/../config/settings.php';

        // boot ORM and get DB handler
        require __DIR__ . "/fixtures/bootstrapDatabase.php";
        self::$pdoMysql = $capsule->connection("default")->getPdo();

        self::$pdoPgsql = $capsule->connection("pgsql")->getPdo();

        // override DB connection to sqlite
        $settings['phinx']['environments']['test']['connection'] = self::$pdoMysql;

        // prepare DB structure and data
        $config = new Config($settings['phinx']);
        $manager = new Manager($config, new StringInput(' '), new NullOutput());
        $manager->migrate('test');
        $manager->seed('test');

    }

    public function initDIcontainer(
        bool $sentinelMocked,
        bool $viewEnabled
    ) {
        $enableSession = false;

        // prepare DI
        $builder = new ContainerBuilder();
        $builder->addDefinitions('tests/fixtures/bootstrapContainer.php');
        $container = $builder->build();

        // $rfMock = \Mockery::mock(ResponseFactoryInterface::class);
        // $responseFactory = $rfMock;

        require __DIR__ . '/../tests/fixtures/bootstrapContainerAfter.php';

        if($sentinelMocked) {
            require __DIR__ . '/../tests/fixtures/containers/sentinelMock.php';
        } else {
            require __DIR__ . '/../tests/fixtures/containers/sentinel.php';
        }

        if($viewEnabled === true) {
            require __DIR__ . '/../tests/fixtures/containers/view.php';
            $enableSession = true;
        }

        // if($enableSession === true){
        //     $a = require 'tests/fixtures/containers/session.php';
        //     $container->set(key($a), $a[key($a)]);
        // }

        // Not compiled
        $this->assertNotInstanceOf(CompiledContainer::class, $container);

        $this->assertInstanceOf(ContainerInterface::class, $container);

        $this->assertInstanceOf(LoggerInterface::class, $container->get('logger'));

        // $this->assertIsObject($container->get('smarty'));
        $this->assertInstanceOf(\Smarty::class, $container->get('smarty'));

        $this->assertInstanceOf(\PDO::class, $container->get('connPgsql'));

        $this->assertInstanceOf(Guard::class, $container->get('csrf'));

        $this->assertInstanceOf(\Cartalyst\Sentinel\Sentinel::class, $container->get('sentinel'));

        $this->assertInstanceOf(\Slim\Flash\Messages::class, $container->get('flash'));

        return $container;
    }

    public function initAdminatorMockClass(ContainerInterface $container)
    {
        // mock "underlaying" class for helper functions/logic
        $adminatorMock = \Mockery::mock(
            \App\Core\adminator::class,
            [
                $container->get('connMysql'),
                $container->get('smarty'),
                $container->get('logger'),
                '127.0.0.1', // userIPAddress
                $container->get('pdoMysql'),
                $container->get('settings'),
            ]
        )->makePartial();

        $adminatorMock->userIdentityUsername = 'test@test';
        $adminatorMock->shouldReceive('getUserLevel')->andReturn(1);
        $adminatorMock->shouldReceive('checkLevel')->andReturn(true);
        $adminatorMock->shouldReceive('getServerUri')->andReturn("http://localhost:8080/home");
        $adminatorMock->shouldReceive('getUserToken')->andReturn(false);
        // $adminatorMock->shouldReceive('show_stats_faktury_neuhr')->andReturn([0, 0, 0, 0]);

        return $adminatorMock;
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdoMysql = null;
        self::$pdoPgsql = null;
    }
}

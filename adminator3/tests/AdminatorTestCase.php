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
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Slim\Csrf\Guard;

abstract class AdminatorTestCase extends TestCase
{
    public static $pdoMysql;

    public static function setUpBeforeClass(): void
    {
        $settings = require __DIR__ . '/../config/settings.php';

        // boot ORM and get DB handler
        require __DIR__ . "/fixtures/bootstrapDatabase.php";
        self::$pdoMysql = $capsule->connection("default")->getPdo();

        // override DB connection to sqlite
        $settings['phinx']['environments']['test']['connection'] = self::$pdoMysql;

        // prepare DB structure and data
        $config = new Config($settings['phinx']);
        $manager = new Manager($config, new StringInput(' '), new NullOutput());
        $manager->migrate('test');
        $manager->seed('test');

    }

    public function initDIcontainer()
    {
        // prepare DI
        $builder = new ContainerBuilder();
        $builder->addDefinitions('tests/fixtures/bootstrapContainer.php');
        $container = $builder->build();

        $rfMock = \Mockery::mock(ResponseFactoryInterface::class);
        $responseFactory = $rfMock;

        require_once __DIR__ . '/../tests/fixtures/bootstrapContainerAfter.php';

        // Not compiled
        $this->assertNotInstanceOf(CompiledContainer::class, $container);

        $this->assertInstanceOf(ContainerInterface::class, $container);

        $this->assertInstanceOf(LoggerInterface::class, $container->get('logger'));

        // $this->assertIsObject($container->get('smarty'));
        $this->assertInstanceOf(\Smarty::class, $container->get('smarty'));

        $this->assertInstanceOf(Sentinel::class, $container->get('sentinel'));

        $this->assertInstanceOf(\PDO::class, $container->get('connPgsql'));

        $this->assertInstanceOf(Guard::class, $container->get('csrf'));

        return $container;
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdoMysql = null;
    }
}

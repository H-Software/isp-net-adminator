<?php

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Slim\Csrf\Guard;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Facade;

$container->set(
    'settings',
    function () {
        return require __DIR__ . '/../../config/settings-tests.php';
    }
);

$container->set(
    'logger',
    function ($c) {

        $logger = new Logger('test');
        $testLog = new TestHandler();
        $logger->pushHandler($testLog);

        // $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stder', \Monolog\Logger::DEBUG));
        return $logger;
    }
);

$container->set(
    'connMysql',
    self::$pdoMysql
);

$container->set(
    'pdoMysql',
    self::$pdoMysql
);

$container->set(
    'connPgsql',
    self::$pdoPgsql
);

$container->set(
    'pdoPgsql',
    self::$pdoMysql
);

$container->set(
    'smarty',
    function ($c) {
        $settings = $c->get('settings');

        $smarty = new Smarty();
        $smarty->compile_check = $settings['smarty']['compile_check'];
        $smarty->caching       = $settings['smarty']['caching'];
        //$smarty->debugging = true;

        return $smarty;
    }
);

$container->set(
    'csrf',
    function () {

        $guardMock = \Mockery::mock(Guard::class);

        $guardMock->shouldReceive('getTokenNameKey')->andReturn("nameKey42");
        $guardMock->shouldReceive('getTokenValueKey')->andReturn("Value42");
        $guardMock->shouldReceive('getTokenName')->andReturn("name42");
        $guardMock->shouldReceive('getTokenValue')->andReturn("value42");

        return $guardMock;
    }
);

$container->set(
    'flash',
    function ($container) {
        return new \Slim\Flash\Messages();
    }
);

$container->set(
    'redis',
    function () use ($container): Redis {
        $logger = $container->get('logger');
        $logger->debug('DI\redis: called');

        $settings = $container->get('settings');
        $r_settings = $settings['redis'];

        /**
        * Setup a new app instance container
        *
        * @var Illuminate\Container\Container
        */
        $app = new Container();

        $app->singleton('redis', function () use ($app, $r_settings) {
                return new \Illuminate\Redis\RedisManager($app, $r_settings['driver'], [
                    'cluster' => false,
                    'default' => [
                        'host'     => $r_settings['host'],
                        'port'     => $r_settings['port'],
                        'database' => 0,
                        'timeout'  => 2,
                    ],
                ]);
            });

        /**
        * Set $app as FacadeApplication handler
        */
        Facade::setFacadeApplication($app);

        $redis = new Redis();

        if (!is_object($redis)) {
            $logger->error('DI\redis: return value (redis) is not object');
        }

        $logger->info("DI\\redis: Attempting to connect to Redis (settings: " . $r_settings['host'] . ":" . $r_settings['port'] . ")");

        try {
            Redis::ping();
            $logger->info("DI\\redis: connected! getHost() & getPort(): ". var_export(Redis::getHost() . ":". Redis::getPort(), true));
        } catch (\Exception $ex) {
            $m = $ex->getMessage();
            $logger->error("DI\\redis: Redis error: $m");
            return $redis;
        }

        if (Redis::isConnected()) {
            $logger->info("DI\\redis: Redis server PING -> " . Redis::ping());
        } else {
            $logger->error("DI\\redis: Redis server not connected, can't send PING!");
        }

        return $redis;
    }
);

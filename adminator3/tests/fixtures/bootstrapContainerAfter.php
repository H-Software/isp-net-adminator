<?php

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\DbUnit\DataSet\DataSet;
use Slim\Csrf\Guard;

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
    function ($c) {

        $db = new PDO('sqlite::memory:');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    }
);

$container->set(
    'smarty',
    function ($c) {
        $smarty = new Smarty();
        $smarty->compile_check = true;
        //$smarty->debugging = true;

        return $smarty;
    }
);

$container->set(
    'csrf',
    function () use ($responseFactory) {

        $guardMock = \Mockery::mock('Guard');

        $guardMock->shouldReceive('getTokenNameKey')->andReturn(42);
        $guardMock->shouldReceive('getTokenValueKey')->andReturn(42);

        return $guardMock;
        // return new Guard($responseFactory);

    }
);

$container->set(
    'flash',
    function ($container) {
        return new \Slim\Flash\Messages();
    }
);

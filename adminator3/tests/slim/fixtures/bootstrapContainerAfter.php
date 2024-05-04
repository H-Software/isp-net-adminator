<?php

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\DbUnit\DataSet\DataSet;

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
    function ($c) {

        $db = new PDO('sqlite::memory:');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    }
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

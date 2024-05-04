<?php

use Monolog\Handler\TestHandler;
use Monolog\Logger;

$container->set(
    'logger',
    function ($c) {

        $logger = new Logger('test');
        $testLog = new TestHandler();
        $logger->pushHandler($testLog);

        // $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stder', \Monolog\Logger::DEBUG));

        // $handler = new Monolog\ErrorHandler($logger);
        // $handler->registerExceptionHandler();
        // $handler->registerFatalHandler();

        return $logger;
    }
);

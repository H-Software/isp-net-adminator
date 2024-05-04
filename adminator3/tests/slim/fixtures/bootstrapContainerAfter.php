<?php

$container->set(
    'logger',
    function ($c) {

        $logger = new \Monolog\Logger('test');

        $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stder', \Monolog\Logger::DEBUG));

        // $handler = new Monolog\ErrorHandler($logger);
        // $handler->registerExceptionHandler();
        // $handler->registerFatalHandler();

        return $logger;
    }
);

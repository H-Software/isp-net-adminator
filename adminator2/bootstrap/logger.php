<?php

$logger = new Monolog\Logger($settings['logger']['name']);

$formatter = new Monolog\Formatter\LineFormatter(
                                $settings['logger']['output'], 
                                $settings['logger']['dateFormat']
                            );

$stream = new Monolog\Handler\StreamHandler(
                                $settings['logger']['path'], 
                                $settings['logger']['level']
                            );
$stream->setFormatter($formatter);

$fingersCrossed = new Monolog\Handler\FingersCrossedHandler(
    $stream, 
    $settings['logger']['level']
);
$logger->pushProcessor(new Monolog\Processor\UidProcessor());

$handler = new Monolog\ErrorHandler($logger);
$handler->registerExceptionHandler();
$handler->registerFatalHandler();

$logger->pushHandler($fingersCrossed);
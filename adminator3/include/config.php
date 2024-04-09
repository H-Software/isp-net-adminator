<?php

// autoload
require __DIR__ . '/../vendor/autoload.php';

$loader = new Nette\Loaders\RobotLoader;

$loader->addDirectory(__DIR__ . '/../models');
$loader->addDirectory(__DIR__ . '/../controllers');
// $loader->addDirectory(__DIR__ . '/../smarty');

$loader->setTempDirectory(__DIR__ . '/../temp');
$loader->register();

// debug
// $res = $loader->getIndexedClasses();
// print_r($res);

// logger init
$logger = new \Monolog\Logger('my_logger');
$file_handler = new \Monolog\Handler\StreamHandler('../a3-logs/app.log');
$logger->pushHandler($file_handler);

// DB init

init_mysql("Adminator3");

init_postgres("Adminator3");

// Slim
$slim_config['displayErrorDetails'] = true;
$slim_config['addContentLengthHeader'] = false;

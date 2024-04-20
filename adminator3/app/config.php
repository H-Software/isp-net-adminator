<?php

// autoload
require __DIR__ . '/../vendor/autoload.php';

$loader = new Nette\Loaders\RobotLoader;

// $loader->addDirectory(__DIR__ . '/../models');
// $loader->addDirectory(__DIR__ . '/../controllers');
$loader->addDirectory(__DIR__ . '/../app/Middleware');
$loader->addDirectory(__DIR__ . '/../app/src/dao');
$loader->addDirectory(__DIR__ . '/../app/Auth');
$loader->addDirectory(__DIR__ . '/../app/Core');
$loader->addDirectory(__DIR__ . '/../app/Controllers');
$loader->addDirectory(__DIR__ . '/../app/Models');
$loader->addDirectory(__DIR__ . '/../app/Validation');
// $loader->addDirectory(__DIR__ . '/../smarty');

$loader->setTempDirectory(__DIR__ . '/../temp');
$loader->register();

// debug
// $res = $loader->getIndexedClasses();
// print_r($res);

// logger init
// $logger = new \Monolog\Logger('my_logger');
// $file_handler = new \Monolog\Handler\StreamHandler(__DIR__ . '/../../a3-logs/app.log');
// $logger->pushHandler($file_handler);

// DB init

init_mysql("Adminator3");

init_postgres("Adminator3");

// Slim Config
$slim_config['displayErrorDetails'] = true;
$slim_config['addContentLengthHeader'] = false;

// ORM init
$capsule = new Illuminate\Database\Capsule\Manager;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => getenv("MYSQL_SERVER") ? getenv("MYSQL_SERVER") : "localhost",
    'database' => 'adminator2',
    'username' => getenv("MYSQL_USER") ? getenv("MYSQL_USER") : "root",
    'password' => getenv("MYSQL_PASSWD") ? getenv("MYSQL_PASSWD") : "password",
    'charset' => 'utf8',
    'port' => '3306',
    'collation' => 'utf8_unicode_ci',
    'prefix' => ''
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

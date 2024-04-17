<?php

// autoload
require __DIR__ . '/../vendor/autoload.php';

$loader = new Nette\Loaders\RobotLoader;

$loader->addDirectory(__DIR__ . '/../../adminator3/models/app/core/shared');
// $loader->addDirectory(__DIR__ . '/../app/Core');
// $loader->addDirectory(__DIR__ . '/../app/Controllers');
$loader->setTempDirectory(__DIR__ . '/../temp');
$loader->register();

// DBs inits

$conn_mysql = init_mysql("Adminator2");

$db_mysql_link = $conn_mysql;

init_postgres("Adminator2");

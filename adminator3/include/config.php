<?php

// autoload
require __DIR__ . '/../vendor/autoload.php';

$loader = new Nette\Loaders\RobotLoader;

$loader->addDirectory(__DIR__ . '/../models');
$loader->addDirectory(__DIR__ . '/../smarty');

$loader->setTempDirectory(__DIR__ . '/../temp');
$loader->register();

// debug
// $res = $loader->getIndexedClasses();
// print_r($res);

// DB init

init_mysql("Adminator3");

init_postgres("Adminator3");

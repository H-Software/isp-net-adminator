<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('Europe/Prague');

$loaderOrig = require dirname(__DIR__) . '/vendor/autoload.php';

$loader = new Nette\Loaders\RobotLoader();

// $loader->addDirectory(__DIR__ . '/../app/Auth');
$loader->addDirectory(__DIR__ . '/../app/Core');
// $loader->addDirectory(__DIR__ . '/../app/Controllers');
// // $loader->addDirectory(__DIR__ . '/../app/Handlers');
// $loader->addDirectory(__DIR__ . '/../app/Middleware');
// $loader->addDirectory(__DIR__ . '/../app/Middleware');
// $loader->addDirectory(__DIR__ . '/../app/Models');
// $loader->addDirectory(__DIR__ . '/../app/Validation');
// $loader->addDirectory(__DIR__ . '/../app/View');

$loader->setTempDirectory(__DIR__ . '/../temp');
$loader->register();


define('SLIM_MODE', 'testing');

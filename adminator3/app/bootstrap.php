<?php

use Cartalyst\Sentinel\Native\SentinelBootstrapper;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

$loader = new Nette\Loaders\RobotLoader;

$loader->addDirectory(__DIR__ . '/../app/Auth');
$loader->addDirectory(__DIR__ . '/../app/Core');
$loader->addDirectory(__DIR__ . '/../app/Controllers');
// $loader->addDirectory(__DIR__ . '/../app/Handlers');
$loader->addDirectory(__DIR__ . '/../app/Middleware');
$loader->addDirectory(__DIR__ . '/../app/Middleware');
$loader->addDirectory(__DIR__ . '/../app/Models');
$loader->addDirectory(__DIR__ . '/../app/Validation');
$loader->addDirectory(__DIR__ . '/../app/View');

$loader->setTempDirectory(__DIR__ . '/../temp');
$loader->register();

// debug
// $res = $loader->getIndexedClasses();
// print_r($res);

require __DIR__ ."/../boostrap/database.php";

require __DIR__ ."/../boostrap/containerBuilder.php";

// old style DI stuff
require __DIR__ ."/../boostrap/containerAfter.php";

// session_start must be before sentinel stuff
// and after containerBuilder
require __DIR__ ."/../boostrap/session.php";

// authz
Sentinel::instance(new SentinelBootstrapper((require __DIR__ . '/../config/sentinel.php')));

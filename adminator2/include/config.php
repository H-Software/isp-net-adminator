<?php

// autoload
require __DIR__ . '/../vendor/autoload.php';

$loader = new Nette\Loaders\RobotLoader;

$loader->addDirectory(__DIR__ . '/../../adminator3/app/Core/shared');
$loader->addDirectory(__DIR__ . '/../../adminator3/app/Core/Adminator');
$loader->addDirectory(__DIR__ . '/../../adminator3/app/Models');
// $loader->addDirectory(__DIR__ . '/../app/Controllers');
$loader->setTempDirectory(__DIR__ . '/../temp');
$loader->register();

require __DIR__ . '/../bootstrap/settings.php';

require __DIR__ . '/../bootstrap/logger.php';

// session_start must be before sentinel stuff
// and after containerBuilder
require __DIR__ . '/../bootstrap/session.php';

// DB inits and ORM
require __DIR__ . '/../bootstrap/database.php';

// authz
require __DIR__ . '/../bootstrap/sentinel.php';

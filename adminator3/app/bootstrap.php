<?php

use Cartalyst\Sentinel\Native\SentinelBootstrapper;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Odan\Session\SessionInterface;

require __DIR__ ."/../boostrap/netteLoader.php";

require __DIR__ ."/../boostrap/database.php";

require __DIR__ ."/../boostrap/containerBuilder.php";

// old style DI stuff
require __DIR__ ."/../boostrap/containerAfter.php";

$session = $container->get(SessionInterface::class);
$logger = $container->get('logger');
$feature = $container->get('openfeature');

// session_start must be before sentinel stuff
// and after containerBuilder
require __DIR__ ."/../boostrap/session.php";

require __DIR__ ."/../boostrap/appFactory.php";

require __DIR__ ."/../boostrap/dependencies.php";

//
// authz
//
// must be after session middleware / bootstrap\session
// 
Sentinel::instance(new SentinelBootstrapper((require __DIR__ . '/../config/sentinel.php')));

# instanciate FF
$feature = $container->get('openfeature');

# FPDF
define('FPDF_FONTPATH', "include/font/");

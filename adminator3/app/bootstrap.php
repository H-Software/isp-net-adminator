<?php

use Cartalyst\Sentinel\Native\SentinelBootstrapper;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

require __DIR__ ."/../boostrap/netteLoader.php";

require __DIR__ ."/../boostrap/database.php";

require __DIR__ ."/../boostrap/containerBuilder.php";

// old style DI stuff
require __DIR__ ."/../boostrap/containerAfter.php";

// session_start must be before sentinel stuff
// and after containerBuilder
require __DIR__ ."/../boostrap/session.php";

// authz
Sentinel::instance(new SentinelBootstrapper((require __DIR__ . '/../config/sentinel.php')));

require __DIR__ ."/../boostrap/appFactory.php";

require __DIR__ ."/../boostrap/dependencies.php";

# FPDF
define('FPDF_FONTPATH', "include/font/");

# test
// $feature = $container->get('openfeature');

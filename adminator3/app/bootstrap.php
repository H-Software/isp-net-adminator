<?php

// use Cartalyst\Sentinel\Native\SentinelBootstrapper;
// use Cartalyst\Sentinel\Native\Facades\Sentinel;

require __DIR__ ."/../boostrap/netteLoader.php";

require __DIR__ ."/../boostrap/database.php";

require __DIR__ ."/../boostrap/containerBuilder.php";

// old style DI stuff
require __DIR__ ."/../boostrap/containerAfter.php";

require __DIR__ ."/../boostrap/appFactory.php";

require __DIR__ ."/../boostrap/dependencies.php";

# FPDF
define('FPDF_FONTPATH', "include/font/");

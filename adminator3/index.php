<?php

// init db functions defs
require "include/main.function.shared.php";
// autoload, init DB conns, init Illuminate\Database
require "app/config.php";
// slim config
require "app/settings.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App($config);

require __DIR__ ."/app/bootstrap-doctrine.php";

require "app/dependencies.php";

require "app/routing.php";

// final
$app->run();

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

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use Pimple\Container;
use Pimple\Psr11\Container as Psr11Container;

$container = new Container(['settings' => $config]);


$app = AppFactory::create(null, new Psr11Container($container));

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

require __DIR__ ."/app/bootstrap-doctrine.php";

require "app/dependencies.php";

require "app/routing.php";

$app->setBasePath('/home');

$app->addRoutingMiddleware();

// final
$app->run();

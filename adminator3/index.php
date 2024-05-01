<?php

require __DIR__ . '/vendor/autoload.php';

// init db functions defs
require "include/main.function.shared.php";
// autoload, init DB conns, init Illuminate\Database
require "app/bootstrap.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

use Slim\Factory\AppFactory;

AppFactory::setContainer($container);
$app = AppFactory::create();

$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();
$routeParser = $app->getRouteCollector()->getRouteParser();

// Add Error Handling Middleware
$displayErrorDetails = true;
$logErrors = true;
$logErrorDetails = false;
$app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);

require __DIR__ ."/app/dependencies.php";

require "app/routing.php";

$app->addRoutingMiddleware();

// final
$app->run();

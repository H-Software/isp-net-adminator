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

// use Slim\App;

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use DI\DependencyException;

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/app/container.php');
$container = $builder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

// $app = \DI\Bridge\Slim\Bridge::create($container);

// $app->setBasePath('/');

$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();
$routeParser = $app->getRouteCollector()->getRouteParser();

// Add Error Handling Middleware
$displayErrorDetails = true;
$logErrors = true;
$logErrorDetails = false;
$app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);

require "app/dependencies.php";

require "app/routing.php";

$app->addRoutingMiddleware();

// final
$app->run();

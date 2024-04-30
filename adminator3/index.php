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


use Slim\Factory\AppFactory;
use DI\Container;
use DI\ContainerBuilder;

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/app/container.php');
$container = $builder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

// $app->setBasePath('/');

$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();

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

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
use Slim\Exception\NotFoundException;

// $app = new \Slim\App($config);
// $container = new Container(['settings' => $config]);

$container = new \DI\Container();

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

require __DIR__ ."/app/bootstrap-doctrine.php";

require "app/dependencies.php";

require "app/routing.php";

$app->addRoutingMiddleware();

// final
$app->run();

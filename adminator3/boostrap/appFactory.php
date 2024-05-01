<?php

use Slim\Factory\AppFactory;

AppFactory::setContainer($container);
$app = AppFactory::create();

$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();
$routeParser = $app->getRouteCollector()->getRouteParser();

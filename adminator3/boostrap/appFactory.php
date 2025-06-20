<?php

use Slim\Factory\AppFactory;

class_alias(\Illuminate\Support\Facades\Redis::class, 'Redis');

AppFactory::setContainer($container);
$app = AppFactory::create();

$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();
$routeParser = $app->getRouteCollector()->getRouteParser();

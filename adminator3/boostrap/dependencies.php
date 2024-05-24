<?php

use App\Middleware\SessionMiddleware;
use Slim\Views\TwigMiddleware;
use Slim\Csrf\Guard;

$logger = $container->get('logger');
$feature = $container->get('openfeature');

$container->set(Slim\Interfaces\RouteParserInterface::class, $routeParser);

$logger->debug("bootstrapDependencies: setup DI container for csrf");
$container->set(
    'csrf',
    function () use ($responseFactory) {
        return new Guard($responseFactory);
    }
);

if($feature->getBooleanValue("adminator3SlimMiddlewareCsrf", true)) {
    $logger->debug("bootstrapDependencies: adding middleware: csrf");
    $app->add('csrf');
} else {
    $logger->warning("bootstrapDependencies: csrf middleware disabled over openFeature");
}

// $app->addMiddleware($container->get(TwigMiddleware::class));
$logger->debug("bootstrapDependencies: adding middleware: Twig");
$app->addMiddleware(TwigMiddleware::createFromContainer($app));

$logger->debug("bootstrapDependencies: adding middleware: FlashOldFormDataMiddleware");
$app->addMiddleware($container->get('FlashOldFormDataMiddleware'));

$logger->debug("bootstrapDependencies: adding middleware: SessionMiddleware");
$app->addMiddleware($container->get(SessionMiddleware::class));


$container->set(
    'AuthController',
    function ($container) use ($routeParser) {
        return new \App\Controllers\Auth\AuthController($container, $routeParser);
    }
);

$container->set(
    'HomeController',
    function ($container) {
        return new \App\Controllers\HomeController($container);
    }
);

$container->set(
    'aboutController',
    function ($c) {
        return new \App\Controllers\aboutController($c);
    }
);

$container->set(
    'adminController',
    function ($c) {
        return new \App\Controllers\adminController($c);
    }
);

$container->set(
    'archivZmenController',
    function ($c) {
        return new \App\Controllers\archivZmenController($c);
    }
);

$container->set(
    'othersController',
    function ($c) {
        return new \App\Controllers\othersController($c);
    }
);

$container->set(
    'objektyController',
    function ($c) {
        return new \App\Controllers\objektyController($c);
    }
);

$container->set(
    'partnerController',
    function ($c) {
        return new \App\Controllers\partnerController($c);
    }
);

$container->set(
    'platbyController',
    function ($c) {
        return new \App\Controllers\platbyController($c);
    }
);

$container->set(
    'topologyController',
    function ($container) {
        return new \App\Controllers\topologyController($container);
    }
);

$container->set(
    'vlastniciController',
    function ($c) {
        return new \App\Controllers\vlastniciController($c);
    }
);

$container->set(
    'workController',
    function ($c) {
        return new \App\Controllers\workController($c);
    }
);

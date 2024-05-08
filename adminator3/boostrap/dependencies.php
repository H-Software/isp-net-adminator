<?php

use App\Middleware\SessionMiddleware;
use Slim\Views\TwigMiddleware;
use Slim\Csrf\Guard;

$container->set(Slim\Interfaces\RouteParserInterface::class, $routeParser);

$container->set(
    'csrf',
    function () use ($responseFactory) {
        return new Guard($responseFactory);
    }
);

$app->add('csrf');

$app->addMiddleware($container->get(SessionMiddleware::class));

// $app->addMiddleware($container->get(TwigMiddleware::class));
$app->addMiddleware(TwigMiddleware::createFromContainer($app));

$app->addMiddleware($container->get('FlashOldFormDataMiddleware'));

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

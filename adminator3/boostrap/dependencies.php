<?php

use App\Middleware\SessionMiddleware;
use Slim\Views\TwigMiddleware;
use Slim\Csrf\Guard;
use Slim\Interfaces\RouteParserInterface;

$logger = $container->get('logger');
// $feature = $container->get('openfeature');

$container->set(RouteParserInterface::class, $routeParser);

$container->set(
    'csrf',
    function () use ($responseFactory, $container) {
        $logger = $container->get('logger');
        $logger->debug('DI\csrf: creating Guard instance');
        $guard = new Guard($responseFactory);
        return $guard;
    }
);

$logger->debug("bootstrapDependencies: adding middleware: Twig");
$app->addMiddleware(TwigMiddleware::createFromContainer($app));

$logger->debug("bootstrapDependencies: adding middleware: Guard");
$app->addMiddleware($container->get('GuardMiddleware'));

$logger->debug("bootstrapDependencies: adding middleware: FlashOldFormData");
$app->addMiddleware($container->get('FlashOldFormDataMiddleware'));

$logger->debug("bootstrapDependencies: adding middleware: Session");
$app->addMiddleware($container->get(SessionMiddleware::class));

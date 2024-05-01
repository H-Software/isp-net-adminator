<?php

declare(strict_types=1);


use App\Middleware\SessionMiddleware;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use DI\Bridge\Slim\Bridge;
use Slim\Csrf\Guard;
use Slim\Interfaces\RouteParserInterface;


return [

    // App::class => function (ContainerInterface $container) {
    //     $app = Bridge::create($container);
    //     return $app;
    // },

    SessionInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        $session = new PhpSession((array) $settings['session']);

        return $session;
    },

    SessionMiddleware::class => function (ContainerInterface $container) {
        return new SessionMiddleware($container->get(SessionInterface::class), $container->get('csrf'));
    },
    
    // Guard::class => function (ContainerInterface $container) {
    //     $storage = [];

    //     return new Guard($container->get(ResponseInterface::class), 'csrf', $storage, null, 200, 32, true);
    // },

    // RouteParserInterface::class => function (ContainerInterface $container) {
    //     return $container->get(App::class)->getRouteCollector()->getRouteParser();
    // },

    // ResponseInterface::class => function (ContainerInterface $container) {
    //     return $container->get(App::class)->getResponseFactory();
    // },

    // ResponseFactoryInterface::class => function (ContainerInterface $container) {
    //     return $container->get(Psr17Factory::class);
    // },
    
];

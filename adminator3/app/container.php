<?php

declare(strict_types=1);


use Odan\Session\Middleware\SessionStartMiddleware;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Psr\Container\ContainerInterface;

return [


    SessionInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        $session = new PhpSession((array) $settings['session']);

        return $session;
    },

    SessionStartMiddleware::class => function (ContainerInterface $container) {
        return new SessionStartMiddleware($container->get(SessionInterface::class));
    },

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

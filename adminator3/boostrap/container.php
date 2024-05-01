<?php

declare(strict_types=1);

use Psr\Log\LoggerInterface;
use App\Middleware\SessionMiddleware;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
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

    LoggerInterface::class => function (ContainerInterface $container) {
        return $container->get('logger');
    },

    SessionInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        $logger = $container->get('logger');
        
        $logger->debug("SessionInterface: creating PhpSession");
        // $logger->debug("SessionInterface: PhpSession config dump: " . var_export($settings['session'], true));

        $session = new PhpSession((array) $settings['session']);
        // $logger->debug("SessionInterface: creating PhpSession: result: " . var_export($session, true));

        return $session;
    },

    SessionMiddleware::class => function (ContainerInterface $container) {
        return new SessionMiddleware($container->get(SessionInterface::class), $container->get('csrf'), $container->get('logger'));
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

    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },
    
];

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
use Cartalyst\Sentinel\Sentinel;
use App\Renderer\Renderer;
use Slim\Interfaces\RouteParserInterface;

return [
    LoggerInterface::class => function (ContainerInterface $container) {
        return $container->get('logger');
    },

    SessionInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        $logger = $container->get('logger');

        $logger->debug("DI\SessionInterface: creating PhpSession handler adapter");
        // $logger->debug("SessionInterface: PhpSession config dump: " . var_export($settings['session'], true));

        $session = new PhpSession((array) $settings['session']);
        // $logger->debug("SessionInterface: creating PhpSession: result: " . var_export($session, true));

        return $session;
    },

    SessionMiddleware::class => function (ContainerInterface $container) {
        $logger = $container->get('logger');
        $logger->debug("bootstrapContainer: init container SessionMiddleware");

        return new SessionMiddleware($container->get(SessionInterface::class), $container->get('logger'));
    },

    // Guard::class => function (ContainerInterface $container) {
    //     $storage = [];

    //     return new Guard($container->get(ResponseInterface::class), 'csrf', $storage, null, 200, 32, true);
    // },

    // prepare for auto-wiring for controllers and etc
    RouteParserInterface::class => function (ContainerInterface $container) use ($app) {
        return $app->getRouteCollector()->getRouteParser();
    },

    // ResponseInterface::class => function (ContainerInterface $container) {
    //     return $container->get(App::class)->getResponseFactory();
    // },

    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    Sentinel::class => function (ContainerInterface $container) {
        return $container->get('sentinel');
    },

    // Smarty::class => function (ContainerInterface $container) {
    //     return $container->get('smarty');
    // },

    Renderer::class => function (ContainerInterface $container) {
        $logger = $container->get('logger');
        $logger->debug("DI\Renderer called");

        return new Renderer($container);
    },

];

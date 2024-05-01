<?php

declare(strict_types=1);


use Odan\Session\Middleware\SessionStartMiddleware;
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

    SessionStartMiddleware::class => function (ContainerInterface $container) {
        return new SessionStartMiddleware($container->get(SessionInterface::class));
    },

    Guard::class => function (ContainerInterface $container) {
        $storage = [];

        return new Guard($container->get(ResponseInterface::class), 'csrf', $storage, null, 200, 32, true);
    },

    RouteParserInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector()->getRouteParser();
    },

    ResponseInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    },

    // ResponseFactoryInterface::class => function (ContainerInterface $container) {
    //     return $container->get(Psr17Factory::class);
    // },

    // Twig::class => function (ContainerInterface $container) {
    //     $settings = $container->get('settings');
    //     $twig = Twig::create($settings['twig']['path'], [
    //         'cache' => false,
    //     ]);
    //     // $twig->getEnvironment()->setCharset($settings['twig']['charset']);
    //     // $twig->getEnvironment()->enableStrictVariables();
    //     // $twig->addExtension(new WebpackExtension($settings['webpack']['manifest'], PUBLIC_DIR));
    //     // $twig->addExtension($container->get(TwigPhpExtension::class));
    //     // $twig->addExtension($container->get(CsrfExtension::class));
    //     // $twig->addExtension($container->get(TwigMessagesExtension::class));
    //     // $twig->addExtension($container->get(TwigTranslationExtension::class));
    //     // $twig->getEnvironment()->addGlobal('user', Sentinel::check());
    //     // $twig->getEnvironment()->addGlobal('settings', $settings);
    //     // $twig->getEnvironment()->addGlobal('startTime', $startTime);

    //     return $twig;
    // },
    
    // TwigMiddleware::class => function (ContainerInterface $container) {
    //     return TwigMiddleware::createFromContainer(
    //         $container->get(App::class),
    //         'view',
    //     );
    // },

];

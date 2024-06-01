<?php

use Slim\Views\Twig;
use App\View\CsrfExtension;
use Slim\Views\TwigExtension;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\TwigRuntimeExtension;
use Slim\Views\TwigRuntimeLoader;
use Psr\Http\Message\UriInterface;
use Twig\TwigFilter;

$container->set(
    CsrfExtension::class,
    function ($container) {
        return new CsrfExtension($container);
    }
);

$container->set(
    'view',
    function ($container) {
        $settings = $container->get('settings');
        // $logger = $container->get('logger');
        $routeParser = $container->get(RouteParserInterface::class);
        $ui = $container->get(UriInterface::class);
        // $logger->debug("bootstrap\containerAfer: view: called");

        $view = Twig::create(
            $settings['twig']['path'],
            [
                'cache' => false,
                'debug' => true,
            ]
        );

        $view->getEnvironment()->enableStrictVariables();

        $view->addExtension($container->get(CsrfExtension::class));

        // "simulate" twig-middleware
        $runtimeLoader = new TwigRuntimeLoader($routeParser, $ui);
        $view->addRuntimeLoader($runtimeLoader);

        // TODO: maybe better option without routeParser and Uri
        // $runtimeLoader =$this->getMockBuilder(TwigRuntimeLoader::class)
        //                 ->disableOriginalConstructor()
        //                 ->getMock();

        return $view;
    }
);

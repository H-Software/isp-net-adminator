<?php

use Slim\Views\Twig;
use App\View\CsrfExtension;
use Slim\Views\TwigExtension;

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

        // $logger->debug("bootstrap\containerAfer: view: called");

        $view = Twig::create(
            $settings['twig']['path'],
            [
                'cache' => false,
            ]
        );

        $view->getEnvironment()->enableStrictVariables();

        $view->addExtension($container->get(CsrfExtension::class));

        return $view;
    }
);

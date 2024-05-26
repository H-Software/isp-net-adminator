<?php

use App\Renderer\Renderer;

use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Cartalyst\Sentinel\Sentinel;

return [

    LoggerInterface::class => function (ContainerInterface $container) {
        return $container->get('logger');
    },

    Sentinel::class => function (ContainerInterface $container) {
        return $container->get('sentinel');
    },

    Renderer::class => function (ContainerInterface $container) {
        $logger = $container->get('logger');
        $logger->debug("DI\Renderer called");

        return new Renderer($container);
    },
];

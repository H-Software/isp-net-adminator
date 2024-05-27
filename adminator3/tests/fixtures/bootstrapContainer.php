<?php

use App\Renderer\Renderer;

use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Cartalyst\Sentinel\Sentinel;
use Psr\Http\Message\ResponseFactoryInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

return [

    LoggerInterface::class => function (ContainerInterface $container) {
        return $container->get('logger');
    },

    Sentinel::class => function (ContainerInterface $container) {
        return $container->get('sentinel');
    },

    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    Renderer::class => function (ContainerInterface $container) {
        $logger = $container->get('logger');
        $logger->debug("DI\Renderer called");

        return new Renderer($container);
    },
];

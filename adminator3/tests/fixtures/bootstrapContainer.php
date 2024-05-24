<?php

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
];

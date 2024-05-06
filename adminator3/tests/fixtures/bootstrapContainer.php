<?php

use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

return [

    LoggerInterface::class => function (ContainerInterface $container) {
        return $container->get('logger');
    },
];

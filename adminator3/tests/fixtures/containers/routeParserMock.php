<?php

use Slim\Interfaces\RouteParserInterface;

$container->set(
    RouteParserInterface::class, function() {

        $rp = \Mockery::mock(
            RouteParserInterface::class
        );

        $rp->shouldReceive('urlFor')->andReturn("/xxx");

        return $rp;
    }
);

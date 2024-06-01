<?php

use Slim\Interfaces\RouteParserInterface;
use Psr\Http\Message\UriInterface;

$container->set(
    RouteParserInterface::class, function() {

        $rp = \Mockery::mock(
            RouteParserInterface::class
        );

        $rp->shouldReceive('urlFor')->andReturn("/xxx");

        return $rp;
    }
);

$container->set(
    UriInterface::class, function() {

        $ui = \Mockery::mock(
            UriInterface::class
        );

        return $ui;
    }
);

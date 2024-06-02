<?php

use Slim\Interfaces\RouteParserInterface;

$container->set(
    RouteParserInterface::class, function() {

        $rp = \Mockery::mock(
            RouteParserInterface::class
        );

        $rp->shouldReceive('urlFor')->with('home')->andReturn("/home");
        $rp->shouldReceive('urlFor')->andReturn("/somewhere");

        return $rp;
    }
);

<?php

use Slim\Interfaces\RouteParserInterface;

$container->set(
    RouteParserInterface::class, function() {

        $rp = \Mockery::mock(
            RouteParserInterface::class
        );

        $data = [];
        $queryParams = [];

        $rp->shouldReceive('urlFor')->with('auth.signin', [], [])->andReturn("/auth/signin");
        $rp->shouldReceive('urlFor')->with('home')->andReturn("/home");
        // $rp->shouldReceive('urlFor')->andReturn("/somewhere");

        return $rp;
    }
);

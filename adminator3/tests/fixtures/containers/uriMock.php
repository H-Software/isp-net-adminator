<?php

use Psr\Http\Message\UriInterface;

$container->set(
    UriInterface::class, function() {

        $ui = \Mockery::mock(
            UriInterface::class
        );

        return $ui;
    }
);

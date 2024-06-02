<?php

use Cartalyst\Sentinel\Sentinel;
use Cartalyst\Sentinel\Users\UserInterface;

$container->set('sentinel', function () use ($container) {
    // $logger = $container->get('logger');

    // $logger->debug("bootstrap\containerAfer: sentinel: called");

    $userObj = \Mockery::mock(
        UserInterface::class
    );
    $userObj->email = "admin@test";

    $sentinel = \Mockery::mock(
        Sentinel::class,
    );
    $sentinel->shouldReceive('getUser')->andReturn($userObj);
    $sentinel->shouldReceive('authenticate')->andReturn($userObj);

    return $sentinel;
});

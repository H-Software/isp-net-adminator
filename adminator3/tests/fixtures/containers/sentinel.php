<?php

use Cartalyst\Sentinel\Native\SentinelBootstrapper;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

$container->set('sentinel', function () use ($container) {
    // $logger = $container->get('logger');

    // $logger->debug("bootstrap\containerAfer: sentinel: called");
    
    // $boostrap = new SentinelBootstrapper((require __DIR__ . '/../config/sentinel.php'));
    $boostrap = new SentinelBootstrapper();

    $sentinel = new Sentinel($boostrap);

    return $sentinel->getSentinel();

});

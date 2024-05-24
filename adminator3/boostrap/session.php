<?php

// https://laravel.com/docs/10.x/session#introduction
// https://github.com/rdehnhardt/skeleton/blob/master/config/session.php
// https://stackoverflow.com/a/47055083/19497107

use Odan\Session\SessionInterface;

$session = $container->get(SessionInterface::class);
$logger = $container->get('logger');

if (!$session->isStarted() && !headers_sent()) {
    $logger->debug("Bootstrap\Session: session not started, starting");
    $session->start();
}
if (!$session->has('regen') || $session->get('regen') < time()) {
    $session->regenerateId();
    $session->set('regen', time() + 300);
}

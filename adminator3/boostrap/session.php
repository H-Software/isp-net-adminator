<?php

if (!$session->isStarted() && !headers_sent()) {
    $logger->debug("Bootstrap\Session: session not started!");
    // $logger->debug("Bootstrap\Session: session not started, starting");
    // $session->start();
}
// if (!$session->has('regen') || $session->get('regen') < time()) {
//     $session->regenerateId();
//     $session->set('regen', time() + 300);
// }

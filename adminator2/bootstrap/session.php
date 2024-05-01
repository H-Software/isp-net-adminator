<?php

use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;

$logger->debug("SessionInterface: creating PhpSession");
// $logger->debug("SessionInterface: PhpSession config dump: " . var_export($settings['session'], true));

$session = new PhpSession((array) $settings['session']);
// $logger->debug("SessionInterface: creating PhpSession: result: " . var_export($session, true));

if (!$session->isStarted() && !headers_sent()) {
    $logger->debug("Bootstrap\Session: session not started, starting");
    $session->start();
}
if (!$session->has('regen') || $session->get('regen') < time()) {
    $session->regenerateId();
    $session->set('regen', time() + 300);
}

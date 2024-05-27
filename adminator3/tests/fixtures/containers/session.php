<?php

use Psr\Container\ContainerInterface;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;

return [
    SessionInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        // $logger = $container->get('logger');

        // $logger->debug("DI\SessionInterface: creating PhpSession handler adapter");
        // $logger->debug("SessionInterface: PhpSession config dump: " . var_export($settings['session'], true));

        echo "DI\SessionInterface: creating PhpSession handler adapter";
        
        $session = new PhpSession((array) $settings['session']);
        // $logger->debug("SessionInterface: creating PhpSession: result: " . var_export($session, true));

        return $session;
    },
];
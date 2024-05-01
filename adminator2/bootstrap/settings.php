<?php

$settings = [
    'logger' => [
        'name' => 'slim-app-a2',
        'level' => Monolog\Logger::DEBUG,
        'path' => __DIR__ . '/../log/app.log',
        // the default date format is "Y-m-d\TH:i:sP"
        // https://www.php.net/manual/en/datetime.format.php
        'dateFormat' => 'Y-m-d \TH:i:sv \T\ZP',
        // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
        // we now change the default output format according to our needs.
        'output' => "%datetime% > %level_name% > %message% %context% %extra%\n",
    ],
    'session' => [
        'name' => 'adminator2-slimapp',
        'lifetime' => 7200,
        'path' => null,
        'domain' => null,
        'secure' => false,
        'httponly' => true,
        'cache_limiter' => 'nocache',
    ],

];

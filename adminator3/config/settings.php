<?php

$settings = [
        'logger' => [
            'name' => 'slim-app',
            // 'level' => Monolog\Logger::DEBUG,
            'path' => __DIR__ . '/../../a3-logs/app.log',
        ],
        'twig' => [
            'path' => __DIR__ . '/../resources/views/',
        ],
        'session' => [
            'name' => 'adminator3-slimapp',
            'lifetime' => 7200,
            'path' => null,
            'domain' => null,
            'secure' => false,
            'httponly' => true,
            'cache_limiter' => 'nocache',
        ],
        'db' => [
            'driver'        => 'mysql',
            'use_socket'    => false,
            'host'          => getenv("MYSQL_SERVER") ? getenv("MYSQL_SERVER") : "localhost",
            'port'          => 3306,
            'socket'        => 'localhost;unix_socket=/var/run/mysqld/mysqld.sock',
            'database'      => 'adminator2',
            'username'      => getenv("MYSQL_USER") ? getenv("MYSQL_USER") : "root",
            'password'      => getenv("MYSQL_PASSWD") ? getenv("MYSQL_PASSWD") : "password",
            'prefix'        => '',
            'charset'       => 'utf8mb4',
            'encoding'      => 'utf8mb4',
            'collation'     => 'utf8mb4_unicode_ci',
            'strict'        => true,
            'timezone'      => null,
            'cacheMetadata' => false,
            'log'           => true,
            'attributes'    => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
            ],
        ],
];

return $settings;

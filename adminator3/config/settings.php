<?php

$app_mode = (isset($_ENV['MODE'])) ? $_ENV['MODE'] : "";

$settings = [
        'app' => [
            'mode' => $app_mode,
            'core' => [
                'topology' => [
                    'node' => [
                        'listing_interval' => 2
                    ],
                    'router' => [
                        'listing_interval' => 2
                    ]
                ]
            ]
        ],
        'logger' => [
            'name' => 'slim-app',
            'level' => Monolog\Logger::DEBUG,
            'path' => __DIR__ . '/../logs/app.log',
            // the default date format is "Y-m-d\TH:i:sP"
            // https://www.php.net/manual/en/datetime.format.php
            'dateFormat' => 'Y-m-d \TH:i:sv \T\ZP',
            // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
            // we now change the default output format according to our needs.
            'output' => "%datetime% > %level_name% > %message% %context% %extra%\n",
        ],
        'twig' => [
            'path' => __DIR__ . '/../resources/views/',
            'cache' => false,
            'debug' => true,
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
            'host'          => getenv("MYSQL_SERVER") ? getenv("MYSQL_SERVER") : null,
            'port'          => 3306,
            // 'socket'        => 'localhost;unix_socket=/var/run/mysqld/mysqld.sock',
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
        'db2' => [
            // postgres config
            'driver' => 'pgsql',
            'host' => getenv("POSTGRES_SERVER") ? getenv("POSTGRES_SERVER") : "localhost",
            'database' => 'adminator.new',
            'username' => getenv("POSTGRES_USER") ? getenv("POSTGRES_USER") : "root",
            'password' => getenv("POSTGRES_PASSWD") ? getenv("POSTGRES_PASSWD") : "password",
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ],
        'cache' => [
            'cache.prefix' => 'a3cache::',
            'cache.default' => 'database',
            'cache.stores.database' => [
                'driver' => 'database',
                'connection' => 'default',
                'table' => 'cache',
                // 'lock_connection' => null,
            ],
        ],
        'redis' => [
            'host' => getenv("REDIS_HOST") ? getenv("REDIS_HOST") : "localhost",
            'port' => getenv("REDIS_PORT") ? getenv("REDIS_PORT") : "16379",
            'driver' => getenv("REDIS_DRIVER") ? getenv("REDIS_DRIVER") : "phpredis",
        ],
        'phinx' => [
            'paths' => [
                'migrations' => 'database/migrations',
                'seeds'      => 'database/seeds/prod',
            ],
            'migration_base_class' => 'App\Migration\Migration',
            'environments' => [
                'default_migration_table' => 'phinxlog',
                'default_database' => 'dev',
                'test' => [
                    'adapter' => 'sqlite',
                    // 'connection' => self::$pdoMysql,
                    'table_prefix' => ''
                ]
            ]
        ],
        'phinxDev' => [
            'paths' => [
                'migrations' => [
                    'database/migrations',
                    'database/migrations2'
                ],
                'seeds' => 'database/seeds',
            ],
        ]
];

if ($app_mode == "development"){
    $settings['smarty'] = [
        "compile_check" => Smarty::COMPILECHECK_ON,
        "caching" =>  Smarty::CACHING_OFF,
    ];
}
else{
    $settings['smarty'] = [
        "compile_check" => Smarty::COMPILECHECK_OFF,
        "caching" =>  Smarty::CACHING_OFF,
        // "caching" =>  Smarty::CACHING_LIFETIME_SAVED,
    ];
}

return $settings;

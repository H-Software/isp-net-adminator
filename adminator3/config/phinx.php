<?php

declare(strict_types=1);

// init db functions defs
require "include/main.function.shared.php";
// autoload, init DB conns, init Illuminate\Database
require "app/config.php";

$settings        = include __DIR__ . '/../app/settings.php';

$db = $settings['db'];

return [
    'paths' => [
        'migrations' => 'database/migrations',
        'seeds'      => 'database/seeds',
    ],
    'migration_base_class' => 'App\Migration\Migration',
    // 'templates'            => [
    //     'file' => 'app/Migration/MigrationStub.php.stub',
    // ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'dev',
        'default'                 => [
            'adapter' => $db['driver'],
            'host'    => $db['host'] ?? null,
            'port'    => $db['port'] ?? null,
            'socket'  => $db['socket'] ?? null,
            'name'    => $db['database'],
            'user'    => $db['username'],
            'pass'    => $db['password'],
        ],
        // 'default'                 => [
        //     'adapter' => 'mysql',
        //     'host'    => getenv("MYSQL_SERVER") ? getenv("MYSQL_SERVER") : "localhost",
        //     'port'    => 3306,
        //     'name'    => 'admintor2',
        //     'user'    => getenv("MYSQL_USER") ? getenv("MYSQL_USER") : "root",
        //     'pass'    => getenv("MYSQL_PASSWD") ? getenv("MYSQL_PASSWD") : "password",
        // ],
    ],
    'foreign_keys'             => true,
    'default_migration_prefix' => 'db_schema_',
    'generate_migration_name'  => true,
    'mark_generated_migration' => true,
];

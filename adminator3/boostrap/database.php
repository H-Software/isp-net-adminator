<?php

$capsule = new Illuminate\Database\Capsule\Manager();

$settings = require __DIR__ . '/../config/settings.php';

// Mysql init
//
$db = $settings['db'];

$capsule->addConnection(
    [
        'driver'    => $db['driver'],
        'host'      => $db['host'] ?? "localhost",
        'port'      => $db['port'] ?? '3306',
        'database'  => $db['database'],
        'username'  => $db['username'],
        'password'  => $db['password'],
        'charset'   => $db['charset'],
        'collation' => $db['collation'],
        'prefix'    => $db['prefix'],
    ],
    "default"
);

// PgSQL init
//
$db2 = $settings['db2'];

// https://stackoverflow.com/questions/34649181/multiple-database-connection-using-illuminate-database-eloquent-orm-in-codeignit/34650166#34650166
$capsule->addConnection(
    [
        'driver' => 'pgsql',
        'host' => getenv("POSTGRES_SERVER") ? getenv("POSTGRES_SERVER") : "localhost",
        'database' => 'adminator.new',
        'username' => getenv("POSTGRES_USER") ? getenv("POSTGRES_USER") : "root",
        'password' => getenv("POSTGRES_PASSWD") ? getenv("POSTGRES_PASSWD") : "password",
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ],
    "pgsql"
);

$capsule->setAsGlobal();
$capsule->bootEloquent();

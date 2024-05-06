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
        'driver'    => $db2['driver'],
        'host'      => $db2['host'],
        'database'  => $db2['database'],
        'username'  => $db2['username'],
        'password'  => $db2['password'],
        'charset'   => $db2['charset'],
        'collation' => $db2['collation'],
        'prefix'    => $db2['prefix'],
    ],
    "pgsql"
);

$capsule->setAsGlobal();
$capsule->bootEloquent();

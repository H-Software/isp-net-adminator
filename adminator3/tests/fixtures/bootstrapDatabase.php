<?php

$capsule = new Illuminate\Database\Capsule\Manager();

// Mysql init
//

// https://github.com/illuminate/database/blob/eb8edf206d3a6eea8894bc6e21f53469e27dd5c9/Connectors/SQLiteConnector.php#L24
$capsule->addConnection(
    [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => ''
    ],
    "default"
);

// PgSQL init
//

// https://stackoverflow.com/questions/34649181/multiple-database-connection-using-illuminate-database-eloquent-orm-in-codeignit/34650166#34650166
// $capsule->addConnection(
//     [
//         'driver' => 'pgsql',
//         'host' => getenv("POSTGRES_SERVER") ? getenv("POSTGRES_SERVER") : "localhost",
//         'database' => 'adminator.new',
//         'username' => getenv("POSTGRES_USER") ? getenv("POSTGRES_USER") : "root",
//         'password' => getenv("POSTGRES_PASSWD") ? getenv("POSTGRES_PASSWD") : "password",
//         'charset' => 'utf8',
//         'collation' => 'utf8_unicode_ci',
//         'prefix' => '',
//     ],
//     "pgsql"
// );

$capsule->setAsGlobal();
$capsule->bootEloquent();

<?php

// DBs
$capsule = new Illuminate\Database\Capsule\Manager;

// Mysql init
//
$conn_mysql = init_mysql("Adminator2");

$db_mysql_link = $conn_mysql;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => getenv("MYSQL_SERVER") ? getenv("MYSQL_SERVER") : "localhost",
    'database' => 'adminator2',
    'username' => getenv("MYSQL_USER") ? getenv("MYSQL_USER") : "root",
    'password' => getenv("MYSQL_PASSWD") ? getenv("MYSQL_PASSWD") : "password",
    'charset' => 'utf8',
    'port' => '3306',
    'collation' => 'utf8_unicode_ci',
    'prefix' => ''
], "default");

// PgSQL init
//
$db_ok2 = init_postgres("Adminator2");

// https://stackoverflow.com/questions/34649181/multiple-database-connection-using-illuminate-database-eloquent-orm-in-codeignit/34650166#34650166
$capsule->addConnection([
    'driver' => 'pgsql',
    'host' => getenv("POSTGRES_SERVER") ? getenv("POSTGRES_SERVER") : "localhost",
    'database' => 'adminator.new',
    'username' => getenv("POSTGRES_USER") ? getenv("POSTGRES_USER") : "root",
    'password' => getenv("POSTGRES_PASSWD") ? getenv("POSTGRES_PASSWD") : "password",
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
], "pgsql");

$capsule->setAsGlobal();
$capsule->bootEloquent();
<?php

// autoload
require __DIR__ . '/../vendor/autoload.php';

// DBs inits

$conn_mysql = init_mysql("Adminator2");

$db_mysql_link = $conn_mysql;

init_postgres("Adminator2");

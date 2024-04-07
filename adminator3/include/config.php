<?php

$base_html = "<html>
                <head>
                    <title>Adminator3 není dostupný</title>
                    <meta http-equiv=\"Content-Language\" content=\"cs\" >
                    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">

                    <meta http-equiv=\"Cache-Control\" content=\"must-revalidate, no-cache, post-check=0, pre-check=0\" >
                    <meta http-equiv=\"Pragma\" content=\"public\" >

                    <meta http-equiv=\"Cache-Control\" content=\"no-cache\" >
                    <meta http-equiv=\"Pragma\" content=\"no-cache\" >
                    <meta http-equiv=\"Expires\" content=\"-1\" >
                </head>
                <body>
		<img src=\"img2/logo.png\">";

$hlaska_connect = $base_html."\n<div style=\"color: black; padding-left: 20px;  \">\n";
$hlaska_connect .= "<div style=\"padding-top: 50px; font-size: 18px; \">\n";
$hlaska_connect .= "Omlouváme se, Adminátor3 v tuto chvíli není dostupný! </div>\n";
$hlaska_connect .= "<div style=\"padding-top: 10px; font-size: 12px; \" >\nDetailní informace: Chyba! Nelze se pripojit k Mysql databázi. </div>\n";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$MYSQL_SERVER = getenv("MYSQL_SERVER") ? getenv("MYSQL_SERVER") : "localhost";
$MYSQL_USER = getenv("MYSQL_USER") ? getenv("MYSQL_USER") : "root";
$MYSQL_PASSWD = getenv("MYSQL_PASSWD") ? getenv("MYSQL_PASSWD") : "password";

try {
    $conn_mysql = new mysqli(
        $MYSQL_SERVER,
        $MYSQL_USER,
        $MYSQL_PASSWD,
        "adminator2");
} catch (Exception $e) {
    echo $hlaska_connect;
    echo 'Caught exception: Connect to mysql server failed! Message: ',  $e->getMessage(), "\n";
    echo "<div>Mysql server hostname: " . $MYSQL_SERVER . "</div>\n";
    if ($conn_mysql->connect_error) {
        echo "connection error: " . $conn_mysql->connect_error . "\n";
    }
    echo  "</div></div></body></html>\n";
    die();
}

try {
    $conn_mysql->query("SET NAMES 'utf8';");
} catch (Exception $e) {
    die ($hlaska_connect . 'Caught exception: ' .  $e->getMessage() . "\n" . "</div></div></body></html>\n");
}

try {
    $conn_mysql->query("SET CHARACTER SET 'utf8mb3';");
} catch (Exception $e) {
    die ($hlaska_connect . 'Caught exception: ' .  $e->getMessage() . "\n" . "</div></div></body></html>\n");
}

$hlaska_connect = $base_html."<div style=\"color: black; padding-left: 20px;  \">";
$hlaska_connect .= "<div style=\"padding-top: 50px; font-size: 18px; \">";
$hlaska_connect .= "Omlouváme se, Adminátor3 v tuto chvíli není dostupný! </div>";
$hlaska_connect .= "<div style=\"padding-top: 10px; font-size: 12px; \" >Detailní informace: Chyba! Nelze se pripojit k Postgre databázi. </div>";

global $db_ok2;

$POSTGRES_SERVER = getenv("POSTGRES_SERVER") ? getenv("POSTGRES_SERVER") : "localhost";
$POSTGRES_USER = getenv("POSTGRES_USER") ? getenv("POSTGRES_USER") : "root";
$POSTGRES_PASSWD = getenv("POSTGRES_PASSWD") ? getenv("POSTGRES_PASSWD") : "password";
$POSTGRES_DB = getenv("POSTGRES_DB") ? getenv("POSTGRES_DB") : "password";
$POSTGRES_PORT = "5432";
$POSTGRES_CONNECT_TIMEOUT="5";

$POSTGRES_CN = "host=" . $POSTGRES_SERVER . " ";
$POSTGRES_CN .= "port=" . $POSTGRES_PORT . " ";
$POSTGRES_CN .= "user=" . $POSTGRES_USER . " ";
$POSTGRES_CN .= "password=" . $POSTGRES_PASSWD . " ";
$POSTGRES_CN .= "dbname=" . $POSTGRES_DB . " ";
$POSTGRES_CN .= "connect_timeout=" . $POSTGRES_CONNECT_TIMEOUT . " ";

try {
    $db_ok2=pg_connect($POSTGRES_CN);
} catch (Exception $e) {
    die ($hlaska_connect . 'Caught exception: ' .  $e->getMessage() . "\n" . "</div></div></body></html>\n");
}

if ( !($db_ok2) ){ 
    die ($hlaska_connect.pg_last_error($db_ok2)."</div></div></body></html>");
}

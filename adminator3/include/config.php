<?php

$base_html = "<html>
                <head>
                    <title>Adminator2 není distupný</title>
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

try {
    $conn_mysql = new mysqli(
        "localhost",
        "root",
        "mySecretDBpass",
        "adminator2");
} catch (Exception $e) {
    echo $hlaska_connect;
    echo 'Caught exception: Connect to mysql server failed! Message: ',  $e->getMessage(), "\n";
    if ($conn_mysql->connect_error) {
        echo "connection error: " . $conn_mysql->connect_error . "\n";
    }
    echo  "</div></div></body></html>\n";
    die();
}

mysql_query("SET NAMES 'utf8';");
mysql_query("SET CHARACTER SET utf-8");

global $db_ok2;
		
$db_ok2=pg_connect("host=212.80.82.233 port=5432 user=adminator2 password=..:adminator2mso dbname=simelon.new");

if ( !($db_ok2) ){ print "chyba v pristupu k postgree db"; }

?>

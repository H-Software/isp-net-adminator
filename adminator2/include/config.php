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

$hlaska_connect = $base_html."<div style=\"color: black; padding-left: 20px;  \">";
$hlaska_connect .= "<div style=\"padding-top: 50px; font-size: 18px; \">";
$hlaska_connect .= "Omlouváme se, Adminátor2 v tuto chvíli není dostupný! </div>";
$hlaska_connect .= "<div style=\"padding-top: 10px; font-size: 12px; \" >Detailní informace: Chyba! Nelze se pripojit k Mysql databázi. </div>";

$MC=mysql_connect("212.80.82.233", "adminator2", "rX.wuK.G") or die ($hlaska_connect.mysql_error()."</div></div></body></html>");

$db_mysql_link = $MC;

$hlaska_db = $base_html."<div style=\"color: black; padding-left: 20px;  \">";

$hlaska_db .= "<div style=\"padding-top: 50px; font-size: 18px; \">";
$hlaska_db .= "Omlouváme se, Adminátor2 v tuto chvíli není dostupný! </div>";
$hlaska_db .= "<div style=\"padding-top: 10px; font-size: 12px; \" >";
$hlaska_db .= "Detailní informace: Chyba! Nelze vybrat databázi. ";
$hlaska_db .= "<div style=\"font-size: 10px;\">Chybové hlášení: ";

$MS=mysql_select_db("adminator2", $db_mysql_link) or die ($hlaska_db.mysql_error()."</div></div></body></html>");

$db_mysql_db_sl = $MS;

mysql_query("SET NAMES 'utf8';");
mysql_query("SET CHARACTER SET utf-8");
		
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

$hlaska_connect = $base_html."<div style=\"color: black; padding-left: 20px;  \">";
$hlaska_connect .= "<div style=\"padding-top: 50px; font-size: 18px; \">";
$hlaska_connect .= "Omlouváme se, Adminátor2 v tuto chvíli není dostupný! </div>";
$hlaska_connect .= "<div style=\"padding-top: 10px; font-size: 12px; \" >Detailní informace: Chyba! Nelze se pripojit k Postgre databázi. </div>";

$db_ok2=pg_connect("host=212.80.82.233 port=5432 user=adminator2 password=..:adminator2mso dbname=simelon.new")
	or die ($hlaska_connect.pg_last_error($db_ok2)."</div></div></body></html>");

?>

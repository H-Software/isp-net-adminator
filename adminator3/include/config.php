<?php

$MC=mysql_connect("212.80.82.233", "adminator2", "rX.wuK.G") or die ("Nelze se pripojit k Mysql: ".mysql_error());

$MS=mysql_select_db("adminator2") or die ("Nelze vybrat dababazi ".mysql_error());

mysql_query("SET NAMES 'utf8';");
mysql_query("SET CHARACTER SET utf-8");

global $db_ok2;
		
$db_ok2=pg_connect("host=212.80.82.233 port=5432 user=adminator2 password=..:adminator2mso dbname=simelon.new");

if ( !($db_ok2) ){ print "chyba v pristupu k postgree db"; }

?>

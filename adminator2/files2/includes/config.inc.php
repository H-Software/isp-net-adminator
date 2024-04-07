<?php
// database
define("DB_HOSTNAME","b.ns.simelon.net");
define("DB_DATABASE","adminator2");
define("DB_USERNAME","adminator2");
define("DB_PASSWORD","rX.wuK.G");
define("TABLE_PREFIX","afb_");         // prefix of table names in the database
define("BASEURL","/adminator2/files2/");             // path of application directory from the domain name. this is determined automatically when installed.

//db connection
require_once('includes/adodb/adodb.inc.php');
$db = NewADOConnection('mysql');
$db->Connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
?>
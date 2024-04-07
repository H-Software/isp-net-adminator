<?php
// database
define("DB_HOSTNAME","[db_hostname]");
define("DB_DATABASE","[db_database]");
define("DB_USERNAME","[db_username]");
define("DB_PASSWORD","[db_password]");
define("TABLE_PREFIX","[prefix]");         // prefix of table names in the database
define("BASEURL","[baseurl]");             // path of application directory from the domain name. this is determined automatically when installed.

//db connection
require_once('includes/adodb/adodb.inc.php');
$db = NewADOConnection('mysql');
$db->Connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
?>
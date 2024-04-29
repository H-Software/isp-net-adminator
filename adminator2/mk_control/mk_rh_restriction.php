<?php

require_once(__DIR__ . "/../include/main.function.shared.php");
require_once(__DIR__ . "/../include/config.php");
require_once(__DIR__ . "/../mk_control/ros_api_restriction.php");

error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);

echo "mk_rh_restriction.php started <br>\n";

use RouterOS\Config;
use RouterOS\Client;
use RouterOS\Query;

//
//input variables
//

$debug = 0;

//login vars
$login_user = "admin";
$login_pass = "";

//api vars

//ip adress device
if( isset($_GET["ip"]) )
{ $ip = $_GET["ip"]; }
elseif( isset($_SERVER['argv']['1']) )
{ $ip = $_SERVER['argv']['1']; }
else
{
  echo "ERROR: Missing IP Adress. Aborting... \n";
  exit;
}

if(!( preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" . 
		    "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $ip)) )
{
  echo "ERROR: IP address is not valid. Aborting... \n";
  exit;
}

$mk=new mk_net_n_sikana;
$mk->conn_mysql = $conn_mysql;

$mk->zamek_status(); //pokud ON, tak exit :)

$mk->zamek_lock();

// $conn = RouterOS::connect($ip, $login_user, $login_pass) or die("couldn't connect to router\n");

$rosConfig = new Config([
  'host' => $ip,
  'user' => $login_user,
  'pass' => $login_pass,
  'port' => 18728,
]);

try {
  $rosClient = new Client($rosConfig);
  echo "mk_rh_restriction.php: Connection to router was established.<br>\n";
} catch (Exception $exception) {
  die("mk_rh_restriction.php: Error! Couldn't connect to router!\n" . $exception->getMessage() . "<br>\n");
}

$resourceQuery = (new Query('/system/resource/print'));

$response = $rosClient->query($resourceQuery)->read();
echo "mk_rh_restriction.php: INFO: version of RouterOS: " . var_export($response[0]['version'], true) . "<br>\n";

$mk->debug = $debug;
$mk->conn = $rosClient;

$rs = $mk->find_obj($ip); 

if ($rs === false){
  echo "mk_rh_restriction.php: ERROR: find_obj failed!<br>\n";
  $mk->zamek_unlock();
  exit;
}

$mk->detect_diff_and_repaid("net-n"); 

$mk->detect_diff_and_repaid("sikana"); 

$mk->zamek_unlock();

echo "mk_rh_restriction.php finish \n";

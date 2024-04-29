<?php

require_once(__DIR__ . "/../include/main.function.shared.php");
require_once(__DIR__ . "/../include/config.php");
require_once(__DIR__ . "/../mk_control/ros_api_qos.php");

error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);

echo "mk_qos_handler.php started <br>\n";

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
$element_name_dwn = "dst-address";
$element_name_upl = "src-address";
$item_ip_dwn = "dst-address";
$item_ip_upl = "src-address";

$chain = "forward";

$sc_speed_koef = 1.1;

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

// $conn = RouterOS::connect($ip, $login_user, $login_pass) or die("couldn't connect to router\n");

$rosConfig = new Config([
  'host' => $ip,
  'user' => $login_user,
  'pass' => $login_pass,
  'port' => 18728,
]);

try {
  $rosClient = new Client($rosConfig);
  echo "mk_qos_handler.php: Connection to router was established.<br>\n";
} catch (Exception $exception) {
  die("mk_qos_handler.php: Error! Couldn't connect to router!\n" . $exception->getMessage() . "<br>\n");
}

$mk_qos=new mk_synchro_qos($conn_mysql);

$mk_qos->debug = $debug;
$mk_qos->conn = $rosClient;

$rs = $mk_qos->set_wanted_values($ip); //nastaveni IP a ID routeru do globalnich promennych
if( $rs === false ) {
  echo "mk_qos_handler.php: Error! Router not found in database<br>\n";
  exit(2);
}

$mk_qos->element_name_dwn=$element_name_dwn;
$mk_qos->element_name_upl=$element_name_upl;

$mk_qos->item_ip_dwn=$item_ip_dwn;
$mk_qos->item_ip_upl=$item_ip_upl;

$mk_qos->speed_mp_dwn="10240000";
$mk_qos->speed_mp_upl="5120000";

$mk_qos->chain=$chain;

$mk_qos->find_version();

$mk_qos->find_obj($ip);
//$mk_qos->find_obj("10.128.0.3");

$mk_qos->sc_speed_koef=$sc_speed_koef;

 //
 //reseni firewall/mangle
 //

 $mk_qos->detect_diff_in_mangle();
   
 if($mk_qos->force_mangle_rewrite == 1){
  $mk_qos->synchro_mangle_force(); 
 }
 else
 { 
    print "  counts excess ip: ".count($mk_qos->arr_global_diff_exc).", missing ip: ".count($mk_qos->arr_global_diff_mis)."\n";    
    $mk_qos->synchro_mangle(); 
 } 

 //
 // reseni queue(s)
 //

 $mk_qos->qt_global();
  
 $mk_qos->detect_diff_queues();
 //$mk_qos->synchro_qt_force();

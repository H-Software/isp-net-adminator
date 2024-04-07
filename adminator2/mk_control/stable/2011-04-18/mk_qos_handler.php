<?php

//spravne
error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);

//error_reporting(E_ALL | E_STRICT | E_RECOVERABLE_ERROR | E_PARSE ^ E_NOTICE ^ E_WARNING);
//error_reporting(E_ALL ^ (E_NOTICE && E_WARNING));

//error_reporting(E_ALL ^ E_NOTICE | E_WARNING);
//error_reporting(E_ALL ^ E_WARNING);


require_once("/srv/www/htdocs.ssl/adminator2/include/routeros.class.php");

require_once("/srv/www/htdocs.ssl/adminator2/include/config.php");

require_once("/srv/www/htdocs.ssl/adminator2/mk_control/ros_api_simelon.php");

//
//input variables
//

$debug = 0;

//login vars
$login_user = "admin-api";
$login_pass = "Q5I.iPB:sP";

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

$conn = RouterOS::connect($ip, $login_user, $login_pass) or die("couldn't connect to router\n");

$mk_qos=new mk_synchro_qos;

$mk_qos->debug=$debug;
$mk_qos->conn=$conn;

$mk_qos->element_name_dwn=$element_name_dwn;
$mk_qos->element_name_upl=$element_name_upl;

$mk_qos->item_ip_dwn=$item_ip_dwn;
$mk_qos->item_ip_upl=$item_ip_upl;

$mk_qos->chain=$chain;

//$mk_qos->find_obj($ip);
$mk_qos->find_obj("10.128.0.3");

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
   

?>

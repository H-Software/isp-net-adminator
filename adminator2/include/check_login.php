<?php

session_start();

$SN = "autorizace"; 
session_name("$SN"); 

$sid = $_SESSION["db_login_md5"];
$level = $_SESSION["db_level"];
$nick =$_SESSION["db_nick"];

$date = date("U"); 
$ad = date("U") - 1200; 

try {
   $MSQ_S = $conn_mysql->query("SELECT id FROM autorizace WHERE id != '".$conn_mysql->real_escape_string($sid)."' ");
   $MSQ_S_RADKU = $MSQ_S->num_rows;
} catch (Exception $e) {
   die ("<h2 style=\"color: red; \">Login Failed (check login): Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
}

if( $MSQ_S_RADKU == 0 )
{
//jestli je prihlasen pouze jeden clovek tak se neresi cas
 $MSQ = $conn_mysql->query("SELECT id FROM autorizace WHERE (id = '".$conn_mysql->real_escape_string($sid)."') "); 
}
else
{
 $MSQ = $conn_mysql->query("SELECT id FROM autorizace ".
		    "WHERE (id = '".$conn_mysql->real_escape_string($sid)."') AND (date >= ".$conn_mysql->real_escape_string($ad).") "); 
}

 if($MSQ->num_rows <> 1)
 {

    $stranka=$cesta.'nologinpage.php';
    header("Location: ".$stranka);

    echo "Neautorizovaný přístup / Timeout Spojení   ".htmlspecialchars($sid)."  ".htmlspecialchars($level)."";
    exit;

 }

 $MSQ = $conn_mysql->query("UPDATE autorizace ".
		    "SET date = ".$conn_mysql->real_escape_string($date)." WHERE id = '".$conn_mysql->real_escape_string($sid)."' "); 

 // sem asi odstranovani ostatnich useru co jim prosel limit
 $MSQ_D = $conn_mysql->query("DELETE FROM autorizace ".
		     " WHERE ( date <= ".$conn_mysql->real_escape_string($ad).") AND (id != '".$conn_mysql->real_escape_string($sid)."') ");

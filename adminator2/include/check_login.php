<?php

session_start();

$SN = "autorizace"; 
session_name("$SN"); 

$sid = $_SESSION["db_login_md5"];
$level = $_SESSION["db_level"];
$nick =$_SESSION["db_nick"];


$date = date("U"); 
$ad = date("U") - 1200; 

$MSQ_S= mysql_query("SELECT id FROM autorizace WHERE id != '".mysql_real_escape_string($sid)."' ");
$MSQ_S_RADKU= mysql_num_rows($MSQ_S);

if( $MSQ_S_RADKU == 0 )
{
//jestli je prihlasen pouze jeden clovek tak se neresi cas
 $MSQ = mysql_query("SELECT id FROM autorizace WHERE (id = '".mysql_real_escape_string($sid)."') "); 
}
else
{
 $MSQ = mysql_query("SELECT id FROM autorizace ".
		    "WHERE (id = '".mysql_real_escape_string($sid)."') AND (date >= ".mysql_real_escape_string($ad).") "); 
}

 if(mysql_num_rows($MSQ) <> 1)
 {

    $stranka=$cesta.'nologinpage.php';
    header("Location: ".$stranka);

    echo "Neautorizovaný přístup / Timeout Spojení   ".htmlspecialchars($sid)."  ".htmlspecialchars($level)."";
    exit;

 }

 $MSQ = mysql_query("UPDATE autorizace ".
		    "SET date = ".mysql_real_escape_string($date)." WHERE id = '".mysql_real_escape_string($sid)."' "); 

 // sem asi odstranovani ostatnich useru co jim prosel limit
 $MSQ_D= mysql_query("DELETE FROM autorizace ".
		     " WHERE ( date <= ".mysql_real_escape_string($ad).") AND (id != '".mysql_real_escape_string($sid)."') ");

?>

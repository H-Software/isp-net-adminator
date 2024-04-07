<?php

global $cesta;

$cesta = "../";

include ($cesta."include/config.php"); 
include ($cesta."include/check_login.php");

include ($cesta."include/check_level.php");

if ( !( check_level($level,117) ) )
{
// neni level

 $stranka=$cesta.'nolevelpage.php';
 header("Location: ".$stranka);
 
  echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
  exit;
      
}
	

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

include ($cesta."include/charset.php"); 

?>

<title>Adminator 2</title> 

</head> 

<body> 

<? include ($cesta."head.php"); ?> 

<? include ($cesta."category.php"); ?> 

 
 <tr>
 <td colspan="2" bgcolor="silver" height=""><? include("voip-subcat-inc.php"); ?></td>
  </tr>
 
  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->
  
 <?
 
 $id=$_GET["id"];
 
 $cas1 = explode(" ", microtime()); 
 $cas1 = $cas1[1] + $cas1[0]; 
 $rd = "10000"; /* zaokrouhlování */
 
 echo "<div style=\"padding-top: 10px; \">
         <span style=\"font-weight: bold; font-size: 18px; border-bottom: 1px solid grey; \" >Voip systém</span>
	 
	 <span style=\"border-bottom: 1px solid grey; \"> - Telefonní čísla</span>
		 
        </div>";

 echo "<div style=\"font-weight: bold; padding-top: 10px; padding-bottom: 5px; \" >Výpis čísel: </div>";
 			
 echo "<div style=\"padding-left: 5px; padding-bottom: 20px; \">";
  
 system("/var/www/cgi-bin/cgi-adm2/account_list.pl 1 ".$id); 
 
 echo "</div>";
 
 $cas2 = explode(" ", microtime()); 
 $cas3 = (round((($cas2[1] + $cas2[0]) - $cas1) * $rd)) / $rd;
 
 echo "<div >".$cas3."</div>";
 
 ?> 
  <!-- konec vlastniho obsahu -->  
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 


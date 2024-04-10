<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require_once ("include/class.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,80) ) )
{
// neni level

   $stranka='nolevelpage.php';
   header("Location: ".$stranka);
 
   echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
   exit;
}	

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

include ("include/charset.php"); 

?>

<title>Adminator 2</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
 <tr>
 <td colspan="2" height="20" bgcolor="silver" >

    <span style="margin-left: 40px; "><a href="vlastnici-cat.php" class="odkaz-uroven-vys" >| O úrověn výš |</a></span>
   
    <span style="margin-left: 40px; "><a href="vypovedi.php" >Výpis výpovědí </a></span>
    
    <span style="margin-left: 40px; "><a href="vypovedi-vlozeni.php" > Vložení výpovědi </a></span>
	 
    <span style="margin-left: 40px; "><a href="vypovedi-plaintisk.php" > Tisk nevyplněné žádosti </a></span>
    	      
  </td>
  </tr>
 
  <tr>
  <td colspan="2">
  
  <!-- zacatek vlastniho obsahu -->
  
  <br><br>
  <div style="">Tisk prázdné žádosti s hlavičkou společnosti "Simelon, s.r.o. " - <a href="vypovedi-tisk.php?firma=2">zde</a></div>
  <br>
  <div style="">Tisk prázdné žádosti s hlavičkou společnosti "Martin Lopušný " - <a href="vypovedi-tisk.php?firma=1">zde</a></div>
  <br>
  
  <!-- konec vlastniho obsahu -->
 
  </td>
  </tr>
  
 </table>

</body> 
</html> 

 
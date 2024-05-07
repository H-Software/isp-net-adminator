<?php

global $cesta;

$cesta = "../";

require($cesta."include/main.function.shared.php");
require_once ($cesta."include/config.php"); 
require ($cesta."include/check_login.php");
require ($cesta."include/check_level.php");

if( !( check_level($level,75) ) )
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

require ($cesta."include/charset.php"); 

?>

<title>Adminator 2 - partner category</title> 

</head> 

<body> 

<? require ($cesta."head.php"); ?> 

<? require ($cesta."category.php"); ?> 

 
 <tr>
 <td colspan="2" height="20" bgcolor="silver" >

    <span style="margin-left: 40px; "><a href="partner-pozn-update.php" >Připojování nových klientů</a></span> 

    <span style="margin-left: 40px; "><a href="partner-servis-add.php" >Servisní zásahy</a></span> 
 	
 </td>
 </tr>

  
  <tr>
  <td colspan="2" >

    <div style="padding: 20px; font-size: 18px; ">Prosím vyberte kategorii výše</div>  
  
  <!-- konec vnejsi tabulky -->
  </td>
  </tr>
  
 </table>

</body> 
</html> 

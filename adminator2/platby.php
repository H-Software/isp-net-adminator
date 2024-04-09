<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,14) ) )
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

<title>Adminator2 - platby</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
 <tr>
  <td colspan="2" height="20" bgcolor="silver">
    <? include("platby-subcat-inc2.php"); ?>   
  </td>
 </tr>
       
  <tr>
  <td colspan="2">
  
  <?
    // sem zbytek
    
    echo "<br><br>
	    <span style=\"font-size: 18px\"> Prosím vyberte odkaz výše... </span>
	<br><br>";  
    
  ?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 


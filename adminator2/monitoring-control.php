<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,53) ) )
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

<title>Adminator 2 - monitoring</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
 <tr>
  <td colspan="2"><? require("monitoring-cat.php"); ?></td>
 </tr>
      
  
  <tr>
  <td colspan="2">
    <div style="padding: 20px; ">Vyberte kategorii</div>
  </td>
  
  </tr>
  
 </table>

</body> 
</html> 


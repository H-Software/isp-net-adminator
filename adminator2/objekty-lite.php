<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");
// require("include/class.php"); 
// require("include/c_listing-objekty.php");

if ( !( check_level($level,94) ) )
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

<title>Adminator 2 - objekty lite</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 <tr>
   <td colspan="2" height="20" bgcolor="silver" >
    <? include("objekty-subcat-inc.php"); ?>
   </td>
 </tr>
	 
  <tr>
  <td colspan="2">
  
  <br><br>
  <H2>Na stránce se pracuje.
  </H2><br><br>
  <?
  
   
  
  ?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 


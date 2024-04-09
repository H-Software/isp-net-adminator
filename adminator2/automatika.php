<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,31) ) )
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

<title>Adminator 2 - admin - automatika</title>

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

   <tr>
    <td colspan="2" height="20" bgcolor="silver">
     <? include("admin-subcat-inc.php"); ?>
    </td>
   </tr>
  
  <tr>
  <td colspan="2">
  <!-- zde budou vlastni data -->
  
  <table width="100%">
  	
  <tr>
    <td colspan="2">  <div style="color: red; font-size: 20px; ">Automatika</div> </td>
    <td></td>
  </tr>
    
  <tr><td width="20%"><?include ("automatika-cat.php"); ?></td>
  
  <td>Zde je možné upravovat, jestli mají probíhat automatické akce a v jaký čas.</td>
  
  </tr>
  
  </table>

  </td>
  </tr>
  
 </table>

</body> 
</html> 


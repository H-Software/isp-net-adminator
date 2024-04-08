<?php

require("include/main.function.shared.php");
require_once("include/config.php"); 
require_once("include/check_login.php");
require_once("include/check_level.php");

if ( !( check_level($level,92) ) )
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
 <td colspan="2" height="20" bgcolor="silver">
 <? include("platby-subcat-inc.php"); ?>
 </td>
  </tr>
 
  <tr>
  <td colspan="2">
  
  <br><br>
    <H3>Zvolte podkategorii nahoře...
  </H3><br><br>
      
        
  </td>
  </tr>
  
 </table>

</body> 
</html> 


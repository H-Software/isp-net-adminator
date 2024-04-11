<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,28) ) )
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

<? 
$autologout="no";
include ("category.php"); 
?> 

  
  <tr>
  <td colspan="2">
  
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 


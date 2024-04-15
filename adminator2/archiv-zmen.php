<?php

// set_time_limit(0);
require("include/main.function.shared.php");
require_once ("include/config.php"); 
require_once ("include/check_login.php");
require_once ("include/check_level.php");

if ( !( check_level($level,30) ) )
{
 // neni level
 header("Location: nolevelpage.php");
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require ("include/charset.php"); 

?>

<title>Adminator 2 :: Změny :: Archiv změn </title> 

</head> 

<body> 

<?php require ("head.php"); ?> 

<?php require ("category.php"); ?> 

<tr>
  <td colspan="2"><?php require ("archiv-zmen-subcat.php"); ?></td>
</tr>
  
<tr>
  <td colspan="2">
  
 <?php
 

 ?> 
 
  </td>
  </tr>
  
 </table>

</body> 
</html> 


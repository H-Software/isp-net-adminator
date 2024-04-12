<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");
require("include/class.php"); 
require("include/c_listing-objekty.php");

if( !( check_level($level,135) ) )
{

 // neni level
 header("Location: nolevelpage.php");
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
      
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head>';

echo "<script type=\"text/javascript\" src=\"include/js/simelon-global.js\"></script>";

require("include/charset.php"); 

?>

<title>Adminator 2 - objekty stb</title> 

</head> 
<body> 

<?php require ("head.php"); ?> 
<?php require ("category.php"); ?> 
 
 <tr>
  <td colspan="2" height="20" bgcolor="silver" >
   <?php require("objekty-subcat-inc.php"); ?>
   </td>
  </tr>
        
  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->

<?php
//form promenne

?>

 <!-- konec vlastniho obsahu -->
  </td>
  </tr>
  
 </table>

</body>
</html>

<?php

require_once("include/config.php"); 
require_once("include/check_login.php");

require_once("include/check_level.php");

if ( !( check_level($level,143) ) )
{
    // neni level
    header("Location: nolevelpage.php");
 
    echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
    exit;  
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require("include/charset.php"); 

?>

<title>Adminator 2 :: Změny</title> 

</head> 
<body> 

<?php require ("head.php"); ?> 

<?php require ("category.php"); ?> 
 
 <tr>
 <td colspan="2"><?php require("archiv-zmen-subcat.php"); ?></td>
  </tr>
  
  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->
  
    <br><br>
     <H3>Zvolte podkategorii nahoře...
    </H3><br><br>
       
  <!-- konec vlastniho obsahu -->
  </td>
  </tr>
  
 </table>

</body>
</html>

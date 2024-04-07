<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,142) ) )
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

<title>Adminator 2 :: O programu</title> 

</head> 
<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 
 
 <tr>
 <td colspan="2"><? include("about-cat.php"); ?></td>
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

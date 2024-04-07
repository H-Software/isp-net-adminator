<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,94) ) )
{
// neni level

$stranka='nolevelpage.php';
 header("Location: ".$stranka);
 
   echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
      Exit;
      
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


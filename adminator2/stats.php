<?php

require("include/main.function.shared.php");

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,103) ) )
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

<title>Adminator 2 - statistiky</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 
 

<tr>
  <td colspan="2" bgcolor="silver" height=""><? include("stats-cat-inc.php"); ?></td>
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
		       
<?php

set_time_limit(60);

include ("include/config.php");
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,47) ) )
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

<title>Adminator2 - platby</title> 

</head> 

<body>

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

  <tr>
    <td colspan="2" ><? include("platby-subcat-inc2.php"); ?></td>
  </tr>
     
  <tr>
   <td colspan="2">
  
<?
    
  echo "<br><br>";

  global $rok;
  
  $akt_rok = date("Y", time());
  
//  $akt_rok = $akt_rok + 1;
  
  for($rok=2007;$rok <= $akt_rok;$rok++)
  {
    //echo $rok."\n";

    echo "<a href=\"include/export-hot-plateb.php?rok=".$rok."\" >export hotovostních plateb pro platby za rok ".$rok." zde</a>";
    echo "<div style=\"padding-top: 10px; \" ></div>";
  }
  

  echo "<br>";
  
?>
  
  </td>
  </tr>
  
 </table>
 
</body> 
</html> 


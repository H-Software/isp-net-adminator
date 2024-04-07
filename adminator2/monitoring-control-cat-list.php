<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,61) ) )
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

<title>Adminator 2 - monitoring</title> 

</head> 

<body> 

<?php include ("head.php"); ?> 

<?php include ("category.php"); ?> 

 
 <tr>
  <td colspan="2" ><? require("monitoring-cat.php"); ?></td>
 </tr>

 <tr>
  <td colspan="2"> 
  <!-- vlastni obsah -->
  
  <?php
 
   $vysledek=mysql_query("select * from kategorie" );
   $radku=mysql_num_rows($vysledek);

    if ($radku ==0) { echo "<br><br><span style=\"color: red; font-size: 18px; \"> Chyba! Žádné kategorie nenalezeny. </span>"; }
    else
    {

     echo '<br><br>Výpis kategorií: <BR><BR>';
     echo '<table border="1" width="100%" >';

     echo "\n<tr>
         <td><b>id:</b></td>
         <td><b>Název : </b></td>
         <td><b>Šablona : </b></td>";
     echo "</tr>\n";

     echo "\n";

    while ($zaznam=mysql_fetch_array($vysledek) ):

    echo "<tr><td>".$zaznam["id"]."</td>\n";
    echo "<td width=\"30%\" >".$zaznam["jmeno"]."</td>\n";
    
    if ( $zaznam["sablona"] == 2 ){ echo "<td> Ping </td>\n"; }
    elseif ( $zaznam["sablona"] == 4 ){ echo "<td> Routery (Mikrotik) </td>\n"; }
    else { echo "<td>".$zaznam["sablona"]."</td>\n"; }
    
    echo "</tr>";

    endwhile;

    echo '</table>';
    
    
    }
  ?> 
  
  <!-- konec vlastniho obsahu -->
  
   </td>
  </tr>
  
 </table>

</body>
</html>

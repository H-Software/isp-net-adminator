<?php

require("include/main.function.shared.php");
require_once("include/config.php"); 
require_once("include/check_login.php");
require_once("include/check_level.php");
require "./include/c_listing_topology.php";    //předpokládáme třídu uloženou v externím souboru

if( !( check_level($level,5) ) )
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

<title>Adminator2 - Topology</title>

</head>

<body> 

<?php require ("head.php"); ?>

<?php require ("category.php"); ?>

   <tr>
     <td colspan="2" bgcolor="silver" >
     <?php require ("topology-cat2.php"); ?>
     </td>
   </tr>
   
  <tr>
  
    <td colspan="2">
    <!-- zacatek vlastniho obsahu -->
  
  <?php

	 
?>
 
  </td>
  </tr>
  
 </table>

</body> 
</html> 


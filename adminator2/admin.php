<?php

require_once ("include/config.php");
require_once ("include/check_login.php");

require_once ("include/check_level.php");

if( !( check_level($level,17) ) )
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

require ("include/charset.php"); 

?>

<title>Adminator 2</title> 

</head> 

<body> 

<?php require ("head.php"); ?> 

<?php require ("category.php"); ?> 

 <tr>
   <td colspan="2" height="20" bgcolor="silver">
     <?php require("admin-subcat2-inc.php"); ?>
   </td>
 </tr>
	      
  <tr>
  <td colspan="2">
  
  <div style="color: red;font-size: 18px;">Vlastní administrace systému </div><br><br> Vyberte kategorii vlevo</td>
  
  </td>
  </tr>
 </table>

</body> 
</html> 

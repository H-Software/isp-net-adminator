<?php

require("include/main.function.shared.php");
require("include/config.php"); 

require("include/check_login.php");
require("include/check_level.php");

if( !( check_level($level,16) ) )
{ // neni level

 header("Location: nolevelpage.php");
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;      
}
	
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require_once ("include/charset.php"); 

?>

<title>Adminator2 - Work </title> 

</head> 

<body> 


<?php 
 require ("head.php");
 
 require ("category.php"); 

 echo '<tr>
	<td colspan="2">';
?>
	</td>
    </tr>
    
    <tr>
    <td colspan="2">


	<div style="margin-left: 10px; margin-top: 5px;" >
       </div>
		
    </td>
   </tr>
       
  </table>
</body>
</html>

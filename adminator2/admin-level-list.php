<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,21) ) )
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

<title>Adminator 2</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 <tr>
 <td colspan="2" height="50" bgcolor="silver">
      <? include("admin-subcat2-inc.php"); ?>
  </tr>
 
  <tr>
  <td colspan="2">
 
  <?
  	try {
        $vysledek = $conn_mysql->query("select * from leveling order by level asc");
	} catch (Exception $e) {
		die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
	}

    $radku=$vysledek->num_rows;
	
	if ($radku==0) echo "Zadné levely v db (divny) ";
	else
	{
	    echo '<br><br>Výpis levelů stránek: <BR><BR>';
				    
	    echo '<table border="1" width="100%" >';
						    
	    echo "\n<tr>
	    <td width=\"5%\"><b>id:</b></td>
	    <td width=\"30%\"><b>Popis: </b></td>
	    									    
	    <td width=\"20%\"><b>Level: </b></td>
				    
	    <td width=\"10%\"><b>Úprava: </b></td>
	    <td width=\"10%\"><b>Smazání: </b></td>
	    </tr>\n";
							
	    echo "\n";
	
		while ($zaznam=$vysledek->fetch_array()):
			$id=$zaznam["id"];
			
			echo "<tr><td>".$zaznam["id"]."</td>\n";
			echo "<td>".$zaznam["popis"]."</td>\n";
			
			echo "<td>".$zaznam["level"]."</td>\n";
			
			echo '<td><form method="POST" action="admin-level-update.php">
				<input type="hidden" name="update_id" value="'.$id.'">
			<input type="submit" value="update">
			</form></td>';							
			
			echo "</tr>";
	
	  	endwhile;
    }
?>
  
  </td>
 </tr>
  
 </table>

</body> 
</html> 


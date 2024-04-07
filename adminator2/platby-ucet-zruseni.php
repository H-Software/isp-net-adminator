<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,73) ) )
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

<title>Adminator2 - platby</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
 <tr>
 <td colspan="2" height="50">Platby</td>
  </tr>
 
  <tr>
  <td colspan="2">
  
  <?
  echo "<table border=\"0\" width=\"100%\" >";
  
  echo "<tr>";
  
  echo "<td width=\"25%\" valign=\"top\" >";
  
  include("platby-cat.php");
  
  echo "</td>";
  
  echo "<td valign=\"top\" >";
    // sem zbytek
    
    echo "<H3>Zrušení spárování položky s platbou </H3><br><br>";
    
    while ( list($id_polozky, $id_platby) = each($_POST) ):
    
	if ( !( $id_polozky == "odradit" ) )
	{
	    $platby_id = array( "id" => $id_platby );
	
	    $res_platby=pg_delete($db_ok2,'platby', $platby_id);
    
	    if ( $res_platby == 1 )
	    {
	
	    $platby_polozky_id=array ( "id_polozky" => $id_polozky );
	
	    $platby_polozky = array ( "id_platby" => "" );
	
	    $res_polozky =pg_update($db_ok2,'platby_polozky', $platby_polozky,$platby_polozky_id );
	
	    }
	
	    if ($res_platby) { echo "<br><H3><div style=\"color: green; \" > Platba zrušena. </div></H3>\n"; }
    	    else { echo "<div style=\"color: red; \">Chyba! Platbu nelze zrušit. </div><br>\n".pg_last_error($db_ok2); }
	    
	    if ($res_polozky) { echo "<br><H3><div style=\"color: green; \" > Položka platby upravena. </div></H3>\n"; }
	    else { echo "<div style=\"color: red; \">Chyba! Položku platby nelze upravit. </div><br>\n".pg_last_error($db_ok2); }
	    
	}
    endwhile;
    
  echo "</td></tr></table>";
 
 
  ?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 


<?php

require_once ("include/config.php"); 
require_once ("include/check_login.php");

require_once ("include/check_level.php");
require_once ("include/class.php");

if ( !( check_level($level,58) ) )
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

<title>Adminator 2 - monitoring</title> 

</head> 

<body> 

<?php require ("head.php"); ?> 

<?php require ("category.php"); ?> 

 <tr>
   <td colspan="2"><? require("monitoring-cat.php"); ?></td>
 </tr>
  
  <tr>
  <td colspan="2">  
  <!-- zacatek vlastniho obsahu -->
  
  <?php
  
  $odeslano=$_POST["odeslano"];
  $erase_id=intval($_POST["erase-id"]);
  
   if ( ereg("^OK$",$odeslano) )
   {
    // budeme mazat
    // echo "budeme mazat ... ";
    
	if ( $erase_id < 1 )
	{ 
	    echo "Chyba! Nelze identifikovat id grafu! "; 
	    exit; 
	}
	else
	{
	
	    $res=mysql_query("DELETE FROM mon_grafy WHERE id='".intval($erase_id)."' LIMIT 1 "); 
	
	    if ($res) { echo "<br><H3><div style=\"color: green; \" >Graf úspěšně smazán. </div></H3>\n"; }
	    else { echo "<div style=\"color: red; \">Chyba pri mazání grafu. </div><br>\n"; }
	    
	    $pole = "<b>akce: smazani grafu;</b><br> ";
	    $pole .= "[id_grafu] => ".$erase_id;
	
	    if( $res == 1) $res_w = 1; 
	    else $res_w = 0;
	    
	    $pole = mysql_real_escape_string($pole);
	    $nick = mysql_real_escape_string($nick);
	    
	    $add=mysql_query("INSERT INTO archiv_zmen (akce, provedeno_kym, vysledek) VALUES ('$pole','$nick', '$res_w')");
	
	    Aglobal::work_handler("18"); //monitoring - Monitoring II - Feeder-restart
	    Aglobal::work_handler("22"); //monitoring - Monitoring II - checker-restart      		     
	
	} // konec else erase_id < 1
    
    } // konec if ereg ...
    else
    {
    // zobrazime udaje
    
	$dotaz=mysql_query("SELECT * FROM mon_grafy WHERE id='".intval($erase_id)."' ");
	$dotaz_radku=mysql_num_rows($dotaz);
    
	if ( $dotaz_radku <> 1 )
	{ 
	    echo "Chyba! Nelze zjistit data z databáze! "; 
	    exit; 
	}
	else
	{
	
	echo "<form action=\"\" method=\"POST\" >";
	
	echo "<table border=\"0\" width=\"100%\" >";    
    
	echo "<tr><td colspan=\"2\" ><b>Opravdu chcete smazat tento graf: </b></td></tr>";
	
	echo "<tr><td colspan=\"2\" ><br></td></tr>";
	
	while ( $data=mysql_fetch_array($dotaz) ) 
	{
	
	echo "<tr>"."<td width=\"30%\">id: </td>"."<td>".htmlspecialchars($data["id"])."</td>"."</tr>";
	
	    if ( $data["typ_grafu"] == 2 )
	    { // pingy
	
	    echo "<tr><td>Jméno grafu: </td><td>".htmlspecialchars($data["popis1"])."</td></tr>";
	    echo "<tr><td>ip adresa: </td><td>".htmlspecialchars($data["ip1"])."</td></tr>";
	
	    }
	    elseif ( $data["typ_grafu"] == 3 )
	    { // traffic
	    
	    echo "<tr><td>Jméno grafu: </td><td>".htmlspecialchars($data["popis2"])."</td></tr>";
	    echo "<tr><td>Zdrojová ip adresa: </td><td>".htmlspecialchars($data["ip2"])."</td></tr>";
	    echo "<tr><td>MAC adresa: </td><td>".htmlspecialchars($data["jmeno_ifacu"])."</td></tr>";
	    
	    } 
	    else
	    { 
		echo "Chyba! Nelze zjistit typ grafu! "; 
		exit; 
	    }
	
	    echo "<tr><td colspan=\"2\" ><br></td></tr>";
	    
	    echo "<tr><td colspan=\"1\" align=\"center\"> ";
	    echo "<input type=\"submit\" name=\"odeslano\" value=\"OK\" ></td> ";
	    echo "<td><input type=\"hidden\" name=\"erase-id\" value=\"".intval($erase_id)."\" ><br></td></tr>";
	
	        
	} // konec while
		
	echo "</table>";
    
	echo "</form>";
	
	} // konec else dotay_radku
    
    } // konec else ereg ...  
  
  ?>
  
  <!-- konec vlastniho obsahu-->
  </td>
  </tr>
  
 </table>

</body> 
</html> 


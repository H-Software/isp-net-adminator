<?php

require("include/main.function.shared.php");
require_once ("include/config.php"); 
require_once ("include/check_login.php");
require_once ("include/check_level.php");
require_once("include/class.php");

$level_col = "lvl_objekty_stb_add_portal";

if( !( check_level2($level,$level_col) ) )
{ // neni level
  header("Location: ".$cesta."nolevelpage.php");
  
  echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
  exit;
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head>';

echo "<script type=\"text/javascript\" src=\"include/js/adminator-global.js\"></script>";

require("include/charset.php"); 

?>

<title>Adminator 2 - login do IPTV portálu</title> 

</head> 
<body> 

<?php require ("head.php"); ?> 
<?php require ("category.php"); ?> 
 
 <tr>
  <td colspan="2" height="20" bgcolor="silver" >
   <?php require("objekty-subcat-inc.php"); ?>
   </td>
  </tr>
        
  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->

<?php

    echo "<div style=\"padding-left: 20px; padding-top: 10px; font-weight: bold; font-size: 18px;\" >".
	 "Přidání Set-top-boxu do IPTV portálu</div>\n";
    
    echo "<div style=\"padding-left: 5px;\" >\n";
    
    if($_GET["id_stb"] > 0){
    
	$id_stb = intval($_GET["id_stb"]);
	
	$mq_stb = $conn_mysql->query("SELECT mac_adresa, popis FROM objekty_stb WHERE id_stb = '$id_stb' ");
	
	$mq_stb->data_seek(0);

	/* Fetch single row */
	$mq_stb_r = $mq_stb->fetch_row();

	$stb_mac_adresa = $mq_stb_r[0];
	$stb_popis = "Simelon - ".$mq_stb_r[1];
	
	//debug
	//echo "mac: ".$stb_mac_adresa.", popis: ".$stb_popis."<br>\n";

    	echo "<form method=\"post\" action=\"http://app01.cho01.iptv.local:9080/admin/admin/provisioning/stb-edit.html\" name=\"iptvportal\" >";
    	    
    	echo "<input type=\"hidden\" name=\"mac\" value=\"".$stb_mac_adresa."\" >";
    	    
    	echo "<input type=\"hidden\" name=\"note\" value=\"".$stb_popis."\" >";
    	
    	echo "<input type=\"hidden\" name=\"add\" value=\"add\" >";
    	    
	//echo "<a href=\"javascript: document.iptvportal.submit();\" >".
	//	"aktivace funkcí IPTV portálu (přihlašení)</a>";
	    
	echo "<script type=\"text/javascript\">document.iptvportal.submit();</script>";
	    
	echo "</form>";
	
    }
    else{
    
	echo "<div style=\"padding-top: 10px; padding-bottom: 10px;\">Vyberte Set-top-box</div>\n";
		    
		$mq_stb = $conn_mysql->query("SELECT mac_adresa, popis, id_stb FROM objekty_stb ORDER BY id_stb");
		
		while($data_stb = $mq_stb->fetch_array() ){
		
		    echo "<div style=\"border-bottom: 1px solid gray; width: 500px; \" >";
		    
			echo "<div style=\"float: left; width: 150px; \">".$data_stb["popis"]."</div>";
			echo "<div style=\"float: left; width: 200px;\">".$data_stb["mac_adresa"]."</div>";
			echo "<div style=\"float: left;\"><a href=\"?id_stb=".intval($data_stb["id_stb"])."\" >přidat do portálu</a></div>";
			
			echo "<div style=\"clear: both;\"></div>";
		    echo "</div>";
		}
		
	echo "</div>\n";
	
    }
    
    echo "</div>\n";
    
?>

 <!-- konec vlastniho obsahu -->
  </td>
  </tr>
  
 </table>

</body>
</html>

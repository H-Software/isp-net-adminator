<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

// require("include/c_listing-objekty.php");

if( !( check_level2($level, "lvl_objekty_stb_erase") ) )
{ 
    // neni level
    header("Location: ".$cesta."nolevelpage.php");
  
    echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
    exit;
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require ("include/charset.php"); 

?>

<title>Adminator 2 - objekty stb - smazání</title> 

</head> 
<body> 

<?php require("head.php"); ?> 
<?php require("category.php"); ?> 
 
 <tr>
  <td colspan="2" height="20" bgcolor="silver" >
   <?php require("objekty-subcat-inc.php"); ?>
   </td>
  </tr>
        
  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->

<?php
 
 $pocet_sloupcu = "8";
 
 echo "<div style=\"padding-top: 15px; padding-bottom: 15px; \" >
	<span style=\" padding-left: 5px; 
	font-size: 16px; font-weight: bold; \" >
	.:: Smazání Set-Top-Boxu ::. </span> 
       </div>";
       
 $id_stb = intval($_GET["id_stb"]);

 $odeslano = $_GET["odeslano"];
 
 if( isset($odeslano) )
 {
    //povrzeno, takze odpárovat

    echo "<div style=\"padding: 10px; \">";

    $rs = mysql_query("DELETE FROM objekty_stb WHERE id_stb = '".intval($id_stb)."' LIMIT 1");
    
    if( $rs === true ){
    
	$vysledek_write=1;
	echo "<div style=\"color: green; font-weight: bold;\">SetTopBox úspešně smazán.</div>";
    }
    else{
	echo "<div style=\"color: red; font-weight: bold;\">SetTopBox nelze smazat, vyskytla se chyba.</div>";
	
	//echo mysql_error();
    }
    
    //ulozit do archivu zmen
    $az_akce = "<b> akce: smazani stb objektu ; </b><br>";
    
    $az_akce .= " <b>[id_stb]</b> => ".$id_stb."";
    
    $rs_az_add = mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
                            "('".$conn_mysql->real_escape_string($az_akce)."',".
                            "'".$conn_mysql->real_escape_string(\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email)."',".
                            "'".intval($vysledek_write)."')");
   
    if( $rs_az_add === true ){
	echo "<div style=\"padding-top: 10px; color: green; font-weight: bold;\">Záznam úspešně vložen do archivu změn.</div>";
    }
    else{
	echo "<div style=\"padding-top: 10px; color: red; font-weight: bold;\">Chyba při vkládání do archivu změn.</div>";
	
	//echo mysql_error();
    }
    
    //autorestarty
    // Aglobal::work_handler("4"); //rh-fiber - radius
    // Aglobal::work_handler("7"); //trinity - sw.h3c.vlan.set.pl update
    // Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
                           
    
    //IPTV portál
    //dodelat min. hlasku...
                           
    echo "</div>";
 }
 else
 { 
    //zobrazit potvrzujici dialog
 
    echo "<br><span style=\"font-size: 20px; \">Opravdu chcete smazat tento stb objekt? </span><br><br>";

    $rs = mysql_query("SELECT popis, ip_adresa FROM objekty_stb WHERE id_stb = '".intval($id_stb)."' ");
    $rs_radku = mysql_num_rows($rs);

    if( $rs_radku == 0 )
    {
	echo "<br>Chyba! Nelze nacist puvodni data o objektu! <br>";
    }
    else
    {
	echo "<form action=\"\" method=\"GET\" >";

	while( $data=mysql_fetch_array($rs) )
	{

	    echo "<b>dns jméno</b>: ".htmlspecialchars($data["popis"])."<br><br>";

	    echo "<b>ip adresa</b>: ".htmlspecialchars($data["ip_adresa"])."<br>";

	} // konec while

	echo "<input type=\"hidden\" name=\"id_stb\" value=\"".intval($id_stb)."\" > ";

	echo "<br><br><input type=\"submit\" value=\"OK\" name=\"odeslano\" >";

	echo "</form>";

    } // konec jestli jestli je radku nula
 
 }
 
?>
 <!-- konec vlastniho obsahu -->
  </td>
  </tr>
  
 </table>

</body>
</html>

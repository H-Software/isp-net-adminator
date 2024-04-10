<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require_once ("include/class.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,99) ) )
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

<title>Adminator 2 - Zákazníci - fakturační skupiny</title> 

</head> 

<body> 

<?php include ("head.php"); ?> 

<?php include ("category.php"); ?> 

 <tr>
  <td colspan="2" height="20" bgcolor="silver" >
   <?php include("vlastnici-cat-inc.php"); ?>
  </td>
 </tr>
  
 <tr>
  <td colspan="2" >
  <!-- zacatek vlastniho obsahu -->
  
  <?php

	try {
		$dotaz = $conn_mysql->query("SELECT * FROM fakturacni_skupiny order by nazev DESC ");
		$dotaz_radku = $dotaz->num_rows;
	} catch (Exception $e) {
		die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
	}

  $styl_v_okraje = "border-right: dashed 1px gray; ";
  
  echo '<table border="0" width="" >
	
	<tr>
	  <td colspan="9" ><br></td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  <td colspan="2" >&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>  
	</tr>
  
	<tr>
	  <td colspan="2" class="fakturacni-skupiny" > ';
	  
   echo "<div style=\"float: left; font-weight: bold; font-size: 18px; \">".
	    "Výpis fakturačních skupin".
	    " <span style=\"color: grey;\" >(".intval($dotaz_radku).")</span> ".
	    "</div>";
   
	   
   echo '<div style="float: right; " ><a href="vlastnici2-fs-update.php" >Přidání</a></div>
	   
	   </td>
	  <td><br></td>
	  <td><br></td>
	  <td><span style="" >&nbsp;</span></td>
	  <td>&nbsp;</td>
	  <td colspan="3" width="180px" align="center" >
	    <span style="font-weight: bold; " >Aktivované služby</span>
	  </td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  <td colspan="2" >&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	</tr>

	<tr><td colspan="15" ><br></td></tr>	
	';
	
    echo '<tr>
	    <td width="50px" ><b>id</b></td>
	    <td width="70px" ><b>název skupiny</b></td>
	    <td><b>typ</b></td>
	    <td width="70px" align=\"center\" ><b>typ služby</b></td>

	    <td><span style="'.$styl_v_okraje.' " >&nbsp;</span></td>
	    <td>&nbsp;</td>

	    <td colspan="" ><span style="font-weight: bold; " >Internet</span></td>
	    <td><span style="font-weight: bold; width: 150px; " >IPTV</span></td>
	    <td><span style="font-weight: bold; " >VoIP</span></td>
	    <td><span style="'.$styl_v_okraje.' " >&nbsp;</span></td>
	    <td>&nbsp;</td>
	
	    <td><span style="font-weight: bold; " >upravit</td>
	    <td><span style="font-weight: bold; " >smazat</td>
	
	    <td><span style="'.$styl_v_okraje.' " >&nbsp;</span></td>
	    <td>&nbsp;</td>
	    
	  </tr>
	 
	  <tr>
	    <td><span style="color: gray;" >lidí</span></td>
	    <td colspan="3" >Fakturační text</td>
	    
	    <td><span style="'.$styl_v_okraje.' " >&nbsp;</span></td>
	    <td>&nbsp;</td>

	    <td>tarif</td>
	    <td>tarif</td>
	    <td></td>
	
	    <td><span style="'.$styl_v_okraje.' " >&nbsp;</span></td>
	    <td>&nbsp;</td>
	    <td colspan="2" >&nbsp;</td>

	    <td><span style="'.$styl_v_okraje.' " >&nbsp;</span></td>
	    <td>&nbsp;</td>
	    
	 </tr>
	  
	 <tr>
	    <td colspan="4" >&nbsp;</td>
	
	    <td><span style="'.$styl_v_okraje.' " >&nbsp;</span></td>
	    <td>&nbsp;</td>
	    <td colspan="3" >&nbsp;</td>
	    <td><span style="'.$styl_v_okraje.' " >&nbsp;</span></td>
	    <td>&nbsp;</td>
	    <td colspan="2" >&nbsp;</td>

	    <td><span style="'.$styl_v_okraje.' " >&nbsp;</span></td>
	    <td>&nbsp;</td>

	 </tr>
  	 ';
	  
	
    if ( $dotaz_radku == 0 )
    { echo "<tr><td>Žádné údaje v databázi </td></tr>"; }
    else
    {
      while( $data=$dotaz->fetch_array() )
      {
      
		//prvni radek
		echo "<tr>";
    
		echo "<td class=\"fakturacni-skupiny\" >".$data["id"]."</td>";
		echo "<td class=\"fakturacni-skupiny\" >".$data["nazev"]."</td>";
		
		echo "<td width=\"50px\" class=\"fakturacni-skupiny\" >";
	    if ( $data["typ"] == 1 )echo "DÚ";
	    elseif( $data["typ"] == 2 ) echo "FÚ";
	    else echo "N/A";
		echo "</td>";
		
		echo "<td align=\"center\" class=\"fakturacni-skupiny\" ";
			if ( $data["typ_sluzby"] == 0 )echo " bgcolor=\"#99FF99\" >wifi";
			elseif( $data["typ_sluzby"] == 1 ) echo " bgcolor=\"#fbbc86\" >optika";
			else echo " >N/A";
		echo "</td>";

		echo "<td><span style=\"".$styl_v_okraje."\" >&nbsp;</span></td>
			<td>&nbsp;</td>";

		echo "<td class=\"fakturacni-skupiny\">";
			if ( $data["sluzba_int"] == 1 )
			{ echo "<span style=\"color: green; font-weight: bold; \" >Ano</span>"; }
			elseif( $data["sluzba_int"] == 0 )
			{ echo "<span style=\"color: #CC6666; \" >Ne</span>"; }
			else echo "N/A";
		echo "</td>";

		echo "<td class=\"fakturacni-skupiny\">";
			if( $data["sluzba_iptv"] == 1 )
			{ echo "<span style=\"color: green; font-weight: bold; \" >Ano</span>"; }
			elseif( $data["sluzba_iptv"] == 0 )
			{ echo "<span style=\"color: #CC6666; \" >Ne</span>"; }
			else echo "N/A";
		echo "</td>";

		echo "<td class=\"fakturacni-skupiny\" >";
			if( $data["sluzba_voip"] == 1 )
			{ echo "<span style=\"color: green; font-weight: bold; \" >Ano</span>"; }
			elseif( $data["sluzba_voip"] == 0 )
			{ echo "<span style=\"color: #CC6666; \" >Ne</span>"; }
			else echo "N/A";
		echo "</td>";

		//vert. okraj
        echo "<td><span style=\"".$styl_v_okraje."\" >&nbsp;</span></td>
	    	<td>&nbsp;</td>";

		echo "<td class=\"fakturacni-skupiny\" >
			<a href=\"vlastnici2-fs-update.php?update_id=".$data["id"]."\" >upravit</a>
			</td>";
			
		echo "<td class=\"fakturacni-skupiny\" >
			<a href=\"vlastnici2-fs-erase.php?erase_id=".$data["id"]."\" >smazat</a>
			</td>";

		echo "<td><span style=\"".$styl_v_okraje."\" >&nbsp;</span></td>
			<td>&nbsp;</td>";
		
		echo "</tr>";
    
		//druhej radek
		
		//zjisteni poctu lidi ve skupine
		
		$dotaz_obj_fs = pg_query("SELECT id_cloveka FROM vlastnici WHERE fakturacni_skupina_id = '".$data["id"]."'");
		$dotaz_obj_fs_rows = pg_num_rows($dotaz_obj_fs);
      
		//vypis
		echo "<tr>";
		echo "<td><span style=\"color: grey;\" >".$dotaz_obj_fs_rows."</span></td>";
		
		echo "<td colspan=\"3\" ><span style=\"color: grey; \" >".$data["fakturacni_text"]."</span></td>";
		
		echo "<td><span style=\"".$styl_v_okraje."\" >&nbsp;</span></td>
			<td>&nbsp;</td>";

		echo "<td><span style=\"color: grey; \" >".$data["sluzba_int_id_tarifu"]."</span></td>";
		echo "<td><span style=\"color: grey; \" >".$data["sluzba_iptv_id_tarifu"]."</span></td>";
		
		echo "<td>&nbsp;</td>";
	
		//vert. okraj
        echo "<td><span style=\"".$styl_v_okraje."\" >&nbsp;</span></td>
	    <td>&nbsp;</td>";
	
		echo "<td colspan=\"2\" >&nbsp;</td>";
		
		echo "<td><span style=\"".$styl_v_okraje."\" >&nbsp;</span></td>
			<td>&nbsp;</td>";
			
      	echo "</tr>";
       
     } // konec while

    } // konec else - if dotaz_radku == 0
    	
  echo "</table>";
      
  ?>
    
    <!-- konec vlastniho obsahu -->
  </td>
  </tr>
  
 </table>

</body>
</html>

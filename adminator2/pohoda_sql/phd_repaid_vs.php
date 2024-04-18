<?php

$cesta = "../";

//zde vyresit prepinani roku/db jednotek
if(!isset($_GET["rok"]))
{ 
    // $rok = date("Y"); 
}
else
{ $rok = intval($_GET["rok"]); }

//$mssql_db = "StwPh_26109824_".$rok;

$level_col = "lvl_phd_repaid_vs";

//ini_set("mssql.charset", "UTF-8");
//set_time_limit(10);

require($cesta."include/main.function.shared.php");
require_once($cesta."include/config.php"); 

require_once($cesta."include/config.ms.php"); 

require($cesta."include/check_login.php");

require($cesta."include/check_level.php");

// require("phd_global_function.php");

function select_uj_query() {

      $rs=mssql_query("SELECT name, database_id FROM SYS.DATABASES WHERE name LIKE 'StwPh_26109824%' ");

    return $rs;
}

function select_uj($q) {

    while( $row = mssql_fetch_assoc($q) ){
    
	$db_id = $row["database_id"];
	$db_name = $row["name"];
	
	$ret_a[$db_id] = $db_name;
    }

    if( is_array($ret_a))
	return $ret_a;
    else
	return false;
}

function check_uj($id1, $id2){

    $rs1 = mssql_query("SELECT name FROM SYS.DATABASES WHERE database_id = '".intval($id1)."' ");
    $rs2 = mssql_query("SELECT name FROM SYS.DATABASES WHERE database_id = '".intval($id2)."' ");

    $rn1 = mssql_num_rows($rs1);
    $rn2 = mssql_num_rows($rs2);
    
    if( $rn1 <> 1 or $rn2 <> 1){
	
	echo "<div style=\"padding-5px; color: red; font-weight: bold;\">".
		"Špatný výběr účetní jednotky ...</div>";

	return false;
    }
    else{
    
	
	$row1 = mssql_fetch_row($rs1);
	$row2 = mssql_fetch_row($rs2);
	
	$ret_a[] = $row1[0];
	$ret_a[] = $row2[0];
	
	return $ret_a;
    }
}

if( !( check_level2($level,$level_col) ) )
{ // neni level
  header("Location: ".$cesta."nolevelpage.php");
    
  echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
  exit;
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head>';

require($cesta."include/charset.php"); 

?>

<title>Adminator 2 :: PohodaSQL :: Oprava VS</title> 

</head>

<body>

<?php require ($cesta."head.php"); ?> 

<?php require ($cesta."category.php"); ?> 

 <tr>
   <td colspan="2" height="20" bgcolor="silver" ><? require("../fn-cat.php"); ?> </td>
 </tr>

 <tr>
  <td colspan="2">
  
<?php

  echo "<div style=\"padding-left: 10px; padding-top: 10px; font-weight: bold\" >".
	"Oprava Variabilních symbolů dle jiné účetní jednotky</div>\n";
  
  echo "<div style=\"padding-left: 10px;\">\n";
 
  $rs = select_uj_query();
  
  echo "<div style=\"padding-top: 10px; padding-bottom: 30px; \" >Pohoda SQL: ";
    
  if(!$rs)
  {
    echo "<span style=\"color: red;\">Chyba při vykonávání dotazu pro výběr databází. <br>\n";     
    //zde pripadne vypis chybove hlasky
  }
  else
  { 
    echo "<span style=\"color: green; font-weight: bold;\">Dotaz pro výběr databází úspěšně proveden!</span> ".
	  "Nalezeno ".mssql_num_rows($rs)." záznamů.\n"; 
  }
 
  echo "</div>\n";
  
  $uj_a = select_uj($rs);
	    
  //form pro akci a vyber ucetnich jednotek
  echo "\n<form action=\"\" method=\"POST\" >\n";
   
    echo "<div style=\"float: left; \" >Jednotka, která se má upravit: </div>\n";
    
    echo "<div style=\"float: left; padding-left: 20px;\">\n";
    
	echo "<select name=\"uc_cilova\" size=\"1\" >\n";
	    
	    if( !is_array($uj_a) ){
		echo "  <option value=\"E\">CHYBA! nelze vypsat jednotky</option>\n";
	    }
	    else{
		    
		echo "<option value=\"0\" >Nevybráno</option>\n";
	    
		foreach ($uj_a as $key => $val) {    
		    $key == intval($_POST["uc_cilova"]) ? $selected = "selected" : $selected = "";
		 
		    echo "<option value=\"".intval($key)."\" ".$selected." >".htmlspecialchars($val)."</option>\n";
		}
	    }
	    
	        
	echo "</select>\n";
    
    echo "</div>\n\n";

    echo "<div style=\"float: left; padding-left: 20px; \" >Jednotka, kde jsou správné VS: </div>\n";
    
    echo "<div style=\"float: left; padding-left: 20px;\">\n";
    
	echo "<select name=\"uc_zdrojova\" size=\"1\" >\n";
	    
	    if( !is_array($uj_a) ){
		echo "<option value=\"E\">CHYBA! nelze vypsat jednotky</option>\n";
	    }
	    else{
		echo "<option value=\"0\" >Nevybráno</option>\n";
	    
		foreach ($uj_a as $key => $val) {
		    $key == intval($_POST["uc_zdrojova"]) ? $selected = "selected" : $selected = "";
		 
		    echo "  <option value=\"".intval($key)."\" ".$selected." >".htmlspecialchars($val)."</option>\n";    
		}
	    }
	    
	        
	echo "</select>\n";
    
    echo "</div>\n";

    echo "<div style=\"float: left; padding-left: 20px; \" >Akce: </div>\n";
    
    echo "<div style=\"float: left; padding-left: 20px;\">\n";
    
	echo "\t<select name=\"akce\" size=\"1\" >\n";
	
		$akce = array(0 => "Nevybráno", 1 => "Vypsat adresáře", 2 => "Upravit VS v adresáři (nevratné)");
		
		foreach ($akce as $key => $val) {
		    $key == intval($_POST["akce"]) ? $selected = "selected" : $selected = "";
		 
		    echo "  <option value=\"".intval($key)."\" ".$selected." >".htmlspecialchars($val)."</option>\n";    
		}
		
	echo "\t</select>\n";
    
    echo "</div>\n";
    
    echo "<div style=\"float: left; padding-left: 20px; \" ><input type=\"submit\" name=\"send\" value=\"OK\" ></div>\n";
    
  echo "</form>\n";
  
  echo "<div style=\"clear: both; padding-bottom: 10px;\">&nbsp;</div>\n";
  
  //zde kontrola akcí atd
  $uc_cilova = intval($_POST["uc_cilova"]);
  
  $uc_zdrojova = intval($_POST["uc_zdrojova"]);
  
  $akce = intval($_POST["akce"]);
  
  if( $akce == 1 or $akce == 2){
  
    //echo "debug: input vars: uc_cilova: $uc_cilova, uc_zdrojova: $uc_zdrojova ";
    
    //kontrola vstupnich promennych
    $check_rs = check_uj($uc_cilova, $uc_zdrojova);
    
    if($check_rs === false)
	exit;
    else
	list($uc_cilova_jmeno, $uc_zdrojova_jmeno) = $check_rs;
  }
  
  if($akce == 1 or $akce == 2){
    
    //
    // vypis adresaru ...
    //
    
    echo "<div style=\"float: left; padding-bottom: 10px; font-size: 16px; font-weight: bold;\">Výpis adresářů</div>\n";
    
    echo "<div style=\"float: left; padding-left: 100px; \">cílová uj: ".$uc_cilova_jmeno.", zdrojová uj: ".$uc_zdrojova_jmeno."</div>\n";
    
    echo "<div style=\"clear: both;\">&nbsp;</div>\n";
    
    $rs_ad = mssql_query("SELECT id, Smlouva, Jmeno, Firma FROM ".$uc_cilova_jmeno.".dbo.AD");

    if(!$rs_ad)
    {
	echo "<div style=\"color: red; padding-bottom: 10px;\">Chyba při vykonávání dotazu pro výběr dat z adrešáře. </div>\n";     
    //zde pripadne vypis chybove hlasky
    }
    else
    {	 
	echo "<div style=\"padding-bottom: 10px;\" ><span style=\"color: green; font-weight: bold; \">Dotaz pro výběr dat z adresáře úspěšně proveden!</span> ".
	  "Nalezeno ".mssql_num_rows($rs_ad)." záznamů.</div>\n"; 
    }

    $r1_w = 60;
    $r2_w = 120;
    $r3_w = 200;
    
    $r4_w = 30;
    
    $r5_w = 60;
    $r6_w = 120;
    $r7_w = 200;
    
    $r8_w = 100;
    $r9_w = 100;
    
    echo "<div style=\"float: left; width: ".$r1_w."px; \" >ID</div>\n".
	 "<div style=\"float: left; width: ".$r2_w."px; \" >Smlouva (VS)</div>\n".
	 "<div style=\"float: left; width: ".$r3_w."px; \" >Jméno / Společnost</div>\n".

	 "<div style=\"float: left; width: ".$r4_w."px; \" >_</div>\n".

	 "<div style=\"float: left; width: ".$r5_w."px; \" >ID</div>\n".
	 "<div style=\"float: left; width: ".$r6_w."px; \" >Smlouva</div>\n".
	 "<div style=\"float: left; width: ".$r7_w."px; \" >Jméno / Společnost</div>\n".
            
    "";
    if($akce == 2){
  
	echo "<div style=\"float: left; width: ".$r8_w."px; \">Výsl. úpravy</div>\n";

	echo "<div style=\"float: left; width: ".$r9_w."px; \">Ovl. řádků</div>\n";
    }
    
    echo "<div style=\"clear: both;\">&nbsp;</div>\n";
    
    while( $data1 = mssql_fetch_array($rs_ad) ){
    
    
	$Jmeno = iconv('CP1250', 'UTF-8', $data1["Jmeno"]);
	$Firma = iconv('CP1250', 'UTF-8', $data1["Firma"]);
	
	$id = $data1["id"];
	
	$rs_ad2 = mssql_query("SELECT id, Smlouva, Jmeno, Firma, ISNULL(Smlouva, 'XXX') AS Smlouva2 FROM ".$uc_zdrojova_jmeno.".dbo.AD WHERE ID = '".$id."'");

	$rs_ad2_num_rows = mssql_num_rows($rs_ad2);
	
	if (!$rs_ad2) {
	    echo "<div style=\"color: red; \"> MSSQL error: ". mssql_get_last_message()."</div>\n";
	}
	
	$data2 = mssql_fetch_row($rs_ad2);
	    
	$id2 = $data2[0];
	$Smlouva2 = $data2[4];
	
	$Jmeno2 = iconv('CP1250', 'UTF-8', $data2[2]);
	$Firma2 = iconv('CP1250', 'UTF-8', $data2[3]);

	//$Firma2 = "SELECT id, Smlouva, Jmeno, Firma FROM ".$uc_zdrojova_jmeno.".dbo.AD WHERE ID = '".$id."'";
	
	if($akce == 2 and $rs_ad2_num_rows == 1){
  
	    //
	    // uprava vs ...
	    //
	    
	    if( $Smlouva2 == "XXX"){
		$sql_e = "UPDATE ".$uc_cilova_jmeno.".dbo.AD SET Smlouva = Null WHERE id = '".$id."'";
	    }
	    else{
		$sql_e = "UPDATE ".$uc_cilova_jmeno.".dbo.AD SET Smlouva = '".$Smlouva2."' WHERE id = '".$id."'";        
	    }
	    
	    $rs_e = mssql_query($sql_e);
	    
	    $rs_e_aff_rows = mssql_rows_affected();
	    
	}
		
	
	echo "\n".
	 "<div style=\"float: left; width: ".$r1_w."px; \" >".$data1["id"]."</div>\n".
	 "<div style=\"float: left; width: ".$r2_w."px; \" >".$data1["Smlouva"]."&nbsp;</div>\n".
	 "<div style=\"float: left; width: ".$r3_w."px; \" >".$Jmeno." / ".$Firma."</div>\n".
        
	"";
	
	echo "<div style=\"float: left; width: ".$r4_w."px; \">&nbsp;</div>\n";
	
	echo "".
	 "<div style=\"float: left; width: ".$r5_w."px; \" >".$id2."</div>\n".
	 "<div style=\"float: left; width: ".$r6_w."px; \" >".$Smlouva2."&nbsp;</div>\n".
	 "<div style=\"float: left; width: ".$r7_w."px; \" >".$Jmeno2." / ".$Firma2."</div>\n".
            "";
	
	if($akce == 2 and $rs_ad2_num_rows == 1){
  	    echo "<div style=\"float: left; width: ".$r8_w."px; \">";
  	    
  	    if (!$rs_e) {
		echo "<span style=\"color: red; \">E: ". mssql_get_last_message()."</span>";
	    }
	    else{
		echo "<span style=\"color: green; \">OK</span>";
	    }
	    
	    //echo $sql_e;
	    
  	    echo "</div>\n";
	    echo "<div style=\"float: left; width: ".$r9_w."px; \">".$rs_e_aff_rows."</div>\n";
	}
	elseif($akce == 2){
	
	    echo "<div style=\"float: left; width: ".($r8_w + $r9_w)."px; \">".
		"<span style=\"color: orange; \">nejsou zdroj. data.</span>".
		"</div>";
	}
		
	echo "<div style=\"clear: both; height: 2px; border-bottom: 1px solid gray; \"></div>\n";
    	
	
    } //end of while
    
    
  } //end of elseif(akce == 1)
  
  else{
    echo "<div>Prosím vyberte akci a účetní jednotky ...</div>";
   
  }

  //konec uvodniho DIVu s padding-left
  echo "</div>\n\n\n";

?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 


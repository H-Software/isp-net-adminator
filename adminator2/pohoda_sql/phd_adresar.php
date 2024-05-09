<?php

$cesta = "../";

//zde vyresit prepinani roku/db jednotek
if(!isset($_GET["rok"]))
{ 
    $rok = date("Y"); 
    //$rok = 2009;
}
else
{ $rok = intval($_GET["rok"]); }


//$id_vlastnika = intval($_GET["id_vlastnika"]);

// $mssql_db = "StwPh_26109824_".$rok;

$level_col = "lvl_phd_adresar";

require($cesta."include/main.function.shared.php");
require_once($cesta."include/config.php"); 

require_once($cesta."include/config.ms.php"); 

require_once($cesta."include/check_login.php");

require_once($cesta."include/check_level.php");

//require("phd_global_function.php");

// if( !( check_level2($level,$level_col) ) )
// { // neni level
//   header("Location: ".$cesta."nolevelpage.php");
    
//   echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
//   exit;
// }

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head>';

require($cesta."include/charset.php"); 

?>

<title>Adminator 2 :: PohodaSQL :: Adresář</title> 

</head> 

<body> 

<?php require ($cesta."head.php"); ?> 

<?php require ($cesta."category.php"); ?> 
 
<tr>
 <td colspan="2" height="20" bgcolor="silver" >
   <?php require($cesta."vlastnici-cat-inc.php"); ?>
 </td>
</tr>
          
 <tr>
  <td colspan="2">
  
<?php
  
    echo "<div style=\"padding-left: 10px; padding-top: 10px;\" >\n"; //hlavni frame - pocty
    
    /*
    echo "<div style=\"padding-bottom: 20px; float: left; \">\n
	    <span style=\"font-family: Arial; font-weight: bold; \" >Výpis Adresáře</span>\n".
	 " z jednotky: <b>".$mssql_db."</b>, rok: <b>".$rok."</b>
	  </div>\n";
	  
    // ovladani roku
    echo "<form action=\"\" method=\"get\" >\n".
	  
	    "<div style=\"float: left; padding-left: 40px; padding-right: 40px;\" >Vyberte rok: </div>\n".
	    "<div style=\"padding-right: 40px; float: left;\" >\n
		<select name=\"rok\" size=\"1\" >\n";
		
		//vycet roku
		$start = 2006;
		$cil = date("Y");	
		
		for($i = $start; $i <= $cil; $i++)
		{
		    echo "<option value=\"".$i."\" ";
		    if( $i == $rok){ echo " selected "; }
		    echo " >".$i."</option>\n";
		}
		
    echo "	</select>
	    </div>".
	     
	   //1.sloupce - 3. radka 
	  "<div style=\"\">
	    <input type=\"submit\" name=\"OK\" value=\"Vybrat\">
	    </form>
	  </div>\n";

    */
    
 //zde dalsi obsah	  

 //souhrne pocty vlastniku
 echo "<div style=\"clear: both; padding-top: 5px; padding-bottom: 15px; font-size: 16px; font-weight: bold;\">".
	"Souhrnné počty zákazníků</div> \n";
 
 echo "<div style=\"font-weight: bold; padding-bottom: 20px;\">\n".
	"<div style=\"border-bottom: 2px solid black; float: left; width: 300px;\">Typ</div>\n".
	"<div style=\"border-bottom: 2px solid black; float: left; width: 150px;\">Počet Adminátor</div>\n".
	"<div style=\"border-bottom: 2px solid black; width: 150px; \">Počet Pohoda</div>\n";
 echo "</div>\n";

 //celkem
 $rs_q1 = pg_query("SELECT id_cloveka FROM vlastnici WHERE firma = 1");
 $rs_q1_num = pg_num_rows($rs_q1);
 
 $rs_ms1 = mssql_query("SELECT id FROM AD");
 $rs_ms1_num = mssql_num_rows($rs_ms1);
 
 echo "<div style=\"padding-bottom: 3px;\">\n"; 
 
 echo	"<div style=\"border-bottom: 1px solid gray; float: left; width: 300px;\">Celkem</div>\n".
	"<div style=\"border-bottom: 1px solid gray; float: left; width: 150px;\">".intval($rs_q1_num)."</div>\n".
	"<div style=\"border-bottom: 1px solid gray; float: left; width: 150px;\">".intval($rs_ms1_num)."</div>\n".
	"<div style=\"color: #333333; \">Nemusí souhlasit údaj Adminátor vs. Pohoda</div>\n";
	
 echo "</div>\n";

 //domaci - mesicni FA
 
 $rs_q2 = pg_query("SELECT id_cloveka 
			FROM vlastnici 
			WHERE ( (firma = 1) and (archiv is NULL or archiv = 0) 
					    and (billing_freq = 0) 
					    and (fakturacni is NULL)
			     )");
 $rs_q2_num = pg_num_rows($rs_q2);

 $rs_ms2 = mssql_query("SELECT id FROM AD WHERE Cislo LIKE '27DM%'");
 $rs_ms2_num = mssql_num_rows($rs_ms2);
 
 //pozastavene fakturace
 $rs_q2_pz = pg_query("SELECT id_cloveka FROM vlastnici 
			WHERE ( (firma = 1) and (archiv is NULL or archiv = 0) 
					    and (billing_freq = 0) 
					    and (fakturacni is NULL) 
					    and (billing_suspend_status = 1)
			      )");
			      
 $rs_q2_pz_num = pg_num_rows($rs_q2_pz);

 $rs_ms2_pz = mssql_query("SELECT id FROM AD WHERE 
					    ( (Cislo LIKE '27DM%') 
						AND (Zprava LIKE '%efakt%') 
						AND Skupina NOT LIKE 'VOIP'
					    )");
					    
 $rs_ms2_pz_num = mssql_num_rows($rs_ms2_pz);
 
 echo "<div style=\"padding-bottom: 5px;\">\n"; 
 
 echo	"<div style=\" border-bottom: 1px solid gray; float: left; width: 100px;\">DÚ - měsíční </div>\n".
	"<div style=\" border-bottom: 1px solid gray; float: left; width: 180px; text-align: right; padding-right: 20px;\">".
	    "(z toho pozastavených)</div>\n".
	"<div style=\" border-bottom: 1px solid gray; float: left; width: 150px;\">".
	intval($rs_q2_num)." (".intval($rs_q2_pz_num).") </div>\n".
	"<div style=\" border-bottom: 1px solid gray; width: 150px;\">".
	intval($rs_ms2_num)." (".intval($rs_ms2_pz_num).") </div>\n";
	
 echo "</div>\n";
 
 //
 //domaci - ctvrtletni FA
 //
 $rs_q3 = pg_query("SELECT id_cloveka FROM vlastnici 
			WHERE ( (firma = 1) and (archiv is NULL or archiv = 0) 
					    and (billing_freq = 1) 
					    and (fakturacni is NULL) 
				)");
				
 $rs_q3_num = pg_num_rows($rs_q3);

 $rs_ms3 = mssql_query("SELECT id FROM AD WHERE Cislo LIKE '47%'");
 $rs_ms3_num = mssql_num_rows($rs_ms3);
 
 // pozastavene fakturace
 $rs_q3_pz = pg_query("SELECT id_cloveka FROM vlastnici 
			WHERE ( (firma = 1) and (archiv is NULL or archiv = 0) 
					    and (billing_freq = 1) 
					    and (fakturacni is NULL) 
					    and (billing_suspend_status = 1)
				)");
				
 $rs_q3_pz_num = pg_num_rows($rs_q3_pz);

 $rs_ms3_pz = mssql_query("SELECT id FROM AD 
			    WHERE ( (Cislo LIKE '47%') 
			    		AND (Zprava LIKE '%efakt%') 
					AND Skupina NOT LIKE 'VOIP'
			    ) ");
			    
 $rs_ms3_pz_num = mssql_num_rows($rs_ms3_pz);
 
 echo "<div style=\"padding-bottom: 5px; \">\n"; 
 
 echo	"<div style=\"border-bottom: 1px solid gray; float: left; width: 100px;\">DÚ - čtvrtletní</div>\n".
	"<div style=\" border-bottom: 1px solid gray; float: left; width: 180px; text-align: right; padding-right: 20px;\">".
	    "(z toho pozastavených)</div>\n".
	"<div style=\"border-bottom: 1px solid gray; float: left; width: 150px;\">".
	    intval($rs_q3_num)." (".intval($rs_q3_pz_num).")</div>\n".
	"<div style=\"border-bottom: 1px solid gray; width: 150px;\">".
	    intval($rs_ms3_num)." (".intval($rs_q3_pz_num).") </div>\n";
	
 echo "</div>\n";

 //    
 //firemní - měsíční FA   
 //
 
 $rs_q4 = pg_query("SELECT id_cloveka FROM vlastnici 
			WHERE ( (firma = 1) and (archiv is NULL or archiv = 0) 
					    and (billing_freq = 0) 
					    and (fakturacni > 0) 
			      )");
			      
 $rs_q4_num = pg_num_rows($rs_q4);

 $rs_ms4 = mssql_query("SELECT id FROM AD WHERE Cislo LIKE '27_____'");
 $rs_ms4_num = mssql_num_rows($rs_ms4);

 //pozastavene fakturace
 $rs_q4_pz = pg_query("SELECT id_cloveka FROM vlastnici 
			WHERE ( (firma = 1) and (archiv is NULL or archiv = 0) 
					    and (billing_freq = 0) 
					    and (fakturacni > 0) 
					    and (billing_suspend_status = 1)
			      )");
			      
 $rs_q4_pz_num = pg_num_rows($rs_q4_pz);

 $rs_ms4_pz = mssql_query("SELECT id FROM AD 
			    WHERE ( (Cislo LIKE '27_____') 
			    		AND (Zprava LIKE '%efakt%') 
					AND Skupina NOT LIKE 'VOIP'			    
				    )");
 $rs_ms4_pz_num = mssql_num_rows($rs_ms4_pz);
 
 
 echo "<div style=\"padding-bottom: 5px;\">\n"; 
 
 echo	"<div style=\"border-bottom: 1px solid gray; float: left; width: 100px;\">FÚ - mesíční</div>\n".
	"<div style=\" border-bottom: 1px solid gray; float: left; width: 180px; text-align: right; padding-right: 20px;\">".
	    "(z toho pozastavených)</div>\n".
	"<div style=\"border-bottom: 1px solid gray; float: left; width: 150px;\">".
	    intval($rs_q4_num)." (".intval($rs_q4_pz_num).") </div>\n".
	"<div style=\"border-bottom: 1px solid gray; width: 150px;\">".
	    intval($rs_ms4_num)." (".intval($rs_ms4_pz_num).") </div>\n";
	
 echo "</div>\n";

 //
 //firemní - čtvrtelní FA   
 //
 
 $rs_q5 = pg_query("SELECT id_cloveka FROM vlastnici WHERE ( (firma = 1) and (archiv is NULL or archiv = 0) and (billing_freq = 1) and (fakturacni > 0) )");
 $rs_q5_num = pg_num_rows($rs_q5);

 $rs_ms5 = mssql_query("SELECT id FROM AD WHERE Cislo LIKE '37_____'");
 $rs_ms5_num = mssql_num_rows($rs_ms5);

 //pozastavene
 
 echo "<div style=\"padding-bottom: 5px;\">\n"; 
 
 echo	"<div style=\"border-bottom: 1px solid gray; float: left; width: 100px;\">FÚ - čtvrtletní</div>\n".
	"<div style=\" border-bottom: 1px solid gray; float: left; width: 180px; text-align: right; padding-right: 20px;\">".
	    "(z toho pozastavených)</div>\n". 
	"<div style=\"border-bottom: 1px solid gray; float: left; width: 150px;\">".intval($rs_q5_num)."</div>\n".
	"<div style=\"border-bottom: 1px solid gray; width: 150px;\">".intval($rs_ms5_num)."</div>\n";
	
 echo "</div>\n";
 
      
 echo "</div>\n"; //konec hlavniho frame - pocty
  
 echo "<div style=\"padding-left: 10px; padding-top: 10px;\" >\n"; //hlavni frame - vypis

 echo "<div style=\"clear: both; padding-top: 5px; padding-bottom: 15px; font-size: 16px; font-weight: bold;\">".
	"Detailní výpis zákazníků</div> \n";

 $style1 = "border-bottom: 1px solid black;";
 $style2 = "border-bottom: 2px solid black;";
 $style3 = "border-bottom: 1px solid gray;";
 
 echo "<div style=\"font-weight: bold; padding-bottom: 20px; \">\n".
 
	"<div style=\"".$style1." float: left; width: 50px; \">ID</div>\n".
	"<div style=\"".$style1." float: left; width: 500px;\">Jméno, Příjmení (Společnost)</div>\n".
	"<div style=\"".$style1." float: left; width: 150px;\">Variabilní symbol</div>\n".
	"<div style=\"".$style1." width: 150px; \">účetní index</div>\n";
 
 echo	"<div style=\"".$style2." width: 700px;\">Informace z Pohoda Adresáře</div>\n";
  
 echo "</div>\n";
 
 $sql = "SELECT id_cloveka, jmeno, prijmeni, vs, ucetni_index, fakturacni ".
	" FROM vlastnici WHERE ( (firma = 1) and (archiv is NULL or archiv = 0) )";
 
 $rs_vypis = pg_query($sql);

 while($data = pg_fetch_array($rs_vypis)){
 
    //priprava promennych
    if( $data["fakturacni"] > 0 ){
	
	$id_fa = intval($data["fakturacni"]);
	
	$rs_fa = pg_query("SELECT ftitle, fulice FROM fakturacni WHERE (id = '$id_fa') ");
	
	$rs_fa_num = pg_num_rows($rs_fa);
	
	if($rs_fa_num <> 1){
	    $jmeno = "Error: fakturacni ";
	}
	else{    	
	    while( $d2 = pg_fetch_array($rs_fa)){
		$jmeno = $d2["ftitle"].", ".$d2["fulice"];
	    }
	}
	
	//$jmeno = $id_fa;
    }
    else{
	
	$jmeno = htmlspecialchars($data["jmeno"]);
	$jmeno .= " ".htmlspecialchars($data["prijmeni"]);
    }
    
    echo "<div style=\"padding-bottom: 5px;\">\n"; 
 
    echo "<div style=\"float: left; width: 50px; font-weight: bold; \">".intval($data["id_cloveka"])."&nbsp;</div>\n".
	 "<div style=\"float: left; width: 500px;\">".$jmeno."&nbsp;</div>\n".
	    
	 "<div style=\"float: left; width: 150px;\">".htmlspecialchars($data["vs"])."&nbsp;</div>\n".
	 "<div style=\"width: 150px;\">".htmlspecialchars($data["ucetni_index"])."&nbsp;</div>\n";

    //zde vypis z pohody
    echo "<div style=\"".$style3." clear: both; float: left; width: 50px; font-weight: bold; \">".intval($phd_id)."&nbsp;</div>\n".
	 "<div style=\"".$style3." float: left; width: 500px;\">".$phd_jmeno."&nbsp;</div>\n".
	    
	 "<div style=\"".$style3." float: left; width: 150px;\">".$phd_vs."&nbsp;</div>\n".
	 "<div style=\"".$style3." width: 150px;\">".$phd_ucetni_index."&nbsp;</div>\n";
    	
    echo "</div>\n";
 
 
 } //end while            

 echo "</div>\n"; //konec hlavniho frame - vypis
    
?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

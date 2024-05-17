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

if( !isset($_GET["typ"]) )
{
    $typ = 1; //ve vychozim stavu se bude zobrazovat Internet :-)
}
else
{ $typ = intval($_GET["typ"]); }

$id_vlastnika = intval($_GET["id_vlastnika"]);


//$mssql_db = "StwPh_26109824_".$rok;

//ini_set("mssql.charset", "UTF-8");

require($cesta."include/main.function.shared.php");
require_once($cesta."include/config.php"); 

require_once($cesta."include/config.ms.php"); 

require($cesta."include/check_login.php");

require($cesta."include/check_level.php");

// require("phd_global_function.php");

if( !( check_level($level, 311) ) )
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

<title>Adminator 2 :: PohodaSQL :: Výpis FA</title> 

</head> 

<body> 

<?php include ($cesta."head.php"); ?> 

<?php include ($cesta."category.php"); ?> 
 
 <tr>
  <td colspan="2">
  
<?php
  
    echo "<div style=\"padding-left: 10px; padding-top: 10px;\" >\n";
    
    echo "<div style=\"padding-bottom: 20px; \">\n
	    <span style=\"font-family: Arial; font-weight: bold; \" >Výpis faktur za \"";
	
	if(($typ == 1) or ( $typ == "")){ echo "Internet"; }
	elseif($typ == 2){ echo "Hlas (VoIP)"; }
	elseif($typ == 3){ echo "Televize (IPTV)"; }
	elseif($typ == 4){ echo "Vše"; }
	else
	{ echo "Nelze zjistit"; }
	    
    echo "\"</span>\n
	     z jednotky: <b>".$mssql_db."</b>, rok: <b>".$rok."</b>
	  </div>\n".
	  
	  //prvni sloupec :: ovladani
	  "<div style=\"width: 265px; float: left; \" >\n".
	  
	    "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"get\" >\n".
	  
	    "<div style=\"float: left; padding-right: 40px; \">Vyberte rok: </div>\n".
	    "<div style=\"padding-right: 40px; \" >\n
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
		
    echo "	</select>\n
	    </div>\n".
	   
	   //1.sl - 2. radka
	   "<div style=\"padding-top: 20px;\" >\n".
	     "<div style=\"float: left; margin-right: 50px; \" >Faktury za: </div>\n".
	  
	     "<div>\n
	        <select name=\"typ\" size=\"1\" >\n";
	    
		echo "<option value=\"1\" "; if($typ==1 or !isset($typ))echo " selected "; echo " >Internet (či balíček)</option>\n";
		echo "<option value=\"2\" "; if($typ==2)echo " selected "; echo " >Hlas (VoIP)</option>\n";
		//echo "<option value=\"3\" "; if($typ==3)echo " selected "; echo " >Televize (IPTV)</option>\n";
		echo "<option value=\"4\" "; if($typ==4)echo " selected "; echo " >Vše (kromě IPTV)</option>\n";
		    
	echo	"</select>\n".	
	     "</div>\n".
	 
	   //konec 1.sl - 2. radka
	   "</div>\n".
	  
	   //1.sloupce - 3. radka 
	  "<div style=\"padding-top: 20px; padding-left: 115px;\">\n
	    <input type=\"submit\" name=\"OK\" value=\"Vybrat\">\n
	  </div>\n";

	echo "<input type=\"hidden\" name=\"id_vlastnika\" value=\"".$id_vlastnika."\" >\n";
	echo "</form>\n";
	   
        //konec DIVu prvni sloupec
	echo  "</div>\n";
	  
    //2. sloupec
    echo  "<div style=\"float: left; width: 300px; \" >\n".
	     "<div style=\"font-size: 12px; padding-bottom: 10px; \">Informace o vlastníkovi z Adminátora: </div>\n";

	show_client_info_adm();
	
    //konec DIVu 2.sloupce
    echo "</div>\n";
	  
    //3. sl
    echo "<div style=\"float: left; width: 300px; border: 0px solid black; \" >\n".	  
	     "<div style=\"font-size: 12px; padding-bottom: 10px; \">Informace o vlastníkovi dle Pohody: </div>\n";
    
	show_client_info_phd();
    
    //konec 3.sl
    echo "</div>\n";
    
    //4. sloupec
    echo  "<div style=\"\" >\n";	  
    
	//vnitrni DIV kvuli zarovnani
	echo "<div style=\"width: 220px; margin-left: auto;\">\n";
	
	show_fa_legend();
	
	//konec vnitrniho DIVu
	echo "</div>\n";
	
    //konec DIVu 4.sloupce
    echo  "</div>\n";
	  
    echo "<div style=\"padding-top: 5px; clear: both; width: 98%; height: 1px; color: #BBBBBB;\"><hr></div>\n";
	  
    //zde faktury :-)
    
    print_items();
    
    //hlavni DIV s posunem od okraju
    echo "</div> \n";   
 
 
 
 //
 // function 
 //

 function print_items()
 {
    global $typ, $error, $phd_ad_id, $rok;
    
    if( $error == 1 )
    {
	echo "<div style=\"font-size: 18px; color: red; font-weight: bold;\">".
	      "Položky nelze vypsat, vyskytla se chyba.</div>";
	
	return false;
    }
    
    echo "<div>".
	 "<div style=\"float: left; width: 120px; border: 1px solid black;  \" class=\"vyp-plat-2-table-first\" >Číslo </div>\n".
	 "<div style=\"float: left; width: 120px; border: 1px solid black;  \" class=\"vyp-plat-2-table-first\" >VS</div>\n".
	 "<div style=\"float: left; width: 100px; border: 1px solid black;  \" class=\"vyp-plat-2-table-first\" >Datum vystavení</div>\n".
	 "<div style=\"float: left; width: 100px; border: 1px solid black;  \" class=\"vyp-plat-2-table-first\" >Datum splatnosti</div>\n".
	 "<div style=\"float: left; width: 100px; border: 1px solid black;  \" class=\"vyp-plat-2-table-first\" >Částka s DPH</div>\n".
	 "<div style=\"float: left; width: 100px; border: 1px solid black;  \" class=\"vyp-plat-2-table-first\" >Uhrazené</div>\n".
	 "<div style=\"float: left; width: 200px; border: 1px solid black;  \" class=\"vyp-plat-2-table-first\" >Jméno <br>(Název společnosti)</div>\n".
	 "<div style=\"border: 1px solid black; \" class=\"vyp-plat-2-table-first\" >Stav úhrady <br>(Datum likvidace)</div>\n".
	"</div>\n\n";
					      
    //echo "<tr><td colspan=\"10\">&nbsp;</td></tr>";
			
    if( $typ == 1 ) //Internet
    { 
	$sql .= " AND ( SText LIKE '%".iconv('UTF-8', 'CP1250', "Fakturujeme%Vám%na%měsíc")."%' 
			 or
			SText LIKE '%".iconv('UTF-8', 'CP1250',"Fakturujeme%Vám%na%".$rok)."%' 
		      ) ) "; 
    
    }
    elseif($typ == 2) //VoIP
    { $sql .= "AND SText LIKE '%".iconv('UTF-8', 'CP1250', "Fakturujeme%Vám%za%VoIP%internetové%telefonování")."%' ) "; }
    elseif($typ == 3) //IPTV
    { $sql .= "AND SText LIKE 'iptv' ) "; }
    else //vsechno
    { $sql .= " ) "; } 
    
    $q = mssql_query("SELECT Firma, Jmeno, KcLikv, KcCelkem, DatLikv, DatSplat, Cislo, VarSym,
			convert(char, Datum, 104) AS DatumCZ,
			convert(char, DatSplat, 104) AS DatSplatCZ,
			convert(char, DatLikv, 104) AS DatLikvCZ,
			convert(char, DatSplat, 102) AS DatSplat2
			
			 FROM FA WHERE ( RefAD = '$phd_ad_id' ".$sql);
    
    $q_num = mssql_num_rows($q);
    
    if($q_num <= 0)
    {
	echo "<div style=\"padding-top: 20px; font-size: 18px; \" >Nenalezena žádná faktura  (dle výběru)</div>";
    }
    
    while($aktualni_faktura = mssql_fetch_array($q))
    {
      //priprava a konverze promennych
      $FIRMA = iconv("CP1250","UTF-8",$aktualni_faktura["Firma"]);
      $Jmeno = iconv("CP1250","UTF-8",$aktualni_faktura["Jmeno"]);

      $KCLIKV = round($aktualni_faktura["KcLikv"], 2);
      $KCCELKEM = round($aktualni_faktura["KcCelkem"], 2);
      
      if( ( strlen($FIRMA) > 1 ) )
      { $prijemce = $FIRMA; }
      else
      { $prijemce = $Jmeno; }

      if($KCLIKV == 0)
      {
    	  if( $aktualni_faktura["DatSplat"] <= $aktualni_faktura["DatLikv"] )
    	  { $barva = "#ADD8E6;"; $stav = 2; } //zaplaceno po splatnosti
          else
          { $barva = "#00FF87;"; $stav = 1; } //zaplaceno - zelena
      }
      else
      {
	  if( $aktualni_faktura["DatSplat2"] >= date("Y.m.d"))
          { $barva = "#ECB361;"; $stav = 3; } //nezaplaceno ve splatnosti
          else
          { $barva = "#FF8787;"; $stav = 0; } //nezaplaceno PO splatnosti (cervena)
      }
	      
       echo "<div>";
    
	echo "<div style=\"float: left; width: 120px; background-color: ".$barva."; border: 1px solid black;\" ".
	     " class=\"tab-vypis-plateb-horni\" >".$aktualni_faktura["Cislo"]."</div>\n".
	     "<div style=\"float: left; width: 120px; background-color: ".$barva."; border: 1px solid black;\" ".
	     "class=\"tab-vypis-plateb-horni\" >".$aktualni_faktura["VarSym"]."</div>\n".
             "<div style=\"float: left; width: 100px; background-color: ".$barva."; border: 1px solid black;\" ".
	     "class=\"tab-vypis-plateb-horni\" >".$aktualni_faktura["DatumCZ"]."</div>\n".
	     "<div style=\"float: left; width: 100px; background-color: ".$barva."; border: 1px solid black;\" ".
	     "class=\"tab-vypis-plateb-horni\" >".$aktualni_faktura["DatSplatCZ"]."</div>\n".
             "<div style=\"float: left; width: 100px; background-color: ".$barva."; border: 1px solid black;\" ".
	     "class=\"tab-vypis-plateb-horni\" >".$KCCELKEM."</div>\n";

        echo "<div style=\"float: left; width: 100px; background-color: ".$barva."; border: 1px solid black;\" class=\"tab-vypis-plateb-horni\" >";
        echo ( $KCCELKEM - $KCLIKV );
        echo "</div>\n";

	//jmeno/firma
        echo "<div style=\"float: left; width: 200px; background-color: ".$barva."; border: 1px solid black;\" class=\"tab-vypis-plateb-horni\" >".
	     $prijemce."</div>\n";
     
        echo "<div style=\"background-color: ".$barva."; border: 1px solid black; \" class=\"tab-vypis-plateb-horni\" >&nbsp;";
          if( $stav == 1 or $stav == 2){ echo $aktualni_faktura["DatLikvCZ"]; }
          else{ echo "Neuhrazené"; }
        echo "</div>\n";

	echo "<div style=\"clear: both;\">&nbsp;</div>";
	
      echo "</div>";
      
    } //end of while

 } //end of function print_items
 
 function show_client_info_phd()
 {
    global $full_ui, $error, $id_vlastnika, $phd_ad_id;
    
    $full_ui = generate_fully_fin_index($id_vlastnika);
    
    if( empty($full_ui) )
    {
	echo "<div style=\"color: red; font-weight: bold;\">".
	    "CHYBA! Nelze vypsat, klient není spárován či chybí \"Účetní index\". (".$full_ui.") </div>";
    
	$error = 1;
    }
    else
    {
	$sql_rows = "Firma, Jmeno, Ulice, Obec, ICO, DIC, PSC, ID, Cislo ";
	
	$full_ui_cp1250 = iconv('UTF-8','CP1250', $full_ui);
	
	$q=mssql_query("SELECT ".$sql_rows." FROM AD WHERE AD.Cislo = '$full_ui_cp1250' ");
	$q_num = mssql_num_rows($q);
	
	$q_sql = "SELECT ".$sql_rows." FROM AD WHERE AD.Cislo = '$full_ui_cp1250' ";
	
	if($q_num <> 1)
	{
	    echo "<div style=\"font-size: 14px; color: red; font-weight: bold;\">".
		"CHYBA! Informace nelze vypsat, neočekávaný formát výstupních dat. (".$full_ui.")</div>";
	    $error = 1;
	}
	else
	{
	  echo "<span style=\"color: grey; \">";
	
	  while( $adresar_data = mssql_fetch_array($q))
	  {
	    $Firma = iconv('CP1250', 'UTF-8', $adresar_data["Firma"]); 
	    $Jmeno = iconv('CP1250', 'UTF-8', $adresar_data["Jmeno"]);

	    $Ulice = iconv('CP1250', 'UTF-8', $adresar_data["Ulice"]);
	    $Obec = iconv('CP1250', 'UTF-8', $adresar_data["Obec"]);
	    	    
	    $phd_ad_id = $adresar_data["ID"];
            
	    if( (strlen($Firma) < 1 ) )
	    { echo $Jmeno."<br>"; }
	    else
	    {
	      echo "<div style=\"padding-bottom: 10px; \">";
	      echo $Firma."<br> IČO: ".$adresar_data["ICO"].", DIČ: ".$adresar_data["DIC"];
	      echo "</div>";
	    }
	    //echo "<br>";
	    echo $Ulice.", ".$Obec." ".$adresar_data["PSC"]."<br>";
	    echo "[ad_id]: ".$adresar_data["ID"].", ".
		"<br>účetní index: ".$adresar_data["Cislo"]." (".$full_ui.")";
	
	    echo "</span>";
          
	  } //end while
       
       } //end else if q_num <> 1
    
    } //end else if is_numeric(phd_ad_id)
    
 } //end of function show_client_info_phd
  
 function show_client_info_adm()
 {
    global $id_vlastnika, $db_ok2, $phd_ad_id;
    
    //new sql
    //SELECT vlastnici.jmeno, vlastnici.prijmeni, fakturacni.ftitle FROM vlastnici LEFT JOIN fakturacni ON vlastnici.fakturacni = fakturacni.id
    
    $sql_rows = "vlastnici.jmeno, vlastnici.prijmeni, vlastnici.ulice, vlastnici.mesto, vlastnici.psc, ".
		"vlastnici.id_cloveka, vlastnici.ucetni_index, vlastnici.vs, ".
		"fakturacni.ftitle, fakturacni.ico, fakturacni.fulice, fakturacni.fmesto, fakturacni.fpsc ";
    
    $dotaz_vlastnik = pg_query("
				SELECT ".$sql_rows." FROM vlastnici LEFT JOIN fakturacni 
				ON vlastnici.fakturacni = fakturacni.id
				WHERE id_cloveka = '$id_vlastnika' ");
    $dotaz_vlastnik_radku = pg_num_rows($dotaz_vlastnik);
   
    if($dotaz_vlastnik_radku <> 1)
    { 
	echo "Chyba! Nelze zjistit informace o vlastnikovi! (".$dotaz_vlastnik_radku.")<br> E: ".
	pg_last_error($db_ok2); 
    }
    else
    {
        while($data=pg_fetch_array($dotaz_vlastnik))
        {
            //echo "<div style=\"font-size: 12px; padding-bottom: 10px; \">Informace o vlastníkovi: </div>";

            echo "<span style=\"color: grey; \">";

	    if( $data["ftitle"] != NULL)
	    { 
	        echo "".$data["ftitle"].", IČ: ".$data["ico"]."<br>"; 
		echo "(".$data["jmeno"]." ".$data["prijmeni"].")";

        	echo "<br>".$data["fulice"].", ".$data["fmesto"]." ".$data["fpsc"];
	    }
	    else
	    { 
		echo $data["jmeno"]." ".$data["prijmeni"]; 
	        echo "<br>".$data["ulice"].", ".$data["mesto"]." ".$data["psc"];
	    }
	    
            echo "<br>id: [".$data["id_cloveka"]."], účetní index: ".$data["ucetni_index"];
            echo "<br> vs: ".$data["vs"];

            echo "</span>";
        }
    }

 } //end of function show_client_info_adm
 
 function show_fa_legend()
 {
    echo "
	    <div style=\"float: left; width: 20px; margin-right: 5px; border: 1px solid #aaaaaa; background:#FFFFFF; \">&nbsp;</div>
		<div style=\"height: 25px; \">Nefakturováno </div>
	    
	    <div style=\"float: left; width: 20px; margin-right: 5px; border: 1px solid #aaaaaa; background:#00FF87;\">&nbsp;</div>
		<div style=\"height: 25px; \">Zaplaceno </div>
	    
	    <div style=\"float: left; width: 20px; margin-right: 5px; border: 1px solid #aaaaaa; background:#ADD8E6;\">&nbsp;</div>
		<div style=\"height: 25px; \">Zaplaceno po splatnosti </div>
	   
	    <div style=\"float: left; width: 20px; margin-right: 5px; border: 1px solid #aaaaaa; background:#ECB361;\">&nbsp;</div>
		<div style=\"height: 25px; \">Nezaplaceno ve splatnosti </div>
	   
	    <div style=\"float: left; width: 20px; margin-right: 5px; border: 1px solid #aaaaaa; background:#FF8787;\">&nbsp;</div>
		<div style=\"height: 25px; \">Nezaplaceno po splatnosti </div>
	";
 
 } //end of function show fa legend
  
?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 


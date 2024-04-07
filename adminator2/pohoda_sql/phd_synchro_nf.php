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

$adminator_db_nf = "faktury_neuhrazene";

$level_col = "lvl_phd_synchro_nf";

//ini_set("mssql.charset", "UTF-8");
set_time_limit(10);

require_once($cesta."include/config.php"); 

require_once($cesta."include/config.ms.php"); 

require($cesta."include/check_login.php");

require($cesta."include/check_level.php");

require("phd_global_function.php");

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

<title>Adminator 2 :: PohodaSQL :: Synchronizace neuhrazených faktur</title> 

</head>

<body>

<?php include ($cesta."head.php"); ?> 

<?php include ($cesta."category.php"); ?> 

 <tr>
   <td colspan="2" height="20" bgcolor="silver" ><? include("../fn-cat.php"); ?> </td>
 </tr>

 <tr>
  <td colspan="2">
  
<?php

  echo "<div style=\"padding-left: 10px; padding-top: 10px; font-weight: bold\" >".
	"Synchronizace neuhrazených faktur (Pohoda SQL vs. Adminator)</div>\n";
  
  echo "<div style=\"padding-left: 10px;\">\n";
 
  //
  // vyber neuhra. faktur z Pohody (MS_SQL)
  // 
  $rs=mssql_query("
		    SELECT Cislo, VarSym, Firma, Jmeno, ICO, DIC, KcCelkem, KcLikv, RefAD,
		    convert(char, Datum, 104) AS DatumCZ,
		    convert(char, DatSplat, 104) AS DatSplatCZ

		    FROM FA WHERE ( 
			(KcLikv > 0) 
			AND 
			
			(convert(char, DatSplat, 112) < convert(char, GETDATE(), 112) )

			AND
			(
			  Cislo LIKE '2701%'
			   OR
			  Cislo LIKE '2801%'
			   OR
			  Cislo LIKE '2901%'
			   OR
			  Cislo LIKE '1001%'
			   OR
			  Cislo LIKE '1101%'
			   OR
			  Cislo LIKE '1201%'
			)
		    )								    
		    ");

  echo "<div style=\"padding-top: 10px; padding-bottom: 10px; \" >Pohoda SQL: ";
    
  if(!$rs)
  {
    echo "<span style=\"color: red;\"> Chyba při vykonávání dotazu pro výběr neuhr. faktur. <br>\n"; 
    
    //zde pripadne vypis chybove hlasky
  }
  else
  { 
    echo "<span style=\"color: green; font-weight: bold;\">dotaz pro výběr neuhr. faktur vykonan v poradku!</span> Nalezeno ".
	    mssql_num_rows($rs)." záznamů."; 
  }
 
  echo "</div>";
  
  //
  // vynulovani kontrolniho sloupce "overeno" v adminator (MY_SQL)
  //
  $overeno_stav=mysql_query("UPDATE ".$adminator_db_nf." SET overeno = '0' ");
  
  echo "<div style=\"padding-top: 10px; padding-bottom: 10px;\">Adminator: ";
   
  if($overeno_stav !== true)
  { 
     echo "<span style=\"color: red; font-weight: bold;\"> Nelze vynulovat kontrolni sloupec \"Overeno\"!</span>".
     " <b>Info:</b> ".mysql_error(); 
  }
  else
  {
     echo "<span style=\"color: green; font-weight: bold;\" >Kontrolni sloupec \"overeno\" vynulovan uspesne! </span>"; 
  }
  echo "</div>";    
 
  //
  // vypis faktur
  //
  $datum_akt = strftime("%Y-%m-%d", time());
  
  echo "<br><b>aktuální datum</b>: $datum_akt <br><br>";
  
  echo "<table border=\"1\"><tr>";
  echo "<td>cislo: </td>";
  echo "<td>var sym: </td>";
  echo "<td>Datum: </td>";
  echo "<td>DatSplat: </td>";
  echo "<td>KcCelkem: </td>";
  echo "<td>KcLikv: </td>";
  
  echo "<td>Firma: </td>";
  echo "<td>Jmeno: </td>";
  
  echo "<td>ICO: </td>";
  echo "<td>DIC: </td>";
 
  echo "<td>poř. číslo: </td>";
  echo "<td>stav<br> uložení: </td>";
 
  echo "<td>ID vlastníka: </td>";
  
  echo "</tr>";

  $i=1;

  while ( ( $data=mssql_fetch_array($rs) ) )
  {
    //priprava promennych z DB
    
    $Cislo=$data["Cislo"];
    $VarSym = $data["VarSym"];
    
    $DatumCZ = $data["DatumCZ"];
    $DatSplatCZ = $data["DatSplatCZ"];

    $ico = $data["ICO"];
    $dic = $data["DIC"];

    $KcCelkem = $data["KcCelkem"];
    $KcLikv = $data["KcLikv"];
    
    $Firma = iconv("CP1250","UTF-8", $data["Firma"]);
    $Jmeno = iconv("CP1250","UTF-8", $data["Jmeno"]);
        
    $refAD = $data["RefAD"];
	
    list($rok_akt,$mesic_akt,$den_akt) =split("-",$datum_akt);
          
    list($den_db,$mesic_db,$rok_db) = explode(".",$DatSplatCZ);

    $DatumCZ = trim($DatumCZ);
    $DatSplatCZ = trim($DatSplatCZ);
	
    list($den_datum,$mesic_datum,$rok_datum) = explode(".",$DatumCZ);
    list($den_splat,$mesic_splat,$rok_splat) = explode(".",$DatSplatCZ);

    $DatumCZ_ins = $rok_datum."-".$mesic_datum."-".$den_datum;
    $DatSplatCZ_ins = $rok_splat."-".$mesic_splat."-".$den_splat;
        
    $kc_celkem = round($KcCelkem, 2);
    $kc_likv = round($KcLikv, 2);     
  
    
    //nulování promenych
    $error = "";
    $par_id_vlastnika = "";
    
    $id_faktury = "";
    $id_cloveka = "";
    
    //pozustatek po posunovani splatnosti
    $zobrazovat="ano";
    
    if( $zobrazovat == "ano" )
    {
       echo "<tr>";
 
	echo "<td>".$Cislo."</td>";
	echo "<td>".$VarSym."</td>";
	
	echo "<td>".$den_datum.".".$mesic_datum.".".$rok_datum."</td>"; //echo "<td>".$DatumCZ."</td>";
	
	echo "<td>".$den_splat.".".$mesic_splat.".".$rok_splat."</td>";

	echo "<td>".$kc_celkem."</td>";
	echo "<td>".$kc_likv."</td>";
  
	echo "<td>".$Firma."</td>";
	echo "<td>".$Jmeno."</td>";
  
	echo "<td>".$ico."</td>";
	echo "<td>".$dic."</td>";
   
	echo "<td>".$i."</td>";

	// ted zjistime jestli uz FA neni v db
  
	$sql2 = "SELECT * FROM ".$adminator_db_nf." WHERE ( ";
	$sql2 .= "Cislo LIKE '$Cislo' and VarSym LIKE '$VarSym' and Datum = '$DatumCZ_ins' ";
	$sql2 .= "and DatSplat = '$DatSplatCZ_ins' and KcCelkem  LIKE '$kc_celkem' ";
	$sql2 .= "and KcLikv LIKE '$kc_likv' and Firma LIKE '$Firma' and Jmeno LIKE '$Jmeno' ";
	$sql2 .= "and ICO LIKE '$ico' and DIC LIKE '$dic' AND po_splatnosti_vlastnik = '1') ";  
	
	$dotaz2=mysql_query($sql2);
	$dotaz2_radku=mysql_num_rows($dotaz2);
  
	if( $dotaz2_radku == 1 )
	{   // faktura existuje, zjistime id a upravime
	
	    while($data2 = mysql_fetch_array($dotaz2) )
	    { $id_faktury=$data2["id"]; }
    
	    $uprava=mysql_query("UPDATE ".$adminator_db_nf." SET overeno='1' WHERE id=".$id_faktury." Limit 1 ");

	    if ($uprava){ echo "<td><span style=\"color: green; \" >O-OK</span></td>"; }
	    else { echo "<td><span style=\"color: red; \" >O-E </td>"; }
     
	} // konec if dotaz_radku == 1
	elseif( $dotaz2_radku == 0 )
	{
	    // faktura neexistuje, takze ji pridame
	    $add=mysql_query("INSERT INTO ".$adminator_db_nf." (Cislo,VarSym,Datum,DatSplat, KcCelkem, KcLikv, ".
				"Firma,Jmeno,ICO,DIC,overeno, po_splatnosti_vlastnik ) VALUES ('$Cislo','$VarSym','$DatumCZ_ins',".
				"'$DatSplatCZ_ins','$kc_celkem','$kc_likv','$Firma','$Jmeno','$ico','$dic','1','1' ) ");
 
	    if ($add){ echo "<td><span style=\"color: green; \" >I-OK</span></td>"; }
    	    else { echo "<td><span style=\"color: red; \" >I-E </td>"; }
	}
	else
	{ echo "<td><span style=\"color: red; \" >E (".$dotaz2_radku.")</span></td>"; } 

	//zde výpis id vlastníka a párování 
	
	$dotaz_ad = mssql_query("SELECT Cislo FROM AD WHERE ID = '$refAD' ");
	
	$dotaz_ad_radku = mssql_num_rows($dotaz_ad); 
	if( ($dotaz_ad_radku <> 1) )
	{ 
	    $error .= "Chyba: \"E1: nelze vybrat klienta z adresare Pohoda\" (redAD: ".$refAD.", dotaz_ad_radku: ".$dotaz_ad_radku.")"; 
	    
	    $id_cloveka = 0;
	}
	else
	{
	   while( $data_ad = mssql_fetch_array($dotaz_ad))
	   {
	     $ad_cislo = iconv("CP1250","UTF-8", $data_ad["Cislo"]);
	    
	     if( ereg(".*VYŘ.*", $ad_cislo) )
	     { // archiv
	        $ad_rada = substr($ad_cislo, 0, 6);
		$ui = substr($ad_cislo, 6, 10);
	     
	        if( is_numeric($ui) )
		{
	          $sql_vlastnik = "SELECT id_cloveka FROM vlastnici 
				    WHERE ( ucetni_index = '$ui' AND archiv = 1 ) ";
	        }
		else
		{ 
		    $sql_vlastnik = ""; 
		    $error .= "chybny typ UI\n"; 
		}
	     }
	     elseif( ereg("^37.*", $ad_cislo) )
	     { //ctvrtletni - fakturacni
	        $ad_rada = substr($ad_cislo, 0, 2);
		$ui = substr($ad_cislo, 2, 7);
	     
	        if( is_numeric($ui) )
		{
	    	    $sql_vlastnik = "SELECT id_cloveka FROM vlastnici 
				    WHERE ( ucetni_index = '$ui' 
					    AND billing_freq = 1 
					    AND ( archiv = 0 OR archiv IS NULL )
					    AND (fakturacni > 0)
					   ) ";  
	        }
		else
		{ 
		    $sql_vlastnik = ""; 
		    $error .= "chybny typ UI\n"; 
		}
	     }
	     elseif( ereg("^47.*", $ad_cislo) )
	     { //ctvrtletni 
	        $ad_rada = substr($ad_cislo, 0, 2);
		$ui = substr($ad_cislo, 2, 7);
	     
	        if( is_numeric($ui) )
		{
	    	    $sql_vlastnik = "SELECT id_cloveka FROM vlastnici 
				    WHERE ( ucetni_index = '$ui' 
					    AND billing_freq = 1 
					    AND ( archiv = 0 OR archiv IS NULL )
					    AND (fakturacni is null)
					   ) ";  
	        }
		else
		{ 
		    $sql_vlastnik = ""; 
		    $error .= "chybny typ UI\n"; 
		}
	     }
	     elseif( ereg(".*DM.*", $ad_cislo) )
	     { //domaci uzivatele
	        $ad_rada = substr($ad_cislo, 0, 4);
		$ui = substr($ad_cislo, 4, 9); 
	
		if( is_numeric($ui) )
		{
			$sql_vlastnik = "SELECT id_cloveka FROM vlastnici 
				    WHERE ( 
					    ucetni_index = '$ui' 
					    AND fakturacni is NULL 
					    AND billing_freq = 0
					    AND ( archiv = 0 OR archiv IS NULL )
					   ) ";
	        }
		else
		{ 
		    $sql_vlastnik = ""; 
		    $error .= "chybny typ UI\n"; 
		}

	     }
	     else
	     { //firemni
	        $ad_rada = substr($ad_cislo, 0, 2);
		$ui = substr($ad_cislo, 2, 7); 

		if( is_numeric($ui) )
		{    
		        $sql_vlastnik = "SELECT id_cloveka FROM vlastnici 
				    WHERE ( 
					    ucetni_index = '$ui' 
					    AND fakturacni > 0 
					    AND billing_freq = 0
					    AND ( archiv = 0 OR archiv IS NULL )
					    ) ";
	        }
		else
		{ 
		    $sql_vlastnik = ""; 
		    $error .= "chybny typ UI\n"; 
		}

	     } //else if cosi ..
	     
	     $vlastnik_dotaz = pg_query($sql_vlastnik);
	
	     if(!$vlastnik_dotaz)
	     {
	         $error = "Chyba při vykonávání dotazu \"vlastnik_dotaz\" (sql dotaz: ".$sql_vlastnik;
		 $error .= ", ui: ".$ui.") ";
	     
	         $error .= pg_last_error($db_ok2);
		 
	         $id_cloveka = 0;
	     }
	     else
	     {	 
	       $num_vlastnik = pg_num_rows($vlastnik_dotaz);
	
	       if($num_vlastnik == 1)
	       { //vlastnika se podarilo identifikovat
	          while( $data_vlastnika = pg_fetch_array($vlastnik_dotaz))
		  {
		    $id_cloveka = $data_vlastnika["id_cloveka"];	    		
		  } //konec while pg fetch array
	       } //konec if num_vlastnik == 1
	       else
	       { //vlastnika se neporadilo zjistit (spatne VS, archiv atd, pripadne moc lidi 
	      	  $id_cloveka = 0;
	     
	          $error .= "Chyba: \"E2 (".$num_vlastnik.")\", nepodařilo se určit vlastníka dle \"Účetní index\".";
		  //$error .= " SQL: ".$sql_vlastnik.", ";
		  $error .= "UI: ".$ad_rada."/".$ui;
	       }
	         
	      } //else if ! vlastnik_dotaz
	     
	      //zde uprava párovacího id_klienta
	     
	      //nekde chyba, a nenaplnuje se promenna id_cloveka :((
	      if( !($id_cloveka >= 0) )
	      {
	        $error .= "Chyba: \"E3 (".$id_cloveka.")\", prazdna promenna \"id_cloveka\" ";
	      }
	      else
	      {
		  //zde pripadne este dodelat dalsi kontrolu id_fakturry ...
		  
		  //finalni uprava, pokud jsou spravne promenny
	          $vlastnik_update=mysql_query("UPDATE ".$adminator_db_nf." SET par_id_vlastnika = '$id_cloveka' WHERE id = '$id_faktury'");
	      }
	
	      //$par_id_vlastnika = $ad_rada."/".$ui."(".$num_vlastnik.")";
	    
	   } //end of while
	
	} //end of else if mssq_num_rows dotaz_ad
	
	//finalni vypis
	//debug: echo "<td>".$id_cloveka." (id_fa:".$id_faktury.")</td>";
	echo "<td>".$id_cloveka."</td>";
	
     // konec radku
     echo "</tr>";

    //echo "<tr><td colspan=\"13\">"."UPDATE ".$adminator_db_nf." SET par_id_vlastnika = '$id_cloveka' WHERE id = '$id_faktury'"."</td></tr>";
    
    if( isset($error) )
    {
	echo "<tr>
		<td colspan=\"13\"><span style=\"color: red;\">".$error."</span></td>
	      </tr>\n";
    }
    
    //debug print "<tr><td colspan=\"12\">".$sql2."</td></tr>";

    $i++;
    
    } // konec if zobrazovat == ano
  } // konec while
 
  echo "</table>";
  
  //
  // smazani neoverenych, resp. prebytecny faktur s adminator DB neuh. faktury
  //
   
  // ted smazeme vsechno neoverene
  $vymaz = mysql_query("DELETE FROM ".$adminator_db_nf." WHERE overeno = '0' ");
  
  echo "<div style=\"padding-top: 10px; padding-bottom: 10px;\" >";
  
  if($vymaz !== true)
  { 
    echo "<span style=\"color: red; font-weight: bold; \">Nelze vymazat neověřené záznamy z ".
    "tabulky \"".$adminator_db_nf."\"!</span> Info: ".mysql_error(); 
  }
  else
  {
    echo "<span style=\"color: green; font-weight: bold; \" >Neověřené záznamy z tabulky \"".
    $adminator_db_nf."\" úspešně vymazány! </span>"; 
  }
  
  $datum_import = strftime("%Y-%m-%d %H:%M:%S", time());

  //pak ve finalne zapnout
  $log=mysql_query("INSERT INTO fn_import_log (datum, stav) VALUES ('$datum_import','1' ) ");
      
  if($log !== true)
  { 
    echo ("<div style=\"color: red; font-weight: bold;  padding-top: 10px; padding-bottom: 10px;\">
      Nelze vlozit záznam do import logu. Info: ".mysql_error()."</div>"); 
  }
  else
  {
    echo "<div style=\"color: green; font-weight: bold;  padding-top: 10px; padding-bottom: 10px; \" >
    Záznam úspešně vložen do import logu! </div>"; 
  }
  
  //zde dodelat pridani akce do archivu změn
  
   
  //konec uvodniho DIVu s padding-left
  echo "</div>";

?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 


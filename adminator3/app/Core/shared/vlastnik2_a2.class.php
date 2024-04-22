<?php

class vlastnik2_a2
{
   var $conn_mysql;

   var $logger;

   var $level;
       
   var $export_povolen;

   public static function vypis_tab ($par)
   {
     if ($par == 1) { echo "\n".'<table border="1" width="100%">'."\n"; }
     elseif ($par == 2) { echo "\n".'</table>'."\n"; }
     else    { echo "chybny vyber"; }
    // konec funkce vypis_tab
   }

   // $dotaz_final - for pq_query
   function vypis ($sql,$co,$dotaz_final)
   {
				
    // co - co hledat, 1- podle dns, 2-podle ip		
	echo "<pre>" . var_export($dotaz_final, true) . "</pre>";

    $dotaz=pg_query($dotaz_final);

	if($dotaz !== false){
    	$radku=pg_num_rows($dotaz);
	}
	else{
		echo("<div style=\"color: red;\">Dotaz selhal! ". pg_last_error($db_ok2). "</div>");
	}

    if($radku==0) echo "<tr><td><span style=\"color: red; \" >Nenalezeny žádné odpovídající výrazy dle hledaného \"".$sql."\". </span></td></tr>";
    else
    {

     while( $data=pg_fetch_array($dotaz) ) 
     {
	    echo "<tr><td colspan=\"16\"> <br> </td> </tr>
	    <tr>
		<td class=\"vlastnici-td-black\"><br></td>
		 <td class=\"vlastnici-td-black\" colspan=\"3\" width=\"\" >
	    
	    id: [".$data["id_cloveka"]."]".
	    
	     ", Účetní index: [";
	     
	     if($data["archiv"] == 1)
	     {
	        echo "27VYŘ";
	     }
	     elseif(( ($data["billing_freq"] == 1) and ($data["fakturacni"] > 0) ) )
	     {
	        echo "37";
	     }
	     elseif( $data["billing_freq"] == 1 )
	     { //ctvrtletni fakturace
	        echo "47";											
	     }
	     elseif( ($data["fakturacni"] > 0) )
	     { //faturacni
	           echo "27";
	     }
	     else
	     {  //domaci uzivatel
	           echo "27DM";
	     }
	     
	     echo sprintf("%05d", $data["ucetni_index"]);
	     
	     echo "], Splatnost ke dni: [".$data["splatnost"]."]</td>
	    
	    <td class=\"vlastnici-td-black\" colspan=\"2\">VS: ".$data["vs"]."</td>
	
	    <td class=\"vlastnici-td-black\" colspan=\"4\"> Platit (bez DPH): ".$data["k_platbe"]."</td>
	    <td class=\"vlastnici-td-black\" colspan=\"6\" align=\"right\" width=\"\" >";
	
	    echo "<table border=\"0\" width=\"70%\" > <tr> <td class=\"vlastnici-td-black\" width=\"\" >";
	
	// sem mazani
	global $vlastnici_erase_povolen;
	
	if( ! ( $vlastnici_erase_povolen == "true" ) )
	{ echo "<span style=\"\" > smazat </span> "; }
	else
	{
	    echo "<form method=\"POST\" action=\"vlastnici2-erase.php\" >";
	    echo "<input type=\"hidden\" name=\"erase_id\" value=\"".$data["id_cloveka"]."\" >";
	    echo "<input type=\"submit\" value=\"Smazat\" >"."</form> \n";
	}
	
	echo "</td>
	<td class=\"vlastnici-td-black\" >";
	
	global $vlastnici_update_povolen;
	// 6-ta update
	if ( !( $vlastnici_update_povolen =="true") )
	{ echo "<span style=\"\" >  upravit  </span> \n"; }
	else
	{
	 echo " <form method=\"POST\" action=\"vlastnici2-change.php\" >";
	 echo "<input type=\"hidden\" name=\"update_id\" value=\"".$data["id_cloveka"]."\" >";
	 echo "<input type=\"submit\" value=\"update\" ></form> \n";
	}
	
	 echo "</td> </tr></table>"; 
	 echo "</td></tr>";
	 
	 echo "<tr>";
	 echo "<td class=\"vlastnici-td-black\" ><br></td>";
	 echo "<td class=\"vlastnici-td-black\" colspan=\"1\">Datum podpisu:  ";
	 
	if ( (strlen($data["datum_podpisu"]) > 0) )
	{
	 list($datum_podpisu_rok,$datum_podpisu_mesic,$datum_podpisu_den) = explode("-",$data["datum_podpisu"]);	 
	  $datum_podpisu=$datum_podpisu_den.".".$datum_podpisu_mesic.".".$datum_podpisu_rok;
	 echo $datum_podpisu;
	}
	  
	 echo "</td>";
	 
	 echo "<td class=\"vlastnici-td-black\" colspan=\"1\">Četnost Fa: ";
	    if( $data["billing_freq"] == 0 )
	    { echo "měsíční"; }
	    elseif( $data["billing_freq"] == 1 )
	    { echo "čtvrtletní"; }
	    else
	    { echo "N/A"; }
	    
	 echo "</td>";
	 
	 echo "<td class=\"vlastnici-td-black\" colspan=\"6\">Fakt. skupina: ";
	 
	 $fakturacni_skupina_id=$data["fakturacni_skupina_id"];
	 
	 $dotaz_fakt_skup=$this->conn_mysql->query("SELECT nazev, typ FROM fakturacni_skupiny WHERE id = '".intval($fakturacni_skupina_id)."' ");
	 $dotaz_fakt_skup_radku=$dotaz_fakt_skup->num_rows;
		 
	 if( ( $dotaz_fakt_skup_radku < 1 ) ){ echo " [žádná fakt. skupina] "; }
	 else
	 { 
	   while( $data_fakt_skup=$dotaz_fakt_skup->fetch_array() )
	   { $nazev_fakt_skup = $data_fakt_skup["nazev"]; $typ_fakt_skup = $data_fakt_skup["typ"]; }  
	 
	 echo " [".$nazev_fakt_skup;
	   if ( $typ_fakt_skup == 2){ echo " (FÚ) "; }
	   else{ echo " (DÚ) "; }
	 echo "] ";
	 
	 }
	  
	 echo " </td>";
	 echo "<td class=\"vlastnici-td-black\" colspan=\"7\">";
	 
	 echo "Smlouva: ";
	 
	   if( $data["typ_smlouvy"] == 0){ echo "[nezvoleno]"; }
	   elseif( $data["typ_smlouvy"] == 1){ echo "[na dobu neurčitou]"; }
	   elseif( $data["typ_smlouvy"] == 2)
	   { 
	    echo "[s min. dobou plnění]"." ( do: ";
	    list($trvani_do_rok,$trvani_do_mesic,$trvani_do_den) = explode("-",$data["trvani_do"]);
	    $trvani_do=$trvani_do_den.".".$trvani_do_mesic.".".$trvani_do_rok;
	    
	    echo $trvani_do." )";    
	   }
	   else{ echo "[nelze zjistit]"; }
	 
	 echo "</td>";
	 echo "</tr>";
	
	 //zde treti radek
	 echo "<tr>\n";
	 echo "<td class=\"vlastnici-td-black\" ><br></td>\n";
	 echo "<td class=\"vlastnici-td-black\" colspan=\"1\">
		<div style=\"float: left; \">Pozastavené fakturace:</div>  ";

	 echo "<div style=\"text-align: right; padding-right: 20px;\">";
	 
	 if( $data["billing_suspend_status"] == 1)
	 { echo "Ano"; }
	 elseif( $data["billing_suspend_status"] == 0)
	 { echo "Ne"; }
	 
	 echo "</div>";
	 echo "</td>";	
	
	 if( $data["billing_suspend_status"] == 1)
	 {
	    //dalsi info o pozast. fakturacich
	    
	    echo "<td class=\"vlastnici-td-black\">od kdy: <span style=\"padding-left: 20px;\">";
		if( (strlen($data["billing_suspend_start"]) > 0) or ($data["billing_suspend_start"] != NULL) )
		{ echo htmlspecialchars($data["billing_suspend_start_f"]); }
		else
		{ echo "není zadáno"; }
		
	    echo "</span></td>";
	    
	    //doba
	    echo "<td class=\"vlastnici-td-black\" colspan=\"3\">do kdy: <span style=\"padding-left: 20px;\">";
	    
	    if( (strlen($data["billing_suspend_stop"]) > 0) or ($data["billing_suspend_stop"] != NULL) )
	    { echo htmlspecialchars($data["billing_suspend_stop_f"]); }
	    else
	    { echo " není zadáno "; }
	 
	    echo "</span></td>";
	    
	    //důvod
	    echo "<td class=\"vlastnici-td-black\" colspan=\"5\">důvod: <span style=\"padding-left: 20px;\">";
	    
	    if( strlen($data["billing_suspend_reason"]) == 0)
	    { echo "není zadáno"; }
	    else
	    { echo htmlspecialchars($data["billing_suspend_reason"]); }
	     
	    echo "</span></td>";
	    
	 } 
	 else
	 {
	    echo "<td class=\"vlastnici-td-black\" colspan=\"9\">&nbsp;</td>";
	 }
	 
	 echo "</tr>";
	  
	 echo " 
		<tr> 
		 <td><br></td>
		 <td colspan=\"3\" >".$data["jmeno"]." ".$data["prijmeni"]."<br>
		 ".$data["ulice"]." ";
		     
	 echo "<a href=\"http://www.mapy.cz?query=".$data["ulice"].",".$data["mesto"]."\" target=\"_blank\" >ukaž na mapě</a>";
	    
	 echo "<br>".$data["mesto"]." ".$data["psc"]."</td>
	 <td colspan=\"6\" >";
		 
	 //druhy sloupec - pomyslny
	 echo "icq: ".$data["icq"]." <br>
	 mail: ".$data["mail"]." <br>
	 tel: ".$data["telefon"]." </td>";
		 
	 //treti sloupec - sluzby
	 echo "<td colspan=\"\" valign=\"top\" >";
		 
	  if( $data["sluzba_int"] == 1 )
	  { 
	    echo "<div style=\"\" ><span style=\"font-weight: bold; \"><span style=\"color: #ff6600; \" >Služba Internet</span> - aktivní </span>";
	    if( $data["sluzba_int_id_tarifu"] == 999 )
	    { echo "<span style=\"color: gray; \" >- tarif nezvolen</span></div>"; }
	    else
	    { echo " (<a href=\"admin-tarify.php?id_tarifu=".$data["sluzba_int_id_tarifu"]."\" >tarif)</a></div>"; }
	    
	    $sluzba_int_aktivni = "1"; 
	  }
	  else
	  { $sluzba_int_aktivni = "0"; }
	  
	  if( $data["sluzba_iptv"] == 1 )
	  { 
	    echo "<div style=\"float: left;\" >".
		    "<span style=\"font-weight: bold; \"><span style=\"color: #00cbfc; \" >Služba IPTV</span> - aktivní </span>";
	    
	    if( $data["sluzba_iptv_id_tarifu"] == 999 )
	    { echo "<span style=\"color: gray; \" >- tarif nezvolen</span></div>"; }
	    else
	    { echo " (<a href=\"admin-tarify-iptv.php?id_tarifu=".$data["sluzba_iptv_id_tarifu"]."\" >tarif)</a></div>"; }
	    
	    $sluzba_iptv_aktivni = "1"; 
	  
	    //link portál
	    $mq_prefix = mysql_query("SELECT value FROM settings WHERE name LIKE 'iptv_portal_sub_code_prefix' ");
	    $iptv_prefix_name = mysql_result($mq_prefix, 0, 0);
	    
	    echo "<div style=\"float: left; padding-left: 15px; \" >";
		echo "<a href=\"http://app01.cho01.iptv.grapesc.cz:9080/admin/admin/provisioning/".
		        "subscriber-search.html?type=SUBSCRIBER_CODE&subscriptionNewState=&subscriptionStbAccountState=".
		    	"&localityId=&offerId=&submit=OK&searchText=".urlencode($iptv_prefix_name.$data["prijmeni"])."\" target=\"_new\" >".
			"<img src=\"/img2/Letter-P-icon-small.png\" alt=\"letter-p-small\" width=\"20px\" >".
		    "</a>";
	    echo "</div>";
	    
	    echo "<div style=\"clear: both; \"></div>";
	  
	  }
	  else
	  { $sluzba_iptv_aktivni = "0"; }
		 
	  if( $data["sluzba_voip"] == 1 )
	  { 
	    echo "<div><span style=\"font-weight: bold;\" ><span style=\"color: #e42222; \" >Služba VoIP</span> - aktivní </span>";
	    
	    /*if( $data["sluzba_iptv_id_tarifu"] == 999 )
	    { echo "<span style=\"color: gray; \" >- tarif nezvolen</span></div>"; }
	    else
	    { echo " (<a href=\"\" >tarif)</a></div>"; }
	    */
	    
	    $sluzba_voip_aktivni = "1"; 
	  }
	  else
	  { $sluzba_voip_aktivni = "0"; }
	  
	  if( ( $sluzba_int_aktivni != 1 ) and ( $sluzba_iptv_aktivni != 1 ) and ( $sluzba_voip_aktivni != 1 ) )
	  { echo "<div style=\"color: Navy; font-weight: bold; \" >Žádná služba není aktivovaná</div>"; }
	  else{}
	  
	  //echo "<hr class=\"cara3\" />";
	  echo "<div style=\"border-bottom: 1px solid gray; width: 220px; \" ></div>";
	  
	  if( ( $sluzba_int_aktivni != 1 ) and ( $sluzba_iptv_aktivni != 1 ) and ( $sluzba_voip_aktivni != 1 ) )
	  { 
	   echo "<div style=\"color: #555555; \" >Všechny služby dostupné</div>"; 
	  }
	  else
	  {
	   if( $sluzba_int_aktivni != 1 )
	   { 
	     echo "<div style=\"\" ><span style=\"color: #ff6600; \" >Služba Internet</span>";
	     echo "<span style=\"color: #555555; \"> - dostupné </span></div>"; 
	   }
	   else{}
	   
	   if( $sluzba_iptv_aktivni != 1 )
	   { 
	     echo "<div style=\"\" ><span style=\"color: #27b0db; \" >Služba IPTV</span>";
	     echo "<span style=\"color: #555555; \"> - dostupné </span></div>"; 
	   }
	   else{}
	   
	   if( $sluzba_voip_aktivni != 1 )
	   { 
	     echo "<div style=\"\" ><span style=\"color: #e42222; \" >Služba VoIP</span>";
	     echo "<span style=\"color: #555555; \"> - dostupné </span></div>"; 
	   }
	   else{}
	  
	  }	 
	  
	  echo "</td>";	 
	 echo "</tr>"; //konec radku
		
	 $id=$data["id_cloveka"];
	 $id_v=$id;
	 
	 $id_f=$data["fakturacni"];
	
    // tady asi bude generovani fakturacnich udaju	
    if( ( $id_f > 0 ) ){
		$fakturacni = new fakturacni;
		$fakturacni->vypis($id_f,$id_v); 
	}
    
    $objekt = new objekt_a2(); 
    $objekt->conn_mysql = $this->conn_mysql;
	
    $pocet_wifi_obj = $objekt->zjistipocet(1,$id);
    
    $pocet_fiber_obj = $objekt->zjistipocet(2,$id);
    
    if( $pocet_wifi_obj > 0 or $pocet_fiber_obj == 0 )
    {
     //objekty wifi
     $co="3";
		
     echo "<tr>
	    <td colspan=\"1\" bgcolor=\"#99FF99\" align=\"center\" >W
	    <td colspan=\"10\" bgcolor=\"#99FF99\" >";
      echo "<table border=\"0\" width=\"100%\" >";
        
      $objekt->vypis($sql,$co,$id);
	    
      echo "</table>";
     echo "</td></tr>";
    }
    
    if( $pocet_fiber_obj > 0 )
    {
    
     //objekty fiber
     $co="4";
		
     echo "<tr>";
     echo "<td colspan=\"1\" bgcolor=\"fbbc86\" align=\"center\" >F</td>";
     echo "<td colspan=\"10\" bgcolor=\"fbbc86\" >";
	   
      echo "<table border=\"0\" width=\"100%\" >";
        
      $objekt->vypis($sql, $co, $id);
	    
      echo "</table>";
     echo "</td></tr>";
    }
    
    //stb
    
    // $stb = new App\Core\stb($this->conn_mysql, $this->logger);
    
    // $stb->level = $this->level;
    
    // $pocet_stb = $stb->zjistipocetobj($id);
    
    // if( $pocet_stb > 0 )
    // {
    //   echo "<tr>";
    //   echo "<td colspan=\"1\" bgcolor=\"#c1feff\" align=\"center\" >S</td>\n";
    //   echo "<td colspan=\"10\" bgcolor=\"#c1feff\" valign=\"center\" >\n";
	   
    //   echo "<table border=\"0\" width=\"100%\" >\n";
        
    //   $stb->vypis("1",$id);
	    
    //   echo "</table>\n";
    //   echo "</td></tr>\n";
    // }
    
    //tady dalsi radka asi
    /*
    $voip = new voip();
    $id_vlastnika = $data["id_cloveka"];
    
    $dotaz_sql = "SELECT * FROM voip_cisla WHERE id_vlastnika = '$id_vlastnika' ";
    $voip_radku = $voip->vypis_cisla_query($dotaz_sql);
   
    if ( $voip_radku > 0)
    {
     echo "<tr>";    
     echo "<td colspan=\"14\" ><div style=\"padding-top: 10px; padding-bottom: 10px; \">";
    
      $voip->vypis_cisla("2");
      
     echo "</div></td>";
     echo "</tr>";
    }
    */
        
    //druha radka			
	    echo "<tr>";
	    echo "<td colspan=\"14\">";
	
	    echo "<table border=\"0\" width=\"100%\" >";
	    echo "<tr>";

	    $orezano = explode(':', $data["pridano"]);
	    $pridano=$orezano[0].":".$orezano[1];
		            
	    echo "<td colspan=\"1\" width=\"250px\" >";
	      echo "<span style=\"margin: 20px; \">datum přidání: ".$pridano." </span>";
	    echo "</td>";
	    
	    echo "<td align=\"center\" >";
		echo " <img title=\"poznamka\" src=\"img2/poznamka3.png\" align=\"middle\" ";
		echo " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." ');\" >";
	    echo "</td>";
	    
	    echo "<td>
		    <span style=\"\">vyberte akci: </span>
		  </td>";
		
	    echo "<td colspan=\"1\">";

	    
	    echo "<form action=\"vlastnici-cross.php\" method=\"get\" >";
			
		      echo "<select name=\"akce\" size=\"1\" >";
		    
		      echo "<option value=\"0\" class=\"select-nevybrano\" >Nevybráno</option>";
		      
		       echo "<optgroup label=\"objekty\">";
		        echo "<option value=\"1\" "; if( $_GET["akce"] == 1 ) echo " selected "; echo " > přiřadit objekt </option>";
		        echo "<option value=\"15\" "; if( $_GET["akce"] == 15 ) echo " selected "; echo " > přiřadit objekt STB</option>";

		       echo "</optgroup>";
		
		       echo "<optgroup label=\"fakturacni adresa\">";	
		        echo "<option value=\"2\" "; if( $_GET["akce"] == 2) echo " selected "; echo " >přidání fakturační adresy </option>";
		        echo "<option value=\"3\" "; if( $_GET["akce"] == 3) echo " selected "; echo " >smazání fakturační adresy </option>";
		        echo "<option value=\"4\" "; if( $_GET["akce"] == 4) echo " selected "; echo " >úprava fakturační adresy </option>";
		       echo "</optgroup>";
			
		       echo "<optgroup label=\"Závady/opravy\" >";
			echo "<option value=\"5\" "; if( $_GET["akce"] == 5) echo " selected "; echo " >Vložit závadu/opravu</option>";
			echo "<option value=\"6\" "; if( $_GET["akce"] == 6) echo " selected "; echo " >zobrazit závady/opravy</option>";
		       echo "</optgroup>";
		    
		       echo "<optgroup label=\"Smlouvy/výpovědi\" >";
			echo "<option value=\"7\" "; if( $_GET["akce"] == 7) echo " selected "; echo " >Tisk smlouvy</option>";
			echo "<option value=\"8\" "; if( $_GET["akce"] == 8) echo " selected "; echo " >Vložit zádost o výpověď</option>";
		       echo "</optgroup>";
		    
		       echo "<optgroup label=\"Platby/faktury\" >";
		//	echo "<option value=\"9\" "; if( $_GET["akce"] == 9) echo " selected "; echo " >Vložit hotovostní platbu</option>";
			echo "<option value=\"10\" "; if( $_GET["akce"] == 10) echo " selected "; echo " >Výpis plateb za internet</option>";
			echo "<option value=\"11\" "; if( $_GET["akce"] == 11) echo " selected "; echo " >Výpis všech neuhrazených faktur</option>";
		//	echo "<option value=\"12\" "; if( $_GET["akce"] == 12) echo " selected "; echo " >online faktury (XML) - Internet</option>";
		//	echo "<option value=\"14\" "; if( $_GET["akce"] == 14) echo " selected "; echo " >online faktury (XML) - VoIP (hlas)</option>";        
			echo "<option value=\"16\" "; if( $_GET["akce"] == 16) echo " selected "; echo " >Výpis faktur/Plateb (Pohoda SQL)</option>";        
		
		       echo "</optgroup>";
		    
		       echo "<optgroup label=\"Historie\" >";
			echo "<option value=\"13\" "; if( $_GET["akce"] == 13) echo " selected "; echo " >Zobrazení historie</option>";
		       echo "</optgroup>";
		    
		      echo "</select>";
		      
		      echo "<span style=\"padding-left: 20px;\" >
		    	      <input type=\"submit\" name=\"odeslat\" value=\"OK\">
			    </span>";
		      
		      echo "<input type=\"hidden\" name=\"id_cloveka\" value=\"".$data["id_cloveka"]."\">";
		      
		    echo "</form>";

	    echo "</td>";
	 echo "</tr></table>";    
	
	    echo "</td>";	    
	    echo "</tr>";
	
	/*
	    echo "<tr>";
		echo "<td colspan=\"10\" >";
		
		    	
		echo "</td>";
	    echo "</tr>";
	*/
	
	//konec while
	}
	
	// konec else
	}
	
	// konec funkce vypis
	}
    
    function export(){    
    

	// tafy generovani exportu
	if( $this->export_povolen )
	{

    	    $fp=fopen("export/vlastnici-sro.xls","w");   // Otevřeme soubor tabulka.xls, pokud existuje, bude smazán, jinak se vytvoří nový sobor

    	    if( $fp === false)
    	    { echo "<div style=\"color: red; font-weight: bold; \">Chyba: Soubor pro export nelze otevřít </div>\n"; }
    	    else
    	    {
    		fputs($fp,"<table border='1'> \n \n");   // Zapíšeme do souboru začátek tabulky

    		fputs($fp,"<tr>");   // Zapíšeme do souboru začátek řádky, kde budou názvy sloupců (polí)

    		$vysledek_pole=pg_query("SELECT column_name FROM information_schema.columns WHERE table_name ='vlastnici' ORDER BY ordinal_position ");
    		// Vybereme z databáze názvy polí tabulky tabulka a postupně je zapíšeme do souboru

    		// echo "vysledek_pole: $vysledek_pole ";

    		while ($vysledek_array_pole=pg_fetch_row($vysledek_pole) )
    		{  fputs($fp,"<td><b> ".$vysledek_array_pole[0]." </b></td> \n"); }

    		fputs($fp,"<td><b> id_f </b></td> \n");
    		fputs($fp,"<td><b> f. jméno </b></td> \n");
    		fputs($fp,"<td><b> f. ulice </b></td> \n");
    		fputs($fp,"<td><b> f. mesto </b></td> \n");
    		fputs($fp,"<td><b> f. PSČ </b></td> \n");
    		fputs($fp,"<td><b> f. ičo </b></td> \n");
    		fputs($fp,"<td><b> f. dič </b></td> \n");
    		fputs($fp,"<td><b> f. účet </b></td> \n");
    		fputs($fp,"<td><b> f. splatnost </b></td> \n");
    		fputs($fp,"<td><b> f. cetnost </b></td> \n");

    		fputs($fp,"</tr>");   // Zapíšeme do souboru konec řádky, kde jsou názvy sloupců (polí)

    		// $vysledek=pg_query("select * from platby where hotove='1' ");
    		// Vybereme z databáze všechny záznamy v tabulce tabulka a postupě je zapíšeme do souboru

    		$vysledek = pg_query("SELECT * FROM vlastnici WHERE (archiv ='0' OR archiv is NULL) ORDER BY id_cloveka ASC");

    		while ($data=pg_fetch_array($vysledek) )
    		{
        	    fputs($fp,"\n <tr>");

        	    fputs($fp,"<td> ".$data["id_cloveka"]."</td> ");
        	    fputs($fp,"<td> ".$data["nick"]."</td> ");
        	    fputs($fp,"<td> ".$data["jmeno"]."</td> ");
        	    fputs($fp,"<td> ".$data["prijmeni"]."</td> ");
        	    fputs($fp,"<td> ".$data["ulice"]."</td> ");
        	    fputs($fp,"<td> ".$data["mesto"]."</td> ");
        	    fputs($fp,"<td> ".$data["psc"]."</td> ");
        	    fputs($fp,"<td> ".$data["icq"]."</td> ");
        	    fputs($fp,"<td> ".$data["mail"]."</td> ");
        	    fputs($fp,"<td> ".$data["telefon"]."</td> ");
        	    fputs($fp,"<td> ".$data["poznamka"]."</td> ");
        	    fputs($fp,"<td> ".$data["zaplaceno"]."</td> ");
        	    fputs($fp,"<td> ".$data["fakturacni"]."</td> ");
        	    fputs($fp,"<td> ".$data["vs"]."</td> ");
            	    fputs($fp,"<td> ".$data["k_platbe"]."</td> ");
        	    fputs($fp,"<td> ".$data["firma"]."</td> ");

        	    fputs($fp,"<td> ".$data["pridano"]."</td> ");
        	    fputs($fp,"<td> ".$data["ucetni_index"]."</td> ");
        	    fputs($fp,"<td> ".$data["archiv"]."</td> ");
        	    fputs($fp,"<td> ".$data["fakturacni_skupina_id"]."</td> ");

        	    fputs($fp,"<td> ".$data["splatnost"]."</td> ");
        	    fputs($fp,"<td> ".$data["typ_smlouvy"]."</td> ");
        	    fputs($fp,"<td> ".$data["trvani_do"]."</td> ");
        	    fputs($fp,"<td> ".$data["datum_podpisu"]."</td> ");

        	    fputs($fp,"<td> ".$data["sluzba_int"]."</td> ");
        	    fputs($fp,"<td> ".$data["sluzba_iptv"]."</td> ");

        	    fputs($fp,"<td> ".$data["sluzba_voip"]."</td> ");
        	    fputs($fp,"<td> ".$data["sluzba_int_id_tarifu"]."</td> ");
        	    fputs($fp,"<td> ".$data["sluzba_iptv_id_tarifu"]."</td> ");
        	    fputs($fp,"<td> ".$data["sluzba_voip_fa"]."</td> ");

        	    fputs($fp,"<td> ".$data["billing_freq"]."</td> ");

        	    fputs($fp,"<td> ".$data["billing_suspend_status"]."</td> ");
        	    fputs($fp,"<td> ".$data["billing_suspend_length"]."</td> ");
        	    fputs($fp,"<td> ".$data["billing_suspend_reason"]."</td> ");
        	    fputs($fp,"<td> ".$data["billing_suspend_start"]."</td> ");

        	    if ( $data["fakturacni"] > 0 )
        	    {
        		$id_f=$data["fakturacni"];

        		$vysl_f=pg_query("SELECT * FROM fakturacni WHERE id = '".intval($id_f)."' ");

        		while ( $data_f=pg_fetch_array($vysl_f) )
        		{

        		    fputs($fp,"<td> ".$data_f["id"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["ftitle"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["fulice"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["fmesto"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["fpsc"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["ico"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["dic"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["ucet"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["splatnost"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["cetnost"]."</td> ");

        		}

        	    }

        	    fputs($fp,"</tr> \n ");
        	    // echo "vysledek_array: ".$vysledek_array[$i];

    		} //konec while

    		fputs($fp,"</table>");   // Zapíšeme do souboru konec tabulky

    		fclose($fp);   // Zavřeme soubor

    	    } //konec else if fp === true

	} //konec if export_povolen
    
    } //end of function export
    	
 //konec class-y vlastnik2
}

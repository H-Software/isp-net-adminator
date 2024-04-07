<?php

// set_time_limit(0);

require_once ("include/config.php"); 
require_once ("include/check_login.php");

require_once ("include/check_level.php");

if ( !( check_level($level,30) ) )
{
 // neni level
 header("Location: nolevelpage.php");
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
 
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require ("include/charset.php"); 

?>

<title>Adminator 2 :: Změny :: Archiv změn </title> 

</head> 

<body> 

<?php require ("head.php"); ?> 

<?php require ("category.php"); ?> 

<tr>
  <td colspan="2"><?php require ("archiv-zmen-subcat.php"); ?></td>
</tr>
  
<tr>
  <td colspan="2">
  
 <?php
 
    $pocet = intval($_GET["pocet"]);

    if( strlen($_GET["id"]) > 0 ) {
	$id = intval($_GET["id"]);
    }
    
    if( strlen($_GET["id_nodu"]) > 0 ) {
	$id_nodu = intval($_GET["id_nodu"]);
    }

    if( strlen($_GET["id_stb"]) > 0 ) {
	$id_stb = intval($_GET["id_stb"]);
    }
    
    if( strlen($_GET["id_cloveka"]) > 0 ) {
	$id_cloveka = intval($_GET["id_cloveka"]);
    }
    
    if( strlen($_GET["id_routeru"]) > 0 ) {
	$id_routeru = intval($_GET["id_routeru"]);
    }
    
    $typ = intval($_GET["typ"]);

    $id_objektu = $id;
  
    echo "<div style=\"padding-left: 5px; padding-top: 10px; \">\n";
    
    echo "<div style=\" padding-bottom: 10px; padding-right: 40px; font-size: 18px; font-weight: bold; float: left; \" >\n";
    echo " Archiv změn</div>\n";
 
    echo "<div style=\" \" ><form method=\"GET\" action=\"\" >\n";
    
    echo "<span style=\"margin-right: 20px; \" ><label>Vyberte počet záznamů: </label></span>
	
	  <select name=\"pocet\" size=\"1\" >
	    <option value=\"50\" "; if ($pocet == "50" or !isset($pocet) ){ echo " selected "; } echo " >50</option>
	    <option value=\"100\" "; if( $pocet == "100" ){ echo " selected "; } echo " >100</option>
	    <option value=\"150\""; if( $pocet == "150" ){ echo " selected "; } echo " >150</option>
	    
	    <option value=\"1000\""; if( $pocet == "500" ){ echo " selected "; } echo " >500</option>
	    
	  </select>\n";
    
    if( !isset($id_nodu) and !isset($id) and !isset($id_stb) and !isset($id_cloveka) and !isset($id_routeru) )
    {
    
    echo "<span style=\"margin-right: 20px; margin-left: 20px; \">Typ záznamů:</span>\n";

    echo "<select name=\"typ\" size=\"1\" >
	    <option value=\"0\" "; if ($typ == "0" or !isset($typ) ){ echo " selected "; } echo " >Vše</option>
	
	    <optgroup label=\"objekty\" >
	
	    <option value=\"1\" "; if( $typ == "1" ){ echo " selected "; } echo " >akce: úprava objektu</option>
	    <option value=\"2\""; if( $typ == "2" ){ echo " selected "; } echo " >akce: přidání objektu</option>
	    <option value=\"3\""; if( $typ == "3" ){ echo " selected "; } echo " >akce: smazáni objektu</option>
	
	    <option value=\"4\""; if( $typ == "4" ){ echo " selected "; } echo " >akce: úprava stb objektu</option>
	    <option value=\"5\""; if( $typ == "5" ){ echo " selected "; } echo " >akce: přidání stb objektu</option>
	    <option value=\"6\""; if( $typ == "6" ){ echo " selected "; } echo " >akce: smazaní stb objektu</option>
		
	    <optgroup label=\"vlastníci\" >
	
	    <option value=\"7\""; if( $typ == "7" ){ echo " selected "; } echo " >akce: úprava vlastníka</option>
	    <option value=\"8\""; if( $typ == "8" ){ echo " selected "; } echo " >akce: přidáni vlastníka</option>
	
	    <option value=\"9\""; if( $typ == "9" ){ echo " selected "; } echo " >akce: přidáni fakturační adresy</option>
	    <option value=\"10\""; if( $typ == "10" ){ echo " selected "; } echo " >akce: úprava fakturační adresy</option>
	    <option value=\"11\""; if( $typ == "11" ){ echo " selected "; } echo " >akce: smazání fakturační adresy</option>
	
	<!-- akce: poslani emailu z duvodu neplaceni -->
	
	<!-- akce: poslani SMS z duvodu neplacení -->
	
	    <optgroup label=\"obojí (objekty i vlastníci)\" >
	
	    <option value=\"12\""; if( $typ == "12" ){ echo " selected "; } echo " >akce: přiřazení objektu k vlastníkovi</option>
	    <option value=\"13\""; if( $typ == "13" ){ echo " selected "; } echo " >akce: prirazeni objektu typu STB k vlastnikovi</option>

	    <option value=\"14\""; if( $typ == "14" ){ echo " selected "; } echo " >akce: odrazeni objektu (od vlastníka)</option>
	    <option value=\"15\""; if( $typ == "15" ){ echo " selected "; } echo " >akce: odparovani stb objektu (od vlastníka)</option>

	    <option value=\"25\""; if( $typ == "25" ){ echo " selected "; } echo " >akce: zakazani netu z duvodu sikany</option>

	<!-- akce: automaticke nastaveni sikany z duvodu neuhr. faktur -->
	
	    <optgroup label=\"topologie - routery \" >

	    <option value=\"17\""; if( $typ == "17" ){ echo " selected "; } echo " >akce: přidání routeru</option>
	    <option value=\"18\""; if( $typ == "18" ){ echo " selected "; } echo " >akce: úprava routeru</option>
	    <option value=\"19\""; if( $typ == "19" ){ echo " selected "; } echo " >akce: smazání routeru</option>

	    <optgroup label=\"topologie - nody/lokality \" >

	    <option value=\"20\""; if( $typ == "20" ){ echo " selected "; } echo " >akce: přidání nodu</option>
	    <option value=\"21\""; if( $typ == "21" ){ echo " selected "; } echo " >akce: úprava nodu</option>
	    <option value=\"22\""; if( $typ == "22" ){ echo " selected "; } echo " >akce: smazání nodu</option>

	    <optgroup label=\"monitoring - grafy \" >

	    <option value=\"23\""; if( $typ == "23" ){ echo " selected "; } echo " >akce: přidání/změna grafu</option>
	    <option value=\"24\""; if( $typ == "24" ){ echo " selected "; } echo " >akce: smazání grafu</option>

	    <optgroup label=\"ostatní (prostě zbytek)\" >

	    <option value=\"16\""; if( $typ == "16" ){ echo " selected "; } echo " >akce: požadavek na restart</option>
	    	
<!--	    	
	    <option value=\"\""; if( $typ == "" ){ echo " selected "; } echo " >akce: pridani hotovostni platby</option>

	     akce: pridani opravy
	     
	     akce: voip - pridani klienta (customer)
-->
	    
	  </select>\n";
    
    }
    
    echo "<span style=\"margin-left: 10px; \"><input type=\"submit\" name=\"odeslano\" value=\"OK\" ></span>
    
    <span style=\"margin-left: 40px; \"><a href=\"include\export-archiv-zmen.php\">export dat zde</a></span>
    
    </form> \n\n";
 
    echo "</div>\n"; //konec hlavni divu
    
    $zaklad_sql = "select *,DATE_FORMAT(provedeno_kdy, '%d.%m.%Y %H:%i:%s') as provedeno_kdy2 from archiv_zmen ";
    
    if($typ > 0)
    {   
	
	if($typ == 0){
	    $type_select = "";
	}
	elseif($typ == 1){
	    $type_select = "WHERE (akce LIKE '<b>akce: uprava objektu; </b>%') ";
	}
	elseif($typ == 2){
	    $type_select = "WHERE (akce LIKE '<b> akce: pridani objektu ; </b>%') ";
	}
	elseif($typ == 3){
	    $type_select = "WHERE (akce LIKE '<b>akce: smazani objektu;</b>%') ";
	}
	elseif($typ == 4){
	    $type_select = "WHERE (akce LIKE '<b>akce: uprava stb objektu; </b>%') ";
	}
	elseif($typ == 5){
	    $type_select = "WHERE (akce like '<b> akce: pridani stb objektu ; </b>%') ";
	}
	elseif($typ == 6){
	    $type_select = "WHERE (akce LIKE '<b> akce: smazani stb objektu ; </b>%') ";
	}
	elseif($typ == 7){
	    $type_select = "WHERE (akce LIKE '<b>akce: uprava vlastnika; </b>%') ";
	}
	elseif($typ == 8){
	    $type_select = "WHERE (akce LIKE '<b>akce: pridani vlastnika ; </b>%') ";
	}
	elseif($typ == 9){
	    $type_select = "WHERE (akce LIKE '<b>akce: pridani fakturacni adresy;</b>%') ";
	}
	elseif($typ == 10){
	    $type_select = "WHERE ( ".
				"akce LIKE '<b>akce</b>: uprava fakturacni adresy%' ".
				" OR ".
				"akce LIKE ' akce: uprava fakturacni adresy%' ".
				")";
	}
	elseif($typ == 11){
	    $type_select = "WHERE ( ".
				"akce LIKE ' akce: smazani fakturacni adresy ;%' ".
				")";
	}
	elseif($typ == 12){
	    $type_select = "WHERE ( ".
				"akce LIKE ' prirazeni objektu%' ".
				" OR ".
				"akce LIKE '<b>akce: prirazeni objektu k vlastnikovi; </b>%' ".
			    ")";
	}
	elseif($typ == 13){
	    $type_select = "WHERE akce LIKE '<b>akce: prirazeni objektu typu STB k vlastnikovi; </b>%' ";
	}
	elseif($typ == 14){
	    $type_select = "WHERE ( ".
				    "akce LIKE ' odrazeni objektu%' ".
				    " OR ".
				    "akce LIKE '<b>akce: odrazeni objektu; </b>%' ".
				")";
	}
	elseif($typ == 15){
	    $type_select = "WHERE (akce LIKE '<b> akce: odparovani stb objektu ; </b>%') ";
	}
	elseif($typ == 16){
	    $type_select = "WHERE ( ".
				    "akce LIKE '<b>akce: požadavek na restart;</b>%' ".
				    " OR ".
				    "akce LIKE '<b>akce:</b> požadavek na restart;<br>%' ".
				" ) ";
	}
	elseif($typ == 17){
	    $type_select = "WHERE ( ".
				    "akce LIKE '<b>akce: pridani routeru;</b>%' ".
				    " OR ".
				    "akce LIKE ' akce: pridani routeru ;%' ".
				" ) ";
	}
	elseif($typ == 18){
	    $type_select = "WHERE ( ".
				    "akce LIKE ' akce: uprava routeru ;%' ".
				    " OR ".
				    "akce LIKE '<b>akce: uprava routeru;</b>%' ".
				" ) ";
	}
	elseif($typ == 19){
	    $type_select = "WHERE akce LIKE '<b>akce: smazání routeru;</b>%' ";
	}
	elseif($typ == 20){
	    $type_select = "WHERE akce LIKE '<b>akce: pridani nodu ; </b>%' ";
	}
	elseif($typ == 21){
	    $type_select = "WHERE akce LIKE '<b>akce: uprava nodu;</b>%' ";
	}
	elseif($typ == 22){
	    $type_select = "WHERE akce LIKE '<b>akce: smazání lokality / nodu; </b>%' ";
	}
	elseif($typ == 23){
	    $type_select = "WHERE ( ".
				    "akce LIKE ' pridani/zmena  grafu%' ".
				    " OR ".
				    "akce LIKE '<b>akce: pridani/zmena  grafu;</b>%' ".
				" ) ";
	}
	elseif($typ == 24){
	    $type_select = "WHERE ( ".
				    "akce LIKE ' akce: smazani grafu ;%'".
				    " OR ".
				    "akce LIKE '<b>akce: smazani grafu;</b>%' ".
				" ) ";
	}
	elseif($typ == 25){
	    $type_select = "WHERE ( ".
				    "akce LIKE 'akce: zakazani netu z duvodu sikany %' ".
				    " OR ".
				    "akce LIKE '<b>akce: zakazani netu z duvodu sikany;</b>%' ".
				" ) ";
	}
	
        $sql_result = $zaklad_sql." ".$type_select." ORDER BY id DESC "; 
	
    }
    elseif( $id > 0 )
    { 
	$sql_result = $zaklad_sql." WHERE ( ".
					" akce LIKE '%[id_komplu]=> ".$id."%' ".
//					" OR ".
//					" akce LIKE '%[id_komplu]=> ".$id." ,%' ".
					" ) ORDER BY id DESC ";
    }
    elseif($id_cloveka > 0){
    
     $id_cloveka_sql = " where ( ( akce LIKE '%[id_cloveka]=> ".$id_cloveka." ,%' AND akce NOT LIKE '%[id_komplu]%' ) ";
     $id_cloveka_sql .= " OR ( akce LIKE '%[id_cloveka] => ".$id_cloveka." ,%' ) OR ( akce LIKE ";
     $id_cloveka_sql .= " '%[id_vlastnika] => ".$id_cloveka."%' ) OR ".
    			" ( (akce LIKE '%[id_vlastnika]=> ".$id_cloveka."%') AND (akce LIKE '%prirazeni objektu k vlastnikovi%') )".
    			" OR (akce LIKE '%[id_cloveka] => ".$id_cloveka."%') ) ";
     
     $sql_result = $zaklad_sql.$id_cloveka_sql." ORDER BY id DESC ";
    
    }
    elseif($id_stb > 0){
	
	$sql_stb = " WHERE ( ".
	
		    " ( (akce LIKE '%uprava stb objektu%') AND ( akce LIKE '%[id_stb]=> ".$id_stb.",%') ) ".
		    " OR ".
		    " ( ( akce LIKE '%typu STB%') AND ( akce LIKE '%[id_stb]=> ".$id_stb.",%') ) ".
		    " OR ".
		    " ( (akce LIKE '%odparovani stb objektu%') AND ( akce LIKE '%<b>[id_stb]</b> => ".$id_stb." %' ) ) ".
		    " OR ".
		    " ( (akce LIKE '%pridani stb objektu%') AND ( akce LIKE '%[id_stb]=> ".$id_stb.",%' ) ) ".
		    ") ORDER BY id DESC ";
	
        $sql_result = $zaklad_sql.$sql_stb;
    }
    elseif($id_nodu > 0) {

	$idnodu_select = " WHERE ( ".
			    " akce LIKE '% uprava nodu;%[id_nodu] => ".$id_nodu." %' ".
//			    " OR ".
//			    " akce LIKE '' ".
			 " ) ORDER BY id DESC ";

        $sql_result = $zaklad_sql.$idnodu_select;
    
    }
    elseif($id_routeru > 0){

	$idrouteru_select = " WHERE ( ".
			    "akce LIKE '<b>akce: uprava routeru;</b><br> [id_routeru] => <a href=\"topology-router-list.php\">".$id_routeru."</a>%' ".

//			    " OR ".
//			    " akce LIKE '' ".
			 " ) ORDER BY id DESC ";

        $sql_result = $zaklad_sql.$idrouteru_select;
        
    }
    else
    { 
	$sql_result = $zaklad_sql." order by id DESC "; 
    }
    
    if($pocet > 0){
	$sql_result = $sql_result." LIMIT ".$pocet;
    }
    else{
    	$sql_result = $sql_result." LIMIT 50";
    }
    
    $vysl = mysql_query($sql_result);
    
    if (!$vysl) {
        echo "<div style=\"color: red;\" >Chyba při provádění databázového dotazu (id: " . mysql_error() . ") </div>";

	echo "\n</td>\n</tr>\n</table>\n</body>\n</html>\n";
        exit;
    }
               
    $radku=mysql_num_rows($vysl);

    //ted zjistime jeslti je archiv 
    if( isset($id) )
    {
    echo "<div style=\"padding-left: 5px; \">";
    
    echo "<div style=\"padding-top: 10px; padding-bottom: 10px; font-weight: bold; font-size: 18px; \">";
    echo "Historie objektu: </div>";
    
    
    $dotaz_objekty=pg_query("SELECT dns_jmeno, ip, mac FROM objekty WHERE id_komplu = '".intval($id_objektu)."' ");
    
     if( (pg_num_rows($dotaz_objekty) == 1) )
     {
       while( $data_objekty=pg_fetch_array($dotaz_objekty) )
       {
	echo "<div >dns jméno: <span style=\"color: grey;\">".$data_objekty["dns_jmeno"]."</span></div>";
	echo "<div >ip adresa: <span style=\"color: grey;\">".$data_objekty["ip"]."</span></div>";
	echo "<div >mac adresa: <span style=\"color: grey;\">".$data_objekty["mac"]."</span></div>";
        
	$id_vlastnika=$data_objekty["id_cloveka"];          
       }
    
      $dotaz_vlastnik = pg_query("SELECT archiv, firma FROM vlastnici WHERE id_cloveka = '".intval($id_vlastnika)."' ");
      while($data_vlastnik = pg_fetch_array($dotaz_vlastnik) )
      {
       $firma_vlastnik=$data_vlastnik["firma"];
       $archiv_vlastnik=$data_vlastnik["archiv"];

	echo "<div style=\"padding-top: 5px; \" >Detail vlastníka: ";
	
       if($archiv_vlastnik == 1)
       { echo "<a href=\"vlastnici-archiv.php?find_id=".$data_vlastnik["id_cloveka"]."\" >".$data_vlastnik["id_cloveka"]."</a> \n"; }
       else //if( $firma_vlastnik == 1 )
       { echo "<a href=\"vlastnici2.php?find_id=".$data_vlastnik["id_cloveka"]."\" >".$data_vlastnik["id_cloveka"]."</a> \n"; }
       //else
       //{ echo "<a href=\"vlastnici.php?find_id=".$data_vlastnik["id_cloveka"]."\" >".$data_vlastnik["id_cloveka"]."</a> \n"; }

        echo "</div>";
       }

				      
       echo "<div style=\"padding-bottom: 20px; \"></div>";
     } // konec if pg_num_rows
    
     echo "</div>\n";
    }//konec if isset id
    
   if ( $radku==0 ){ echo "Žádné změny v archivu "; }
   else  
   {
      echo "<table width=\"100%\" border=\"0\" cellpadding=\"5\" class=\"az-main-table\" >\n";
	    
	echo "<tr >\n";    
	    echo "<td class=\"az-border2\" ><b>id:</b></td>\n";
	    echo "<td class=\"az-border2\" ><b>akce:</b></td>\n";
	    echo "<td class=\"az-border2\" ><b>Provedeno kdy:</b></td>\n";
	    echo "<td class=\"az-border2\" ><b>Provedeno kým:</b></td>\n";
	    echo "<td class=\"az-border2\" ><b>Provedeno úspěšně:</b></td>\n";
	echo "</tr>\n";
	    
        while ($data=mysql_fetch_array($vysl) ):
	    
	 echo "<tr>\n";    
           echo "<td class=\"az-border1\" >".$data["id"]."</td>\n";
	   echo "<td class=\"az-border1\" ><span class=\"az-text\" >\n";
	   
	   $id_cloveka_res = "";  
	   $akce = $data["akce"];

	   if(ereg("odrazeni objektu", $akce) == true){
	
	    $pomocne = explode("[id_komplu]", $akce);    
	    $pomocne2 = explode(" ", $pomocne[1] );	    
	    $pomocne3 = explode("<br>", $pomocne2[1] );	    
	    $id_komplu_pomocne = trim($pomocne3[0]);
	    
	    $dotaz_id_komplu = pg_query("SELECT dns_jmeno FROM objekty WHERE id_komplu = '".intval($id_komplu_pomocne)."' ");
      	    
	    while($data_kompl = pg_fetch_array($dotaz_id_komplu) )
            { $data_kompl_dns = $data_kompl["dns_jmeno"]; }
	    
	    $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$data_kompl_dns;
	    $id_komplu_pomocne_rs .= "\" >".$id_komplu_pomocne."</a>";
              
	    $akce = ereg_replace("".$id_komplu_pomocne."", "".$id_komplu_pomocne_rs."", $akce);    
	   
	   }
	
	   if( ereg("id_stb",$akce) == true ){
	   
	     $pm = preg_match("/<b>\[id_stb\]<\/b>/",$akce);
	     
	     if( ($pm == 1) ){    
	        $stb_string = "<b>[id_stb]</b> =>";
	     } 
	     else{
	        $stb_string = "[id_stb]=>";    
	     }
	         
	     $pom = explode($stb_string, $akce);    
	     $pom2 = explode(" ", $pom[1]);
	     
	     //$id_stb = $pom2[1];
	     $id_stb = ereg_replace(",", "", $pom2["1"]);
	     $id_stb = trim($id_stb);
	     
	    // if( !($id_stb > 0) )
	    //    $id_stb = trim($pom2[2]);
	     
	     $id_stb_pom_rs = "<a href=\"objekty-stb.php?id_stb=".$id_stb."\" >".$id_stb."</a>";
	     
	     $akce = ereg_replace(" ".$id_stb," ".$id_stb_pom_rs, $akce);    
	        	
	   } 
	   
	   if( ereg("prirazeni objektu k vlastnikovi", $akce) == true )
	   {
	    $pomocne = explode(" ", $akce);
	    $id_komplu_pomocne = ereg_replace(",", "", $pomocne[7]);
	    
	    $id_cloveka_pomocne = $pomocne[9];
	    
	    if( !($id_cloveka_pomocne > 0) ){
		$id_cloveka_pomocne = $pomocne[10];
	    }
	    
	    $dotaz_id_komplu=pg_query("SELECT * FROM objekty WHERE id_komplu = '".intval($id_komplu_pomocne)."'");
	     while($data_kompl = pg_fetch_array($dotaz_id_komplu) )
             { $data_kompl_dns = $data_kompl["dns_jmeno"]; }
	    $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$data_kompl_dns;
	     $id_komplu_pomocne_rs .= "\" >".$id_komplu_pomocne."</a>";
              
	    $akce = ereg_replace($id_komplu_pomocne, $id_komplu_pomocne_rs, $akce);    
	   
	    $dotaz_vlastnik_pom = pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka_pomocne)."' ");
             while($data_vlastnik_pom = pg_fetch_array($dotaz_vlastnik_pom) )
             { $firma_vlastnik=$data_vlastnik_pom["firma"]; $archiv_vlastnik=$data_vlastnik_pom["archiv"]; }
    	    if( $archiv_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"vlastnici-archiv.php"; }
    	    elseif( $firma_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"vlastnici2.php"; }
    	    else{ $id_cloveka_res .= "<a href=\"vlastnici.php"; }

	    $id_cloveka_res .= "?find_id=".$id_cloveka_pomocne."\" >".$id_cloveka_pomocne."</a>";
	
	    $akce = ereg_replace($id_cloveka_pomocne, $id_cloveka_res, $akce);    
	     
	   }
	   elseif( ereg("smazani objektu", $akce) == true )
	   {
	    //nic no, ale musi to tu bejt, jinak se vyhodnocujou blbe ty porovnani dole	    
	   }
	   elseif( ereg("pridani objektu do \"nove\" garant. tridy", $akce) == true )
	   {
	    //nic no, ale musi to tu bejt, jinak se vyhodnocujou blbe ty porovnani dole	    
	      
	   }
	   /*
	   elseif( ereg('pridani objektu', $akce) == true )
	   {
	    $pomocne = explode(" ", $akce);    
	
	    //echo "i".$pomocne[8]."/i";
	    
	    $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$pomocne[8];
	    $id_komplu_pomocne_rs .= "\" >".$pomocne[8]."</a>";
              
	    $akce = ereg_replace($pomocne[8], $id_komplu_pomocne_rs, $akce);    
	     
	   }
	   */
	   elseif( ereg("\[id_vlastnika\]", $akce) == true )
	   {
	    $pomocne = explode("[id_vlastnika]", $akce);    
	    $pomocne2 = explode(" ", $pomocne[1] );
	    $id_cloveka_pomocne = trim($pomocne2[2]);
	    
	    if( !( $id_cloveka_pomocne > 0 ) )
	    { $id_cloveka_pomocne = $pomocne2[1]; }
	    
	     $dotaz_vlastnik_pom = pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka_pomocne)."' ");
      	    
             while($data_vlastnik_pom = pg_fetch_array($dotaz_vlastnik_pom) )
             { $firma_vlastnik=$data_vlastnik_pom["firma"]; $archiv_vlastnik=$data_vlastnik_pom["archiv"]; }

    	     if( $archiv_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"vlastnici-archiv.php"; }
    	     elseif( $firma_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"vlastnici2.php"; }
    	     else{ $id_cloveka_res .= "<a href=\"vlastnici.php"; }

	    $id_cloveka_res .= "?find_id=".$id_cloveka_pomocne."\" >".$id_cloveka_pomocne."</a>";
	        
	    $akce = ereg_replace($id_cloveka_pomocne, $id_cloveka_res, $akce);
	       
	   }
	   elseif( ereg("\[id_cloveka\]", $akce) == true )
	   {
	    $pomocne = explode("[id_cloveka]", $akce);    
	    $pomocne2 = explode(" ", $pomocne[1] );
	    $id_cloveka_pomocne = trim($pomocne2[2]);
	    
	    if( !( $id_cloveka_pomocne > 0 ) )
	    { $id_cloveka_pomocne = $pomocne2[1]; }
	    
	     $dotaz_vlastnik_pom = pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka_pomocne)."' ");
      	    
             while($data_vlastnik_pom = pg_fetch_array($dotaz_vlastnik_pom) )
             { $firma_vlastnik=$data_vlastnik_pom["firma"]; $archiv_vlastnik=$data_vlastnik_pom["archiv"]; }

    	     if( $archiv_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"vlastnici-archiv.php"; }
    	     elseif( $firma_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"vlastnici2.php"; }
    	     else{ $id_cloveka_res .= "<a href=\"vlastnici.php"; }

	    $id_cloveka_res .= "?find_id=".$id_cloveka_pomocne."\" >".$id_cloveka_pomocne."</a>";
	        
	    $akce = ereg_replace($id_cloveka_pomocne, $id_cloveka_res, $akce);

	   }
	   elseif( (ereg("uprava objektu", $akce) == true) )
	   {
	   
	     $pomocne = explode("[id_komplu]", $akce);    
	     $pomocne2 = explode(" ", $pomocne[1] );	    
	     $id_komplu_pomocne = ereg_replace(",", "", $pomocne2[1]);
	    
	     $dotaz_id_komplu = pg_query("SELECT * FROM objekty WHERE id_komplu = '".intval($id_komplu_pomocne)."' ");
      	    
	    while($data_kompl = pg_fetch_array($dotaz_id_komplu) )
            { $data_kompl_dns = $data_kompl["dns_jmeno"]; }
	    
	    $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$data_kompl_dns;
	    $id_komplu_pomocne_rs .= "\" >".$id_komplu_pomocne."</a>";
              
	    $akce = ereg_replace("".$id_komplu_pomocne."", "".$id_komplu_pomocne_rs."", $akce);    
	   }
	   elseif( ereg("zakazani netu z duvodu sikany", $akce) == true )
	   {
	     $pomocne = explode("[id_komplu]", $akce);    
	     $pomocne2 = explode(" ", $pomocne[1] );	    
	     $id_komplu_pomocne = ereg_replace(",", "", $pomocne2[1]);
	
	     if( is_numeric($id_komplu_pomocne) )
	     {
	        $dotaz_id_komplu = pg_query("SELECT * FROM objekty WHERE id_komplu = '".intval($id_komplu_pomocne)."' ");
      	    
	        while($data_kompl = pg_fetch_array($dotaz_id_komplu) )
                { $data_kompl_dns = $data_kompl["dns_jmeno"]; }
	    
	        $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$data_kompl_dns;
	        $id_komplu_pomocne_rs .= "\" >".$id_komplu_pomocne."</a>";
              
	        $akce = ereg_replace("".$id_komplu_pomocne."", "".$id_komplu_pomocne_rs."", $akce);    
	      }
	   }
	   elseif( ereg("pridani objektu", $akce) == true )
	   {
	     $pomocne = explode("[id_komplu]", $akce);    
	     $pomocne2 = explode(" ", $pomocne[1] );	    
	     $id_komplu_pomocne = $pomocne2[1];
	
	     //$id_komplu_pomocne = ereg_replace(",", "", $pomocne2[1]);
	
	     if( is_numeric($id_komplu_pomocne) )
	     {
	        $dotaz_id_komplu = pg_query("SELECT * FROM objekty WHERE id_komplu = '".intval($id_komplu_pomocne)."' ");
      	    
	        while($data_kompl = pg_fetch_array($dotaz_id_komplu) )
                { $data_kompl_dns = $data_kompl["dns_jmeno"]; }
	    
	        $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$data_kompl_dns;
	        $id_komplu_pomocne_rs .= "\" >".$id_komplu_pomocne."</a>";
              
	        $akce = ereg_replace("".$id_komplu_pomocne."", "".$id_komplu_pomocne_rs."", $akce);    
	      }
	      
	   }
	   elseif( ereg("uprava nodu", $akce) == true )
	   {
	    $pomocne = explode("[id_nodu]", $akce);    
	    $pomocne2 = explode(" ", $pomocne[1] );	    
	    $id_nodu_pomocne = $pomocne2[2];
	    
	    if( ereg('^([[:digit:]]+)$',$id_nodu_pomocne) )
	    {
	     $dotaz_id_nodu = mysql_query("SELECT * FROM nod_list WHERE id = '".intval($id_nodu_pomocne)."' ");
      	    
	     while($data_nod = mysql_fetch_array($dotaz_id_nodu) )
             { $nazev_nodu = $data_nod["jmeno"]; }
	    
	     $id_nodu_rs = "<a href=\"topology-nod-list.php?find=".$nazev_nodu."&typ_nodu=0";
	     $id_nodu_rs .= "\" >".$id_nodu_pomocne."</a>";
                  
	     //$id_nodu_pomocne2 = "[id_nodu] => ".$id_nodu_pomocne;
	     $akce = ereg_replace(" ".$id_nodu_pomocne." ", " ".$id_nodu_rs." ", $akce);    
	    }
	   }
	   elseif( ereg("automaticke nastaveni sikany", $akce) == true )
	   {
	    $pomocne = explode("[id_komplu]", $akce);    
	    $pomocne2 = explode(" ", $pomocne[1] );	    
	    $pomocne3 = explode("<br>", $pomocne2[1] );	    
	    $id_komplu_pomocne = trim($pomocne3[0]);
	    
	    $dotaz_id_komplu = pg_query("SELECT * FROM objekty WHERE id_komplu = '".intval($id_komplu_pomocne)."' ");
      	    
	    while($data_kompl = pg_fetch_array($dotaz_id_komplu) )
            { $data_kompl_dns = $data_kompl["dns_jmeno"]; }
	    
	    $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$data_kompl_dns;
	    $id_komplu_pomocne_rs .= "\" >".$id_komplu_pomocne."</a>";
              
	    $akce = ereg_replace("".$id_komplu_pomocne."", "".$id_komplu_pomocne_rs."", $akce);    
	   }
	   elseif( ereg("uprava routeru", $akce) == true ){
	   
		$pomocne = explode("[id_routeru]", $akce);    
		$pomocne2 = explode(">", $pomocne[1] );	    
		$pomocne3 = explode("<", $pomocne2[2] );	    
		$id_routeru_pomocne = trim($pomocne3[0]);
	
		$akce = ereg_replace("href=\"topology-router-list.php\"", "href=\"topology-router-list.php?f_id_routeru=".intval($id_routeru_pomocne)."&odeslano=OK\"", $akce);
	   }
	   
	   echo $akce."</span>\n</td>\n";
	
	    echo "<td class=\"az-border1\"><span class=\"az-provedeno-kdy\" >";
	      if ( ( strlen($data["provedeno_kdy2"]) < 1 ) ){ echo "&nbsp;"; }
	      else{ echo $data["provedeno_kdy2"]; }
	    echo "</span></td>\n";
	    
	    echo "<td class=\"az-border1\"><span class=\"az-provedeno-kym\" >";
	      if ( ( strlen($data["provedeno_kym"]) < 1 ) ){ echo "&nbsp;"; }
	      else{ echo $data["provedeno_kym"]; }
	    echo "</span></td>\n";		   
	
	    echo "<td class=\"az-border1\">";
	      if ( $data["vysledek"] == 1 ){ echo "<span class=\"az-vysl-ano\">Ano</span>"; }
	      else{ echo "<span class=\"az-vysl-ne\">Ne</span>"; }
	    echo "</td>\n";
	
	    echo "</tr>\n\n";
	    
            endwhile;
	    
	    echo "</table>\n";
         }

 ?> 
 
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 


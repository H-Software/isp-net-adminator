<?php 

set_time_limit(0);

global $cesta;

$cesta = "../";

require($cesta."include/main.function.shared.php");
require_once($cesta."include/config.php"); 
require_once($cesta."include/check_login.php");
require_once($cesta."include/check_level.php");

if ( ( $_GET["no_login"] != "yes" ) )
{

  if ( !( check_level2("","lvl_faktury_fn_aut_sikana") ) ) 
  { 
   header("Location: ".$cesta."nolevelpage.php");
 
   echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";  
   exit;
  }

}
 
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';
      
require ($cesta."include/charset.php"); 

?>

<title>Adminator2 - Neuhrazené faktury </title> 

<script type="text/javascript" src="../checkboxChanger.js" ></script>

</head> 

<body> 

<?php require($cesta."head.php"); ?> 

<?php require($cesta."category.php"); ?> 

<tr>
  <td colspan="2" height="20" bgcolor="silver" ><?php require($cesta."fn-cat.php"); ?> </td>
</tr>
    
<tr>
  <td colspan="2">
    <!-- zacatek vlastniho obsahu -->

<?php

echo "<div style=\"padding-left: 20px; font-size: 20px; font-weight: bold; padding-top: 20px; padding-bottom: 20px; \" >
	Automatické nastavení šikany u vlastníků s Nehr. Fakturami </div>";

$odeslano=$_GET["odeslano"];
$typ=$_GET["typ"];

$odeslano2=$_POST["odeslano2"];

if( isset($odeslano2) )
{ //budeme sikanovat

 echo "<div style=\"padding-left: 20px; \">";

 $count=0;
 
 while ( list($nazev, $hodnota) = each($_POST) ):
 
    //rozdeleni promennych
    list($id_objektu,$dluzne_obdobi,$cislo_faktury)= explode("&",$hodnota);
    
    if ( (  ( $nazev != "odeslano2") and ($id_objektu > 0) ) )
    {
     echo "<div style=\"\">Šikana pro objekt číslo: <b>".htmlspecialchars($id_objektu)."</b> ";
     echo "(dlužné období: ".htmlspecialchars($dluzne_obdobi).", číslo faktury: ".htmlspecialchars($cislo_faktury).") nastavena: ";
     
     $dotaz_vlastnik=pg_query("SELECT id_cloveka FROM objekty WHERE id_komplu = '".intval($id_objektu)."' ");
     while($data_vlastnik = pg_fetch_array($dotaz_vlastnik) ){ $id_cloveka=$data_vlastnik["id_cloveka"]; }
     
     $dotaz_vice_fa=$conn_mysql->query("SELECT * FROM faktury_neuhrazene WHERE par_id_vlastnika = '".intval($id_cloveka)."' ");	
     if ( (mysql_num_rows($dotaz_vice_fa) > 1) )
     { 
      $sikana_text = "Máte nedoplatek za více faktur. ";
      $sikana_text .= "Dostavte se na naši provozovnu.";
     }
     else
     {
      $sikana_text = "Máte nedoplatek za fakturu č. ".htmlspecialchars($cislo_faktury)." v období ".htmlspecialchars($dluzne_obdobi).". ";
      $sikana_text .= "Dostavte se na naši provozovnu.";
     }
     
      $obj_upd = array( "sikana_status" => "a", "sikana_cas" => "8", 
    			"sikana_text" => $conn_mysql->real_escape_string($sikana_text));
			
     $obj_id = array( "id_komplu" => $id_objektu );
     $res = pg_update($db_ok2, 'objekty', $obj_upd, $obj_id);

     if ($res) { echo "<span style=\"color: green; \" > úspěšně </span>.\n"; }
     else { echo "<span style=\"color: red; \"> neúspěšně </span>."; }
    
    $pole2 .= "<b>akce: automaticke nastaveni sikany z duvodu neuhr. faktur;</b><br>";
    $pole2 .= " puvodni data: [id_komplu]=> ".$id_objektu;
    $pole2 .= "<br>stavajici data: [sikana_status] => a, [sikana_cas] => 8, [sikana_text] => ".$sikana_text;
    
    if ( $res == 1){ $vysledek_write=1; }
     
    $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) ".
			"VALUES ('".$conn_mysql->real_escape_string($pole2)."', '".$conn_mysql->real_escape_string(\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email)."', ".
			"'".$conn_mysql->real_escape_string($vysledek_write)."') ");
    
    echo "<div>";
     	
    }
    $pole2 = "";
    $count++;
  endwhile;

 echo "<div style=\"padding-left: 20px; padding-top: 20px; padding-bottom: 20px; \"><a href=\"fn-aut-sikana.php\">Zpět</a></div>";
    
  echo "</div>";
  	   
 //vyvolani restartu
 if($count > 0)
 {
    Aglobal::work_handler("1"); //reinhard-3 (ros) - restrictions (net-n/sikana)
    Aglobal::work_handler("2"); //reinhard-wifi (ros) - restrictions (net-n/sikana)
	 
    Aglobal::work_handler("3"); //reinhard-fiber - sikana/net-n	       
    Aglobal::work_handler("24"); //reinhard-5 (ros) - restrictions (net-n/sikana) 
 }
 
} //konec if isset(odeslano2)
else
{

  //normal dotaz 
  $dotaz=$conn_mysql->query("SELECT *,DATE_FORMAT(Datum, '%m/%Y') as dluzne_obdobi
		       FROM faktury_neuhrazene 
			WHERE ( ( ignorovat = '0' ) AND par_id_vlastnika > 0 AND po_splatnosti_vlastnik = '1' ) ");	
  $dotaz_radku= $dotaz->num_rows;
 
  $id_sikany="1";
 
 if( $dotaz_radku == 0)
 { echo "<div>Žádné neuhrazené faktury v databázi.</div>"; }
 else
 {
    echo "pocet faktur: ".intval($dotaz_radku)."<br><br>";

    echo "<form name=\"checkboxform\" action=\"\" method=\"POST\" >";
    
    echo "<table border=\"0\" width=\"\" cellspacing=\"5\" >";
    
    echo "
	    <tr>
		<td class=\"fn-sikana-table-1-line\"><b>id objektu: </b></td>
		<td class=\"fn-sikana-table-1-line\"><b>dns objektu: </b></td>
		<td class=\"fn-sikana-table-1-line\"><b>Zakázaný Net: </b></td>
		<td class=\"fn-sikana-table-1-line\"><b>Aktivovaná šikana: </b></td>
		<td class=\"fn-sikana-table-1-line\"><b>Dlužné období: </b></td>
		<td class=\"fn-sikana-table-1-line\"><b>Dlužná faktura: </b></td>
		<td class=\"fn-sikana-table-1-line\"><b>dluží více faktur: </b></td>
		<td class=\"fn-sikana-table-1-line\"><b>Vybrat: </b></td>
		
	    </tr>
	    
	    <tr><td colspan=\"7\" ><br></td></tr>
	    ";
	    
    $cislo_radku = 1;
    
  while( $data= $dotaz->fetch_array() )
  {
      $id_cloveka=$data["par_id_vlastnika"];
      $id_faktury=$data["id"];

      // zde zjistit jestli uz se zobrazilo
      for ($p = 0; $p < count($sikanovani_vlastnici); ++$p)
      {
       if( $sikanovani_vlastnici[$p] == $id_cloveka){ $zobrazit_fakturu = "ne"; }
      }
      
    if ( $zobrazit_fakturu == "ano" )    
    {
      //zjistime vlastnika
      $dotaz_objekty=pg_query("SELECT * FROM objekty WHERE id_cloveka = '".intval($id_cloveka)."' ");
      $dotaz_objekty_radku=pg_num_rows($dotaz_objekty);
      
      if( $dotaz_objekty_radku > 0)
      { // vlastnik ma objekty, takze ted testnem stavy
      
        while( ( $data_objekty=pg_fetch_array($dotaz_objekty) ) )
        { 
	    $akce="ano";

	    if( ( ($cislo_radku % 2) == 0 )){ $class="fn-sikana-table-lichy-radek"; }
	    else{ $class="fn-sikana-table-sudy-radek"; }
    	
	   $id_cloveka=$data_objekty["id_cloveka"];
	   
	   $sikanovani_vlastnici[] = $id_cloveka;
	      
	    //$objekty[]=$data_objekty["id_komplu"]; 
	    echo "<tr>";
	    
		if ( $data_objekty["vip_snat"] == 1 ){ $class2="fn-sikana-table-color"; $akce="ne"; }
		else{ $class2=""; }
		 
		echo "<td width=\"\" class=\"".$class." ".$class2."\">".htmlspecialchars($data_objekty["id_komplu"])."</td>";
		echo "<td width=\"\" class=\"".$class." ".$class2."\" >".htmlspecialchars($data_objekty["dns_jmeno"])."</td>";
	
	        echo "<td class=\"".$class." ".$class2."\" >";
		if( $data_objekty["dov_net"] == "n" ){ echo "<span style=\"color: red; \" > Ano </span>"; $akce="ne"; }
		else{ echo "<br>"; }
		echo "</td>";
		
		echo "<td class=\"".$class." ".$class2."\">";
		if( $data_objekty["sikana_status"] == "a" ){ echo "<span style=\"color: orange; \" > Ano </span>"; $akce="ne"; }
		else{ echo "<br>"; }
		echo "</td>";
		
		echo "<td class=\"".$class." ".$class2."\">".htmlspecialchars($data["dluzne_obdobi"])."</td>";
		echo "<td class=\"".$class." ".$class2."\">".htmlspecialchars($data["Cislo"])."</td>";

		echo "<td class=\"".$class."\" >";
		
		//dotaz vice faktur
		$dotaz_vice_fa=$conn_mysql->query("SELECT * FROM faktury_neuhrazene WHERE par_id_vlastnika = '".intval($id_cloveka)."' ");	
	        if ( (mysql_num_rows($dotaz_vice_fa) > 1) )
		{ echo "<span style=\"color: Fuchsia; font-weight: bold; \">Ano</span>"; }
		else
		{ echo "ne"; }
  
		echo "</td>";
		
		//zde uprava
		if( $akce == "ano")
		{
		 echo "<td class=\"".$class."\"><input type=\"checkbox\" name=\"q".intval($id_sikany)."\" ";
		    echo " value=\"".intval($data_objekty["id_komplu"])."&".$data["dluzne_obdobi"]."&";
		    echo htmlspecialchars($data["Cislo"])."\" ></td>";
	
		 $id_sikany++;

		}
		else
		{
		echo "<td class=\"".$class."\" ><br></td>";
		}
		
		
	    echo "<tr> \n\n";
	    
	 $cislo_radku++;		 
		
	} // konec sekundarni while
	
	
      } // konec if dotaz_objekty_radku > 0
      else
      { // vlastnik nema objekt
            
       $sikana_status="9"; //nelze nalezt objekt
              
      } // konec else if dotaz_objekty_radku > 0
    
    } // konec if zobrazit fakturu
    
    $zobrazit_fakturu = "ano";
    
  } // konec while
    
  echo "<tr><td colspan=\"7\" ><br></td></tr>";

  echo "<tr>
	  <td colspan=\"3\" >
	   <input type=\"button\" onClick=\"checkedAll()\" value=\" Zaškrtni vše \" >

	   <INPUT TYPE=\"BUTTON\" onClick=\"uncheckedAll()\" VALUE=\" Odškrtni vše \" >
	   <INPUT TYPE=\"BUTTON\" onClick=\"reverseAll()\" VALUE=\" Reverse \" >
				  
	  </td>
	  <td colspan=\"4\" >
	   <span style=\"padding-left: 50px; \" >
	    <input type=\"submit\" name=\"odeslano2\" value=\"OK\" >
	   </span>
	</td></tr>";
	
    echo "</table>";
    echo "</form>";
  
 } // konec else if dotaz_radku == 0

} // konec else ! isset odeslano

?>

   <!-- konec vlastniho obsahu -->
 </td>
  </tr>
  
 </table>

</body> 
</html> 


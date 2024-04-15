<? 

global $cesta;

$cesta = "../";

require($cesta."include/main.function.shared.php");
include ($cesta."include/config.php"); 
include ($cesta."include/check_login.php");
include ($cesta."include/check_level.php");

if ( !( check_level($level,108) ) ) 
{
$stranka=$cesta.'nolevelpage.php'; header("Location: ".$stranka);
 
echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";  Exit;
}
   
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';
      
include ($cesta."include/charset.php"); 

?>

<title>Adminator2 - Neuhrazené faktury </title> 

</head> 

<body> 

<? include ($cesta."head.php"); ?> 

<? include ($cesta."category.php"); ?> 

 <tr>
  <td colspan="2" height="20" bgcolor="silver" ><? include("../fn-cat.php"); ?> </td>
    </tr>
    
<tr>
  <td colspan="2">
    <!-- zacatek vlastniho obsahu -->

<?

// echo "sem zbytek ... ";
$typ_uziv=$_POST["typ_uziv"];
$parovani=$_POST["parovani"];

$id_cloveka = $_POST["id_cloveka"];

if( (strlen($id_cloveka) < 1 ))
{ $id_cloveka = $_GET["id_cloveka"]; }

$filtr_stav_emailu=$_POST["filtr_stav_emailu"];

if ( !isset($filtr_stav_emailu) )
{ $filtr_stav_emailu=$_GET["filtr_stav_emailu"]; }

$filtr_ignorovat = $_POST["filtr_ignorovat"];

$razeni1 = $_POST["razeni1"];
$razeni2 = $_POST["razeni2"];

$sql="SELECT * FROM faktury_neuhrazene WHERE ( Cislo > 0 ";

if( $typ_uziv == 1 ){ $sql .= " AND length(ICO) < 3 "; }
elseif( $typ_uziv == 2 ){ $sql .= " AND length(ICO) > 3 "; }

if( $parovani == 1 ){ $sql .= " AND par_id_vlastnika > 0 "; }
elseif( $parovani == 2 ){ $sql .= " AND par_id_vlastnika = 0 "; }

if( $filtr_ignorovat == 1 ){ $sql .= " AND ignorovat = 1 "; }
elseif( $filtr_ignorovat == 2 ){ $sql .= " AND ignorovat = 0 "; }

if ( ( ($filtr_stav_emailu >= 0) and ( $filtr_stav_emailu < 99) ) )
{ $sql .= " AND aut_email_stav = '$filtr_stav_emailu' "; }

if( $id_cloveka > 0 )
{ $sql .= " AND par_id_vlastnika = '$id_cloveka' "; }

$sql .= " ) ";

if ( $razeni1 > 0)
{ 
 $sql .= " order by ";
 
 if ($razeni1 == 1){ $sql .= "Cislo "; }
 elseif ($razeni1 == 2){ $sql .= "VarSym"; }

 elseif ($razeni1 == 3){ $sql .= "Datum"; }
 elseif ($razeni1 == 4){ $sql .= "DatSplat"; }
 elseif ($razeni1 == 5){ $sql .= "Firma"; }
 elseif ($razeni1 == 6){ $sql .= "Jmeno"; }
 
}

if ( $razeni2 > 0)
{ 
 $sql .= ", ";
 
 if ($razeni2 == 1){ $sql .= "Cislo "; }
 elseif ($razeni2 == 2){ $sql .= "VarSym"; }

 elseif ($razeni2 == 3){ $sql .= "Datum"; }
 elseif ($razeni2 == 4){ $sql .= "DatSplat"; }
 elseif ($razeni2 == 5){ $sql .= "Firma"; }
 elseif ($razeni2 == 6){ $sql .= "Jmeno"; }
 
}

$dotaz=$conn_mysql->query($sql);

$dotaz_radku=$dotaz->num_rows;

echo "<div style=\"padding-top: 20px; padding-bottom: 20px;\" >
	<span style=\"font-size: 20px; font-weight: bold;  \" >
	Výpis neuhrazených faktur
    </span>";

echo "<span style=\"font-size: ; font-weight: bold; padding-left: 50px; \" >
	<a href=\"fn-export.php\">export neuhrazených faktur</a> </span>";

echo "</div>";

echo "<form method=\"POST\" action=\"".$_SERVER["PHP_SELF"]."\">
     <table border=\"0\" >
	<tr>
	  <td><div style=\"font-weight: bold; \">Filtrování: </div></td>
	  <td><div style=\"padding-left: 25px; padding-right: 25px\">Typ klienta: </div></td>
	  <td><div style=\"padding-left: 25px; padding-right: 25px\">Párování: </div></td>
	  <td><div style=\"padding-left: 25px; padding-right: 25px\">Stav poslání emailu: </div></td>
	  <td><div style=\"padding-left: 25px; padding-right: 25px\">Ignorovat fakturu: </div></td>
	  <td><div style=\"padding-left: 25px; padding-right: 25px\">id klienta: </div></td>
	</tr>
	
	<tr>
	  <td><br></td> 
	
	  <td>
	   <div style=\"padding-left: 25px; padding-right: 25px; \" > 
	    <select size=\"1\" name=\"typ_uziv\" >
	      <option value=\"0\" class=\"fn-select-nevybrano\" >Nevybráno</option>
	      <option value=\"1\" "; if($typ_uziv == 1){ echo " selected "; } echo ">Domácí uživatelé</option>
	      <option value=\"2\" "; if($typ_uziv == 2){ echo " selected "; } echo ">Firemní uživatelé</option>

	    </select>
	   </div>
	  </td>
	
	  <td>
	   <div style=\"padding-left: 25px; padding-right: 25px; \" > 
	    <select size=\"1\" name=\"parovani\" >
	      <option value=\"0\" class=\"fn-select-nevybrano\" >Nevybráno</option>
	      <option value=\"1\" "; if($parovani == 1){ echo " selected "; } echo ">spárované </option>
	      <option value=\"2\" "; if($parovani == 2){ echo " selected "; } echo ">nespárované </option>
	    </select>
	   </div>
	  </td>
	
	  <td>
	   <div style=\"padding-left: 25px; padding-right: 25px; \" > 
	    <select size=\"1\" name=\"filtr_stav_emailu\" >
	      <option value=\"99\" class=\"fn-select-nevybrano\" >Nevybráno</option>
	      <option value=\"0\" "; if($filtr_stav_emailu == 0){ echo " selected "; } echo " >Email ještě nepoeslán</option>
	      <option value=\"1\" "; if( $filtr_stav_emailu == 1){ echo " selected "; } echo " >Email úspěšně odeslán</option>
	      <option value=\"2\" "; if($filtr_stav_emailu == 2){ echo " selected "; } echo " >Email nelze odeslat</option>

	      <option value=\"8\" "; if($filtr_stav_emailu == 8){ echo " selected "; } echo " >Klient nemá email / špatný formát emailu</option>
	      <option value=\"9\" "; if($filtr_stav_emailu == 9){ echo " selected "; } echo " >Nelze určit vlastníka faktury</option>
	      
	    </select>
	   </div>
	  </td>
	
	  <td>
	   <div style=\"padding-left: 25px; padding-right: 25px; \" > 
	    <select size=\"1\" name=\"filtr_ignorovat\" >
	      <option value=\"0\" class=\"fn-select-nevybrano\" >Nevybráno</option>
	      <option value=\"1\" "; if($filtr_ignorovat == 1){ echo " selected "; } echo ">Ano </option>
	      <option value=\"2\" "; if($filtr_ignorovat == 2){ echo " selected "; } echo ">Ne </option>
	    </select>
	   </div>
	  </td>
	
	 <td>
	  <input type=\"text\" name=\"id_cloveka\" value=\"".$id_cloveka."\" size=\"5\" >
	 </td> 
	</tr>

	<tr><td><br></td></tr>
		
	<tr>
	    <td><div style=\"font-weight: bold; \">Řazení: </div></td>
	
	    <td><div style=\"\">Primární: </div></td>
	
	    <td>
	     <div style=\"padding-left: 25px; padding-right: 25px; \" > 
	      <select size=\"1\" name=\"razeni1\" >
	        <option value=\"0\" "; if($razeni1 == 0){ echo " selected "; } echo "class=\"fn-select-nevybrano\" >Nevybráno</option>
	      
	        <option value=\"1\" "; if($razeni1 == 1){ echo " selected "; } echo " >Číslo faktury </option>
		<option value=\"2\" "; if($razeni1 == 2){ echo " selected "; } echo " >Var. symbol </option>
		
		<option value=\"3\" "; if($razeni1 == 3){ echo " selected "; } echo " >Datum vystavení </option>
		<option value=\"4\" "; if($razeni1 == 4){ echo " selected "; } echo " >Datum splatnosti </option>
		
		<option value=\"5\" "; if($razeni1 == 5){ echo " selected "; } echo " >Firma </option>
		<option value=\"6\" "; if($razeni1 == 6){ echo " selected "; } echo " >Jméno </option>
		
	      </select>
	     </div>
	    </td>
	
	    <td><div style=\"\">Sekundární: </div></td>
	
	    <td>
	     <div style=\"padding-left: 25px; padding-right: 25px; \" > 
	      <select size=\"1\" name=\"razeni2\" >
	        <option value=\"0\" "; if($razeni2 == 0){ echo " selected "; } echo "class=\"fn-select-nevybrano\" >Nevybráno</option>
	      
	        <option value=\"1\" "; if($razeni2 == 1){ echo " selected "; } echo " >Číslo faktury </option>
		<option value=\"2\" "; if($razeni2 == 2){ echo " selected "; } echo " >Var. symbol </option>
		
		<option value=\"3\" "; if($razeni2 == 3){ echo " selected "; } echo " >Datum vystavení </option>
		<option value=\"4\" "; if($razeni2 == 4){ echo " selected "; } echo " >Datum splatnosti </option>
		
		<option value=\"5\" "; if($razeni2 == 5){ echo " selected "; } echo " >Firma </option>
		<option value=\"6\" "; if($razeni2 == 6){ echo " selected "; } echo " >Jméno </option>
		
	      </select>
	     </div>
	    </td>
	
	<td><span style=\"padding-left: 10px; padding-right: 10px;\"><input type=\"submit\" name=\"odeslat\" value=\"OK\"></span></td>
	  
	</tr>
	
      </table>
      </form>";

 $class_cara_plna="border-bottom: 1px solid; ";
 
 // $
echo "<table border=\"0\" cellpadding=\"5\" >";

  echo "<tr>";
  echo "<td class=\"fn-tab-prvni-radka\">Číslo faktury: </td>";
  echo "<td class=\"fn-tab-prvni-radka\">Var. symbol: </td>";
  echo "<td class=\"fn-tab-prvni-radka\">Datum vystavení: </td>";
  echo "<td class=\"fn-tab-prvni-radka\">Datum splatnosti: </td>";
  
  echo "<td class=\"fn-tab-prvni-radka\">Částka: </td>";
  echo "<td class=\"fn-tab-prvni-radka\">Neuhrazená částka: </td>";
  
  echo "<td class=\"fn-tab-prvni-radka\">Firma: </td>";
  echo "<td class=\"fn-tab-prvni-radka\">Jméno klienta: </td>";
  
  echo "<td class=\"fn-tab-prvni-radka\">IČO: </td>";
  echo "<td class=\"fn-tab-prvni-radka\">DIČ: </td>";
  
  echo "</tr><tr>";
  
  echo "<td class=\"fn-tab-druha-radka\">kontrolní<br> číslo: </td>";
  echo "<td class=\"fn-tab-druha-radka\">párování <br>id vlastníka:</td>";
  echo "<td class=\"fn-tab-druha-radka\">párování <br>stav:</td>";
  
  echo "<td class=\"fn-tab-druha-radka\">ověřeno: </td>";

  echo "<td class=\"fn-tab-druha-radka\">Stav poslání<br> emailu: </td>";
  echo "<td class=\"fn-tab-druha-radka\">Datum poslání<br> emailu: </td>";

  echo "<td class=\"fn-tab-druha-radka\">Stav (Datum) poslání SMS: </td>";
  echo "<td class=\"fn-tab-druha-radka\">Po splatnosti <br>dle vlastníka: </td>";

  echo "<td class=\"fn-tab-druha-radka\">Ignorovat<br> fakturu: </td>";

  echo "<td class=\"fn-tab-druha-radka\">id záznamu: </td>";

  echo "</tr>";

  echo "<tr><td colspan=\"\"><br></td></tr>";
  
  if ($dotaz_radku == 0)
  {
   echo "<tr><td colspan=\"5\"><span style=\"font-size: 16px; font-weight: bold; color: red; \">
		Žádné faktury v databázi nenalezeny. </span>
	</td></tr>";
  
   exit;
  }
  
$i=1;

global $ico;

while ( ( $data=$dotaz->fetch_array() ) )
{

  $Firma = $data["Firma"];
  $Jmeno = $data["Jmeno"];
    
 $kc_celkem = $data["KcCelkem"];
  $kc_likv = $data["KcLikv"];
  
  $ico=$data["ICO"];
    
  $VarSym=$data["VarSym"];
      
echo "<tr>";  

echo "<td class=\"fn-tab-licha-radka\">".$data["Cislo"]."</td>";
  echo "<td class=\"fn-tab-licha-radka\">".$VarSym."</td>";
  echo "<td class=\"fn-tab-licha-radka\">".$data["Datum"]."</td>";
  echo "<td class=\"fn-tab-licha-radka\">".$data["DatSplat"]."</td>";

  echo "<td class=\"fn-tab-licha-radka\">".$kc_celkem."</td>";
  echo "<td class=\"fn-tab-licha-radka\">".$kc_likv."</td>";
  
  echo "<td class=\"fn-tab-licha-radka\">".$Firma."&nbsp;</td>";
  echo "<td class=\"fn-tab-licha-radka\">".$Jmeno."&nbsp;</td>";
  
  echo "<td class=\"fn-tab-licha-radka\">".$ico."&nbsp;</td>";
  echo "<td class=\"fn-tab-licha-radka\">".$data["DIC"]."&nbsp;</td>";

echo "</tr>";
echo "<tr>";

//zde druhy radek   
  echo "<td class=\"fn-tab-suda-radka\">".$i."</td>";
     
  echo "<td class=\"fn-tab-suda-radka\">";
  
  $id_cloveka=$data["par_id_vlastnika"];
      
  $vlastnik_dotaz=pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka'");
  $vlastnik_radku=pg_num_rows($vlastnik_dotaz);
  
  if ( $vlastnik_radku > 0)
  {
  
  while ($data_vlastnik=pg_fetch_array($vlastnik_dotaz))
  { $firma_vlastnik=$data_vlastnik["firma"]; $archiv_vlastnik=$data_vlastnik["archiv"]; }
  
  if ( $archiv_vlastnik == 1)
  { echo "<a href=\"../vlastnici-archiv.php?find_id=".$id_cloveka."\" >".$id_cloveka."</a> \n"; }
  elseif ($firma_vlastnik == 1 )
  { echo "<a href=\"../vlastnici2.php?find_id=".$id_cloveka."\" >".$id_cloveka."</a> \n"; }
  else
  { echo "<a href=\"../vlastnici.php?find_id=".$id_cloveka."\" >".$id_cloveka."</a> \n"; }
  
  }
  else
  { echo "nepřiřazeno"; }
  
  // echo $data["par_id_vlastnika"];
  
  echo "</td>";  
  echo "<td class=\"fn-tab-suda-radka\">".$data["par_stav"]."</td>";  
  
  echo "<td class=\"fn-tab-suda-radka\">";
   if ( $data["overeno"] == 1 ){ echo "Ano"; }
   else{ echo "Ne"; }
   
  echo "</td>";
  
  echo "<td class=\"fn-tab-suda-radka\">";
    
    if ( $data["aut_email_stav"] == 0 ){ echo "Email ještě neposlán"; }
    elseif ( $data["aut_email_stav"] == 1 ){ echo "Email úspěšně odeslán"; }
    elseif ( $data["aut_email_stav"] == 2 ){ echo "Email nelze odeslat"; }
    
    elseif ( $data["aut_email_stav"] == 7 ){ echo "Nelze zjistit splatnost u vlastnika "; }
    elseif ( $data["aut_email_stav"] == 8 ){ echo "Klient nemá email/<br>špatný formát emailu "; }
    elseif ( $data["aut_email_stav"] == 9 ){ echo "Nelze určit <br>vlastníka faktury "; }
    else{ echo "Nelze zjistit"; }
        
  echo "</td>"; 

  echo "<td class=\"fn-tab-suda-radka\">".$data["aut_email_datum"]."</td>";
         
  echo "<td class=\"fn-tab-suda-radka\">";
    if ( $data["aut_sms_stav"] == 0 ){ echo "sms ještě neposlána"; }
    elseif( $data["aut_sms_stav"] == 9 ){ echo "nelze zjisti vlastnik"; }
    elseif( $data["aut_sms_stav"] == 8 ){ echo "tel. číslo není ve správném formátu"; }
    elseif( $data["aut_sms_stav"] == 2 ){ echo "chyba při odeslání sms"; }
    elseif( $data["aut_sms_stav"] == 1 ){ echo "sms odeslána úspěšně"; }
    else{ echo "jiný stav ".$data["aut_sms_stav"]; }

    echo " ( ".$data["aut_sms_datum"]." ) ";
  
  echo "</td>";  
  
  echo "<td class=\"fn-tab-suda-radka\">";
    if ( $data["po_splatnosti_vlastnik"] == 1 ){ echo "Ano"; }
    else{ echo "Ne"; }
  
  echo "</td>";
    
  echo "<td class=\"fn-tab-suda-radka\">";
  
  if ( $data["ignorovat"] == 1)
  { 
   echo "Ano"; 
   echo " ( <a href=\"fn-update.php?akce=ignorovat&".$data["id"]."=0\" >přepnout</a> )"; 
  }
  else
  { 
   echo "Ne"; 
   echo " ( <a href=\"fn-update.php?akce=ignorovat&".$data["id"]."=1\" >přepnout</a> )";
  }
  
  echo "</td>";  
  
  echo "<td class=\"fn-tab-suda-radka\">".$data["id"]."</td>";  
  
echo "</tr>";

$i++;

}

  echo "</table>";

?>

<!-- konec vlastniho obsahu -->
 </td>
  </tr>
  
 </table>

</body> 
</html> 


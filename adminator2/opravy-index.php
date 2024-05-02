<?php

require("include/main.function.shared.php");
require_once("include/config.php"); 
require_once("include/check_login.php");
require_once("include/check_level.php");

if ( !( check_level($level,101) ) )
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

<title>Adminator 2 - Závady/opravy </title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 <tr>
  <td colspan="2" bgcolor="silver" height=""><? include("opravy-cat-inc.php"); ?></td>
 </tr>
  
  <tr>
  <td colspan="2">
 <!-- zacatek vlastniho obsahu -->  

<?

$typ=$_GET["typ"];

$zobr_vlastnika=$_GET["zobr_vlastnika"];

$priorita_filtr = $_GET["priorita_filtr"];
$v_reseni_filtr = $_GET["v_reseni_filtr"];
$vyreseno_filtr = $_GET["vyreseno_filtr"];

$id_vlastnika = $_POST["id_vlastnika"];

$vlastnik_hledani = $_POST["vlastnik_hledani"];

$id_predchozi_opravy = $_POST["id_predchozi_opravy"];
$datum_vlozeni = $_POST["datum_vlozeni"];
$priorita = $_POST["priorita"];

$v_reseni = $_POST["v_reseni"];
$vyreseno = $_POST["vyreseno"];
$text=$_POST["text"];

$odeslano = $_POST["odeslano"];

if ( ( strlen($id_vlastnika) < 1 ) ) { $id_vlastnika = $_GET["id_vlastnika"]; }

if ( ( strlen($id_predchozi_opravy) < 1 ) ) { $id_predchozi_opravy = $_GET["id_predchozi_opravy"]; }

if ( $vyreseno == 1 and $v_reseni != 1){ $v_reseni="1"; }

if ( (strlen($datum_vlozeni) < 1) )
{ $datum_vlozeni = strftime("%Y/%m/%d %H:%M:%S", time()); }

if ( ( strlen($vlastnik_hledani) < 1 ) ){ $vlastnik_hledani="%"; }
else 
{ 
  if ( !(ereg("^%.*%$",$vlastnik_hledani)) )
  { $vlastnik_hledani="%".$vlastnik_hledani."%"; }
}

if ( ($typ == 1) or !isset($typ) )
{
// pridani zadosti

if ( $id_vlastnika < 1){ $error .= " <div >Není zvolený vlastník. Prosím vyberte vlastníka. </div>"; }

if( ( isset($odeslano) and !(isset($error) ) ) )
{
// budeme ukladat odeslany form

  if ( $vyreseno == 1 and $v_reseni != 1){ $v_reseni="1"; }

  if( $v_reseni == 1){ $v_reseni_kym = \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email; }
  if( $vyreseno == 1){ $vyreseno_kym = \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email; }

  $add=$conn_mysql->query("INSERT INTO opravy (id_vlastnika,id_predchozi_opravy, datum_vlozeni, priorita, v_reseni, v_reseni_kym, vyreseno, vyreseno_kym,text,vlozil )
                    VALUES ('$id_vlastnika','$id_predchozi_opravy','$datum_vlozeni','$priorita','$v_reseni','$v_reseni_kym','$vyreseno','$vyreseno_kym','$text','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "' ) ");

 if( $id_predchozi_opravy > 0)
 {
  //upravime predchozi prispevky
    if( $vyreseno == 1)
    { $rs_vyr=$conn_mysql->query("UPDATE opravy SET vyreseno = '1',v_reseni = '0' WHERE id_predchozi_opravy = '$id_predchozi_opravy' "); }
    elseif( $v_reseni == 1)
    { $rs_v_res=$conn_mysql->query("UPDATE opravy SET v_reseni = '1' WHERE id_predchozi_opravy = '$id_predchozi_opravy' "); }
    
    //zjistime jestli neni nadrazeny prispevek
    $vysl = mysql_query("SELECT * FROM opravy WHERE id_opravy = '$id_predchozi_opravy' ");
    while($data_vysl = mysql_fetch_array($vysl) )
    {
     $vysl_id_opravy = $data_vysl["id_opravy"];
     $id_predchozi_opravy = $data_vysl["id_predchozi_opravy"];
     
     if( $vyreseno == 1)
     { $rs_vyr_vysl=$conn_mysql->query("UPDATE opravy SET vyreseno = '1',v_reseni = '0' WHERE id_opravy = '$id_opravy' "); }
     elseif( $v_reseni == 1)
     { $rs_v_res_vysl=$conn_mysql->query("UPDATE opravy SET v_reseni = '1' WHERE id_opravy = '$id_opravy' "); }
    
    } // konec while mysql_fetch_array
    
 } // konec if id > 0
 
   if ($add){ echo "<br><span style=\"color: green; font-size: 18px; \">Záznam úspěšně vložen.</span><br><br>"; }
   else{ echo "<span style=\"color: red; font-weight: bold; font-size: 16px; \">Záznam nelze vložit do databáze. </span>"; }

   echo "<div style=\"color: grey; \">vysledek sekundarnich akci: rs_vyr: ".$rs_vyr.", rs_v_res: ".$rs_v_res.", </div>";
     
     // pridame to do archivu zmen
    $pole=" akce: pridani opravy; <br>";
    $pole .= " id_vlastnika: ".$id_vlastnika.", id_predchozi_opravy: ".$id_predchozi_opravy.", datum_vlozeni: ".$datum_vlozeni.", priorita: ".$priorita;
    $pole .= ", v_reseni: ".$v_reseni.", v_reseni_kym: ".$v_reseni_kym.", vyreseno: ".$vyreseno.", vyreseno_kym: ".$vyreseno_kym;
    $pole .= ", text: ".$text.", <br> ";

    if ( $add == 1){ $vysledek_write=1; }
    $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write')");

    //zkusime poslat email
        
    // include("opravy-email-inc.php");
    
echo "<br>
Objekt byl přidán/upraven , zadané údaje: <br><br>
<b>id vlastníka</b>: ".$id_vlastnika." <br>
<b>id predchozi opravy</b>: ".$id_predchozi_opravy." <br>
<b>datum vlozeni</b>: ".$datum_vlozeni." <br>

<b>priorita</b>: ".$priorita." <br>
<b>v_reseni</b>: ".$v_reseni." <br>
<b>v reseni kym</b>: ".$v_reseni_kym." <br>

<b>vyreseno</b>: ".$vyreseno." <br>
<b>vyreseno kym</b>: ".$vyreseno_kym." <br>

<b>text</b>: ".$text." <br>

";


} // konec if isset odeslano
else
{ 
 echo "<div style=\"padding-bottom: 10px; padding-top: 10px; font-size: 20px; color: red; font-weight: bold; \"> ".$error."</div>";
 
 $pocet_bunek="5";

echo "\n\n\n <form action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\" name=\"form3\" >";

echo "<table border=\"0\" width=\"100%\" >";

echo "<tr><td colspan=\"".$pocet_bunek."\" ><br></td></tr>";

echo "<tr><td colspan=\"".$pocet_bunek."\" ><span style=\"font-size: 18px; font-weight: bold; \" >Vložení závady / opravy </span></td></tr>";

echo "<tr><td colspan=\"".$pocet_bunek."\" ><br></td></tr>";

echo " <tr>
        <td width=\"\">Výběr vlastníka: </td>
        <td colspan=\"".$pocet_bunek."\" >

            <select size=\"1\" name=\"id_vlastnika\" onchange=\"self.document.forms.form3.submit()\" >";

          $sql="%".$vlastnik_hledani."%";

          $select1=" WHERE ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' OR ulice LIKE '$sql' OR mesto LIKE '$sql' ";
          $select2=" OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' OR vs LIKE '$sql' OR id_cloveka LIKE '$sql' ) ";

         $dotaz_klienti = pg_query("SELECT * FROM vlastnici ".$select1.$select2." order by prijmeni ");

         $radku_klienti = pg_num_rows($dotaz_klienti);

         if ( $radku_klienti == 0 )
         { echo "<option value=\"0\" style=\"color: gray; font-style: bold; \">Žádný klient neodpovídá výběru/ není v databazi! </option>"; }
         else
         {

              echo "<option value=\"0\" style=\"color: gray; font-style: bold; \"> Není vybráno </option>";

              while( $data2=pg_fetch_array($dotaz_klienti) ):

              echo "<option value=\"".$data2["id_cloveka"]."\" ";

              if ( ( $id_vlastnika == $data2["id_cloveka"]) ){ echo " selected  "; }

              echo " >";

              echo " ".$data2["prijmeni"].", ".$data2["jmeno"]."  ( ".$data2["nick"]."  )  ";
              echo " ".$data2["ulice"].", VS: ".$data2["vs"];

              echo "</option> \n ";

              endwhile;

        }

echo "    </select>
        </td>

    </tr>";

echo " <tr>
        <td colspan=\"\">Výběr vlastníka - hledání:</td>
        <td><input type=\"text\" size=\"40\" name=\"vlastnik_hledani\" value=\"".$vlastnik_hledani."\" ></td>
        <td colspan=\"3\" >
            <span style=\"color: grey; font-weight: bold; \" >
		<span style=\"padding-left: 10px; \" ><input type=\"submit\" name=\"hledat\" value=\"Hledat\" ></span>
		<span style=\"padding-left: 10px; \" >hledaný výraz:</span>
            <span style=\"color: #555555\">".$sql." </span></span>
        </td>

    </tr>";

echo "<tr><td colspan=\"".$pocet_bunek."\" ><br></td></tr>";

echo " <tr>
        <td width=\"\">Přidat k existujícímí závadě: </td>
        <td width=\"\" colspan=\"4\" >

         <select size=\"1\" name=\"id_predchozi_opravy\" onChange=\"self.document.forms.form3.submit()\" >";

         $dotaz_predchozi_oprava = mysql_query("SELECT * FROM opravy WHERE ( id_vlastnika = '$id_vlastnika' AND id_predchozi_opravy = 0 ) ");
         $dotaz_predchozi_oprava_radku = mysql_num_rows($dotaz_predchozi_oprava);

         if ( $dotaz_predchozi_oprava_radku == 0 )
         { echo "<option value=\"0\" style=\"color: gray; font-style: bold; \">Žádná závada v databázi neuložena. ( Režim: nová závada )</option>"; }
         else
         {

              echo "<option value=\"0\" style=\"color: gray; font-style: bold; \"> Nová Závada </option>";

              while( $data_predchozi_oprava = mysql_fetch_array($dotaz_predchozi_oprava) ):

              echo "<option value=\"".$data_predchozi_oprava["id_opravy"]."\" ";

              if( $id_predchozi_opravy == $data_predchozi_oprava["id_opravy"] ){ echo " selected  "; }

              echo " >";

              echo "id: ".$data_predchozi_oprava["id_opravy"].", datum: ".$data_predchozi_oprava["datum_vlozeni"];
	      
	      echo ", náhled: "; 
	      
	      echo substr($data_predchozi_oprava["text"], 0, 20);
	      
	      echo ", v řešení: ";
	      if ( $data_predchozi_oprava["v_reseni"] == 1){ echo "ano"; }
	      else{ echo "ne"; }
	    
	    
	      echo ", vyřešeno: ";
	      if ( $data_predchozi_oprava["vyreseno"] == 1){ echo "ano"; }
	      else{ echo "ne"; }
	      echo " ";
	      
              echo "</option> \n ";

              endwhile;
	}

echo "  </select>
       </td>
    </tr>";

echo "<tr><td colspan=\"".$pocet_bunek."\" ><br></td></tr>";

echo " <tr>
        <td colspan=\"\">Datum vložení: </td>
        <td><input type=\"text\" size=\"\" name=\"datum_vlozeni\" value=\"".$datum_vlozeni."\" ></td>
        <td colspan=\"2\" width=\"20px\" >
	    <span style=\"padding-left: 10px; padding-right: 0px; \">Priorita: </span>
	</td>
        
	<td>
	  <select size=\"3\" name=\"priorita\" >";
	
	  if ( ($id_predchozi_opravy > 0) )
	  {
	  $dotaz_priorita = mysql_query("SELECT * FROM opravy WHERE id_opravy = '$id_predchozi_opravy' ");
	  while ($data_priorita=mysql_fetch_array($dotaz_priorita) )
	  { $priorita_db = $data_priorita["priorita"]; }

	  echo  "<option value=\"0\" "; if($priorita_db == "0"){ echo " selected "; } echo " >Nízká</option>
	    <option value=\"1\" "; if($priorita_db == "1"){ echo " selected "; } echo " >Normální</option>
	    <option value=\"2\" "; if($priorita_db == "2"){ echo " selected "; } echo " >Vysoká</option>";
	 
	  
	  }
	  else
	  {
	   echo "<option value=\"0\" "; if( ( $priorita == "0" or !isset($priorita) ) ){ echo " selected "; } echo " >Nízká</option>
	    <option value=\"1\" "; if($priorita == "1"){ echo " selected "; } echo " >Normální</option>
	    <option value=\"2\" "; if($priorita == "2"){ echo " selected "; } echo " >Vysoká</option>";
	  }
	  
echo "  </select>  
	
	</td>
       </tr>";

echo "<tr><td colspan=\"".$pocet_bunek."\" ><br></td></tr>";

echo " <tr>
        <td colspan=\"\">V řešení: </td>
        <td>";
	  
	if ( $id_predchozi_opravy > 0 )
	{
	 $dotaz_v_reseni = mysql_query("SELECT * FROM opravy WHERE id_opravy = '$id_predchozi_opravy' ");
	 while ($data_reseni=mysql_fetch_array($dotaz_v_reseni) )
	 { if ( $data_reseni["v_reseni"] == 1 ){ $v_reseni_db= "1"; } }
	 
	 if( $vyreseno == 1)
	 {
	   echo "<select size=\"1\" name=\"v_reseni\" onChange=\"self.document.forms.form3.submit()\" >";
	  
	    echo "<option value=\"0\" >Ne (nelze změnit)</option>";
	 
	 }
	 elseif ( $v_reseni_db == "1" )
	 {
	   echo "<select size=\"1\" name=\"v_reseni\" onChange=\"self.document.forms.form3.submit()\" >";
	  
	    echo "<option value=\"1\" >Ano (nelze změnit)</option>";
	 } 
	 else
	 {
	   echo "<select size=\"2\" name=\"v_reseni\" onChange=\"self.document.forms.form3.submit()\" >";
	 
	   echo "<option value=\"0\" "; if( ( $v_reseni == "0" or !isset($v_reseni) ) ){ echo " selected "; } echo " >Ne</option>
		<option value=\"1\" "; if($v_reseni == "1"){ echo " selected "; } echo " >Ano</option>";
	 }
	 	
	} //
	else
	{
	 echo "<select size=\"2\" name=\"v_reseni\" onChange=\"self.document.forms.form3.submit()\" >";  
	 
	 echo "<option value=\"0\" "; if( ($v_reseni == "0" or !isset($v_reseni) )){ echo " selected "; } echo " >Ne</option>
	    <option value=\"1\" "; if($v_reseni == "1"){ echo " selected "; } echo " >Ano</option>";
	}
	  
	echo "</select></td>
        <td colspan=\"2\" >
		<span style=\"padding-left: 10px; padding-right: 20px; \">Vyřešeno: </span>
        </td>
	
	<td>";
	
	if ( $id_predchozi_opravy > 0 )
	{
	 $dotaz_vyreseno = mysql_query("SELECT * FROM opravy WHERE id_predchozi_opravy = '$id_predchozi_opravy' ");
	 
	 while ($data_vyreseno=mysql_fetch_array($dotaz_vyreseno) )
	 { 
	   if ( $data_vyreseno["vyreseno"] == 1 ){ $vyreseno_db= "1"; }
	 }
	 
	 if ( $vyreseno_db == "1" )
	 {
	    echo "<select size=\"1\" name=\"vyreseno\" onChange=\"self.document.forms.form3.submit()\" >";
	
	    echo "<option value=\"1\" >Ano (nelze změnit)</option>";
	 } 
	 else
	 {  
	  echo "<select size=\"2\" name=\"vyreseno\" onChange=\"self.document.forms.form3.submit()\" >";
	  echo "<option value=\"0\" "; if( ( $vyreseno == "0" or !isset($vyreseno) )){ echo " selected "; } echo " >Ne</option>
	    <option value=\"1\" "; if($vyreseno == "1"){ echo " selected "; } echo " >Ano</option>";
	 }
	 
	}
	else
	{
	  echo "<select size=\"2\" name=\"vyreseno\" onChange=\"self.document.forms.form3.submit()\" >";
	  echo "<option value=\"0\" "; if( ( $vyreseno == "0" or !isset($vyreseno) )){ echo " selected "; } echo " >Ne</option>
	    <option value=\"1\" "; if($vyreseno == "1"){ echo " selected "; } echo " >Ano</option>";
	} 
	
	 echo "
	  </select>  

	</td>
       </tr>";

echo "<tr><td colspan=\"".$pocet_bunek."\" ><br></td></tr>";

echo "<tr><td colspan=\"".$pocet_bunek."\" ><textarea name=\"text\" cols=\"100\" rows=\"6\" >".$text."</textarea></td></tr>";

echo "<tr><td colspan=\"".$pocet_bunek."\" ><br></td></tr>";

echo "<tr>
	<td><br></td>
	<td colspan=\"4\" ><input type=\"submit\" name=\"odeslano\" value=\"OK\"></td>
      </tr>";

echo "</table>";

echo "</form>";

} // konec else isset odeslano

} // konec if ( typ == 1 )
elseif ( $typ == 2)
{
// vypis 

$v_reseni_filtr = $_GET["v_reseni_filtr"];
$vyreseno_filtr = $_GET["vyreseno_filtr"];

if ( !isset($v_reseni_filtr) ){ $v_reseni_filtr="0"; }
if ( !isset($vyreseno_filtr) ){ $vyreseno_filtr="99"; }

$pocet_bunek="11";

// echo "<table border=\"0\" width=\"100%\" >";

echo "<table border=\"0\" width=\"\" align=\"center\" style=\"font-size: 12px; font-family: Verdana;  \" >";

$limit="1000";

include("opravy-vypis-inc.php");

}

?>

 <!-- konec vlastniho obsahu -->
  </td>
  </tr>
  
 </table>

</body>
</html>

<?php

global $cesta;

$cesta = "../";

include ($cesta."include/config.php"); 
include ($cesta."include/check_login.php");

include ($cesta."include/check_level.php");

if ( !( check_level($level,121) ) )
{
// neni level

 $stranka=$cesta.'nolevelpage.php';
 header("Location: ".$stranka);
 
  echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
  exit;
      
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

include ($cesta."include/charset.php"); 

?>

<title>Adminator 2 - VoIP hovory</title> 

</head> 

<body> 

<? include ($cesta."head.php"); ?> 

<? include ($cesta."category.php"); ?> 

 
 <tr>
 <td colspan="2" bgcolor="silver" height=""><? include("voip-subcat-inc.php"); ?></td>
  </tr>
 
  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->
  
 <?
 
 // global $rs;
  $vlastnik_hledani = $_POST["vlastnik_hledani"];
  $cislo = $_POST["cislo"];
  
  $send = $_POST["send"];
  
  $typ_systemu = $_POST["typ_systemu"];
  if( $typ_systemu < 1 ){ $typ_systemu = $_GET["typ_systemu"]; }
  
  $id_vlastnika = $_POST["id_vlastnika"];
  $odeslat = $_POST["odeslat"];

  $update_id = $_GET["update_id"];
  if( $update_id < 1 ){ $update_id = $_POST["update_id"]; }
   
  echo "<div style=\"padding-top: 10px; \">
	<span style=\"font-weight: bold; font-size: 18px; border-bottom: 1px solid grey; \" >Voip systém</span>";
	
	if( $update_id > 0 )
	{ echo "<span style=\"font-size: 16px; margin-bottom: 20px; border-bottom: 1px solid grey; \"> - Úprava </span>"; }
	else
	{ echo "<span style=\"font-size: 16px; margin-bottom: 20px; border-bottom: 1px solid grey; \"> - Přidání čísla </span>"; }
 
 echo "</div>";
 
  echo "<div style=\"padding-top: 20px; padding-left: 5px; padding-bottom: 20px; \">";

 //PRVNE TEST PROMENNYCH
 //if( !(is_int($id_vlastnika)) )
 
 if( isset($update_id) )
 {
  if( !( ereg('^([[:digit:]])+$',$update_id) ) )  
  { $error .= "<div>Chyba! Update id není ve správném formátu. </div>"; }
 }
 
 if( !( ereg('^([[:digit:]])+$',$id_vlastnika) ) )  
 { $error .= "<div>Chyba! Id vlastníka není ve správném formátu. </div>"; }
 
 if( !( ereg('^([[:digit:]])+$',$typ_systemu) ) )  
 { $error .= "<div>Chyba! Typ systému není ve správném formátu. </div>"; }
 
 if( !( ereg('^([[:digit:]])+$',$cislo) ) )  
 { $error .= "<div>Chyba! Číslo není ve správném formátu. </div>"; }
 
 if( ( isset($odeslat) and !isset($error) ) )
 {
  if( !isset($update_id) )
  {
    // echo "ukladani";
 
   $add=mysql_query("INSERT INTO voip_cisla (cislo, id_vlastnika, typ_systemu)
                               VALUES ('$cislo','$id_vlastnika','$typ_systemu') ");

   if ($add){ echo "<br><span style=\"color: green; font-size: 18px; \">Záznam úspěšně vložen.</span><br><br>"; }
   else { echo "<span style=\"color: red; font-weight: bold; font-size: 16px; \">Záznam nelze vložit do databáze. </span>"; }
    
   //odkaz zpet
   echo "<div><a href=\"voip-cisla.php?typ_systemu=".$typ_systemu."\" >Zpět</a></div>";
   
   //vlozeni do archivu zmen
   $pole2 = "<b>akce: pridani voip cisla; </b><br>"; 
   $pole2 .= "[cislo] => ".$cislo.", [typ_systemu] => ".$typ_systemu.", [id_vlastnika] => ".$id_vlastnika.". ";
   
   if ( $add == 1){ $vysledek_write="1"; }

  } // konec if isset update_id
  else
  {
   
   $uprava=mysql_query("UPDATE voip_cisla SET cislo='$cislo', id_vlastnika='$id_vlastnika' where id_cisla='".$update_id."' Limit 1 ");

   if ($uprava){ echo "<br><span style=\"color: green; font-size: 18px; \">Záznam úspěšně upraven.</span><br><br>"; }
   else { echo "<span style=\"color: red; font-weight: bold; font-size: 16px; \">Záznam nelze upravit v databázi. </span>"; }

   if ( $uprava == 1 ){ $vysledek_write="1"; }

   //odkaz zpet
   echo "<div><a href=\"voip-cisla.php?typ_systemu=".$typ_systemu."\" >Zpět</a></div>";
   
   //vlozeni do archivu zmen
   $pole2 = "<b>akce: uprava voip cisla; </b><br>"; 
   $pole2 .= "[cislo] => ".$cislo.", [typ_systemu] => ".$typ_systemu.", [id_vlastnika] => ".$id_vlastnika.". ";
   	    	    
  }// konec else if isset update_id

    $add_2=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole2','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write')");
    				   
 } // konec if isset odeslano and ! isset error
 else
 { //zobrazeni formu
 
    if( ( isset($update_id) and ( $send != "yes" ) ) )
    {
      //nacteni predchozich udaju
      $dotaz = mysql_query("SELECT * FROM voip_cisla WHERE id_cisla = '$update_id' ");
    
      while( $data = mysql_fetch_array($dotaz) )
      { 
	$id_cisla = $data["id_cisla"];
	$cislo = $data["cislo"]; 
        $id_vlastnika = $data["id_vlastnika"]; 
	$typ_systemu = $data["typ_systemu"];     
      }
     
    }
    
    if( isset($odeslat) ){ echo $error; }
    
    echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\" name=\"form3\" >";
    
    echo "<input type=\"hidden\" name=\"item\" value=\"".$item."\" >";
    
    if( isset($update_id) ){ echo "<input type=\"hidden\" name=\"update_id\" value=\"".$update_id."\" >"; }
    
    echo "<table border=\"1\" >
    	    
	    <tr>
	     <td>Telefonní číslo: </td>
	     <td><input type=\"text\" name=\"cislo\" value=\"".$cislo."\" ></td>
	    </tr>
	    
	    <tr><td><br></td></tr>
	    
	    <tr>
	      <td >Typ systému: </td>
	      <td >
		<select name=\"typ_systemu\" >
		 <option value=\"0\" class=\"select-nevybrano\" >Nevybráno</option>
		 <option value=\"1\" ";	if( $typ_systemu == 1){ echo " selected "; } echo ">Net4Net</option>
		</select>
	    </td>
	    
	    </tr>

	    <tr><td><br></td></tr>    
	";

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

              if( ( $id_vlastnika == $data2["id_cloveka"]) ){ echo " selected  "; }

              echo " >";

              echo " ".$data2["prijmeni"].", ".$data2["jmeno"]."  ( ".$data2["nick"]."  )  ";
              echo " ".$data2["ulice"].", VS: ".$data2["vs"];

              echo "</option> \n ";

              endwhile;

        }

echo "    </select>
        </td>

    </tr>
    
    
    <tr><td><br></td></tr>
	    
    ";

echo "<tr>
        <td colspan=\"\">Výběr vlastníka - hledání:</td>
        <td><input type=\"text\" size=\"40\" name=\"vlastnik_hledani\" value=\"".$vlastnik_hledani."\" >
            <span style=\"color: grey; font-weight: bold; \" >
                <span style=\"padding-left: 10px; \" ><input type=\"submit\" name=\"hledat\" value=\"Hledat\" ></span>
                <span style=\"padding-left: 10px; \" >hledaný výraz:</span>
            <span style=\"color: #555555\">".$sql." </span></span>
        </td>
      </tr>

    <tr><td><br></td></tr>
    
    <tr>
      <td><br></td>
      <td>
      <input type=\"hidden\" name=\"send\" value=\"yes\" >
      <input type=\"submit\" name=\"odeslat\" value=\"OK\" ></td>
    </tr>
    ";

    echo "</form>";
  } // konec else isset odeslano
  
   echo "</div>";
 
 ?> 
  <!-- konec vlastniho obsahu -->  
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 


<?php

global $cesta;

$cesta = "../";

include ($cesta."include/config.php"); 
include ($cesta."include/check_login.php");

include ($cesta."include/check_level.php");

if ( !( check_level($level,133) ) )
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

<title>Adminator 2 - VoIP čísla</title> 

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
  
  //$send = $_POST["send"];
  
  //$id_vlastnika = $_POST["id_vlastnika"];
  $odeslat = $_POST["odeslat"];

  $erase_id = $_GET["erase_id"];
  if( $erase_id < 1 ){ $erase_id = $_POST["erase_id"]; }
   
  echo "<div style=\"padding-top: 10px; \">
	<span style=\"font-weight: bold; font-size: 18px; border-bottom: 1px solid grey; \" >Voip systém</span>
	<span style=\"font-size: 16px; margin-bottom: 20px; border-bottom: 1px solid grey; \"> - Smazání </span>"; 
 
 echo "</div>";
 
 echo "<div style=\"padding-top: 20px; padding-left: 5px; padding-bottom: 20px; \">";

 //PRVNE TEST PROMENNYCH
 //if( !(is_int($id_vlastnika)) )
 
 if( isset($erase_id) )
 {
  if( !( ereg('^([[:digit:]])+$',$erase_id) ) )  
  { $error .= "<div>Chyba! Erase id není ve správném formátu. </div>"; }
 }
 
 if( ( isset($odeslat) and !isset($error) ) )
 {
  //if( !isset($erase_id) )
  {
    // echo "ukladani";

      //nacteni predchozich udaju
      $dotaz2 = mysql_query("SELECT * FROM voip_cisla WHERE id_cisla = '$erase_id' ");
      while( $data2 = mysql_fetch_array($dotaz2) )
      { 
	$id_cisla = $data2["id_cisla"];
	$cislo = $data2["cislo"]; 
        $id_vlastnika = $data2["id_vlastnika"]; 
	$typ_systemu = $data2["typ_systemu"];     
      } 
 
   $erase=$conn_mysql->query("DELETE FROM voip_cisla WHERE id_cisla = '$erase_id' LIMIT 1 ");

   if ($erase){ echo "<br><span style=\"color: green; font-size: 18px; \">Záznam úspěšně vymazán.</span><br><br>"; }
   else { echo "<span style=\"color: red; font-weight: bold; font-size: 16px; \">Záznam nelze smazat. </span>"; }
   
   //odkaz zpet
   echo "<br><br><div><a href=\"voip-cisla.php?typ_systemu=".$typ_systemu."\" >Zpět</a></div>";
       
   //vlozeni do archivu zmen
   $pole2 = "<b>akce: smazani voip cisla; </b><br>"; 
   $pole2 .= "[erase_id] => ".$erase_id.", [cislo] => ".$cislo.", [typ_systemu] => ".$typ_systemu;
   $pole2 .= ", [id_vlastnika] => ".$id_vlastnika.". ";
   
   if ( $erase == 1){ $vysledek_write=1; }

  } // konec if isset update_id
 
    $add_2=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole2','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write')");
    				   
 } // konec if isset odeslano and ! isset error
 else
 { //zobrazeni formu
 
    if( ( isset($erase_id) and ( $send != "yes" ) ) )
    {
      //nacteni predchozich udaju
      $dotaz = mysql_query("SELECT * FROM voip_cisla WHERE id_cisla = '$erase_id' ");
    
      while( $data = $dotaz->fetch_array() )
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
    
    if( isset($erase_id) ){ echo "<input type=\"hidden\" name=\"erase_id\" value=\"".$erase_id."\" >"; }
    
    echo "<table border=\"1\" >

	    <tr>
	     <td colspan=\"2\" ><span style=\"font-size: 18px; font-weight: bold; \" >
	     Opravdu smazat následující číslo? </span>
	     </td>
	    </tr>
    	    
	    <tr><td><br></td></tr>
	    
	    <tr>
	     <td>Telefonní číslo: </td>
	     <td><input type=\"text\" name=\"cislo\" value=\"".$cislo."\" ></td>
	    </tr>
	    
	    <tr><td><br></td></tr>
	    
	    <tr>
	      <td >Typ systému: </td>
	      <td >
		<select name=\"typ_systemu\" >
		 <option value=\"1\" ";	if( $typ_systemu == 1){ echo " selected "; } echo ">Net4Net</option>
		</select>
	    </td>
	    
	    </tr>

	    <tr><td><br></td></tr>    

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


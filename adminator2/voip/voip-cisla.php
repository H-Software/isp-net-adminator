<?php

global $cesta;

$cesta = "../";

require ($cesta."include/main.function.shared.php");
require ($cesta."include/config.php"); 
require ($cesta."include/check_login.php");
require ($cesta."include/check_level.php");

if ( !( check_level($level,120) ) )
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
  
 <?php
 
  //include("../include/class.voip.main.php");
 
  $typ_systemu = $_POST["typ_systemu"];
  $stav_cisla = $_POST["stav_cisla"];
 
  if ( $stav_cisla == "0" ){ $stav_cisla = "1"; }
  
  if ( $stav_cisla == "99" ){ $stav_cisla = "1"; }
  
  
  $cas1 = explode(" ", microtime()); 
  $cas1 = $cas1[1] + $cas1[0]; 
  $rd = "10000"; /* zaokrouhlování */
 
  //if( ($typ_systemu == 2) and ( !isset($stav_cisla) ) ){ $stav_cisla = "1"; }
  
  if( (strlen($typ_systemu) < 1 ) ){ $typ_systemu = $_GET["typ_systemu"]; }
  
  echo "<div style=\"padding-top: 10px; \">
	<span style=\"font-weight: bold; font-size: 18px; border-bottom: 1px solid grey; \" >Voip systém</span>
	
	<span style=\"border-bottom: 1px solid grey; \"> - Telefonní čísla</span>

       </div>";
 
  echo "<div style=\"padding-top: 10px; padding-left: 5px; padding-bottom: 20px; \">";
  
  echo "<form action=\"\" method=\"POST\" name=\"form4\" >
	<input type=\"hidden\" name=\"item\" value=\"".$item."\" >";
  
  echo "<div style=\"padding-bottom: 5px; \">
	 <span style=\"color: grey; font-weight: bold; \">Filtr:</span>

	 <span style=\"padding-left: 10px; \">Typ systému:</span>

	 <span style=\"padding-left: 10px; \">
	   <select size=\"1\" name=\"typ_systemu\" onChange=\"self.document.forms.form4.submit()\"> >
	     <!-- <option value=\"0\" class=\"select-nevybrano\" >Nevybráno</option> -->
	     
	     ";
	
//<!--	     <option value=\"1\" -->"; //if( $typ_systemu == 1){ echo " selected "; } echo ">Net4Net(DealerSoft)</option>

    echo "<option value=\"2\" "; if( $typ_systemu == 2){ echo " selected "; } echo ">DialTelecom(PortaOne)</option>
	     
	   </select>
	 </span>

	 <span style=\"padding-left: 10px; \">Stav čísla:</span>

	 <span style=\"padding-left: 10px; \">
	    <select name=\"stav_cisla\" size=\"1\" >";
	    
	if( $typ_systemu == 2)
	{ 
	  echo "<option value=\"0\" "; if( $stav_cisla == 0){ echo " selected "; } 
	    echo " class=\"select-nevybrano\" >Nevybráno</option>"; 
	  
	  echo "<option value=\"99\" "; if( $stav_cisla == 99){ echo " selected "; } echo " >Vše</option>"; 
	  
	    
	  echo "<option value=\"1\" "; if( $stav_cisla == 1){ echo " selected "; } echo " >Otevřeno</option>"; 
	  echo "<option value=\"2\" "; if( $stav_cisla == 2){ echo " selected "; } echo " >Ukončeno</option>"; 
	}
	else
	{ echo "<option value=\"0\" class=\"select-nevybrano\" >Není dostupné</select>"; }
	
	echo " </select>    
	 </span>

	 <span style=\"padding-left: 10px; \"><input type=\"submit\" name=\"odeslano\" value=\"OK\"></span>

	</div></form>";

  
  if( $typ_systemu == 2)
  { 
  
    echo "<div style=\" padding-bottom: 10px; \" >
	    <a href=\"voip-online-dial-account-add.php\" >Přidání čísla</a></div>"; 
  
    echo "<div style=\"font-weight: bold; padding-bottom: 5px; \" >Výpis čísel: </div>"; 
  
    if ( $stav_cisla == "99" ){ $stav_cisla = "0"; }
  
    system("/var/www/cgi-bin/cgi-adm2/account_list.pl 0 ".$stav_cisla);

    $cas2 = explode(" ", microtime()); 
    $cas3 = (round((($cas2[1] + $cas2[0]) - $cas1) * $rd)) / $rd;    
   
    echo "<div >".$cas3."</div>";
    
  }
  else
  {
   echo "<div style=\"font-weight: bold; \">Prosím vyberte typ systému. </div>";
  }
  
  echo "</div>";
 
 ?> 
  <!-- konec vlastniho obsahu -->  
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 


<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,106) ) )
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

$id_opravy=$_GET["id_opravy"];

$ok=$_POST["ok"];

$pocet_sloupcu = "2";

if ( $ok == "OK" )
{
 // budeme ukladat

 $id_opravy=$_POST["id_opravy"];

 // echo "budeme ukladat ... ";

 echo "<br>";
	
	 $uprava=$conn_mysql->query("UPDATE opravy SET v_reseni='1', v_reseni_kym = '" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "' where id_opravy = '$id_opravy' Limit 1 ");
	 
         if ($uprava){ echo "<br><span style=\"color: green; font-size: 18px; \">Záznam úspěšně upraven.</span><br><br>"; }
          else { echo "<span style=\"color: red; font-weight: bold; font-size: 16px; \">Záznam nelze upravit v databázi. </span>"; }

         $datum = strftime("%d/%m/%Y %H:%M:%S", time());

        // pridame to do archivu zmen

//        $pole .= "<br>aktuální data: nazev: ".$nazev.", ip adresa: ".$ip_adresa.", monitoring: ".$monitoring.", alarm: ".$alarm;
//        $pole .= ", parent_router: ".$parent_router.", mac: ".$mac;

//        $pole .= ",<br> akci provedl: ".\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email.", vysledek akce dle mysql: ".$uprava.", datum akce: ".$datum;

//        $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce) VALUES ('$pole')");

}
else
{

//echo "id opravy: ".$id_opravy;

echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\" >";

echo "<table border=\"0\" width=\"50%\" >";

echo "<tr><td colspan=\"".$pocet_sloupcu."\" ><br></td></tr>";

echo "<tr><td colspan=\"".$pocet_sloupcu."\" ><span style=\"font-size: 18px; font-weight: bold; \">Opravdu začít řešit následující závadu/opravu ?</td></tr>";

echo "<tr><td colspan=\"".$pocet_sloupcu."\" ><br></td></tr>";

$dotaz=$conn_mysql->query("SELECT * FROM opravy WHERE id_opravy = '$id_opravy' ");
$dotaz_radku= $dotaz->num_rows;

if ( $dotaz_radku <> 1)
{ 
    echo "Chyba! Nelze vybrat pozadovanou zavadu/opravu! ";
    exit;
}

while($data= $dotaz->fetch_array() )
{

$id_opravy=$data["id_opravy"];
$id_vlastnika=$data["id_vlastnika"];
$text=$data["text"];

}

echo "<tr>
	<td> id opravy: </td>
	<td>".$id_opravy."</td>
      </tr>";

echo "<tr>
	<td> id vlastníka: </td>
	<td>".$id_vlastnika."</td>
      </tr>";

echo "<tr>
	<td> text: </td>
	<td>".$text."</td>
      </tr>";

echo "<tr><td colspan=\"".$pocet_sloupcu."\" ><br></td></tr>";

echo "<tr>
	<td colspan=\"".$pocet_sloupcu."\" align=\"center\" >
	<input type=\"hidden\" name=\"id_opravy\" value=\"".$id_opravy."\" >
	
	<input type=\"submit\" name=\"ok\" value=\"OK\" ></td></tr>";

echo "</table>";

echo "</form>";

} // konec else

?>
 <!-- konec vlastniho obsahu -->
  </td>
  </tr>
  
 </table>

</body>
</html>

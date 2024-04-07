<? 

global $cesta;

$cesta = "../";

include ($cesta."include/config.php"); 
include ($cesta."include/check_login.php");

include ($cesta."include/check_level.php");

if ( !( check_level($level,110) ) ) 
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

echo "<div style=\"font-size: 20px; font-weight: bold; padding-top: 20px; padding-bottom: 20px; \" >
	Úprava neuhrazených faktur </div>";


$akce=$_GET["akce"];

if ( $akce == "ignorovat" )
{
// prepinani ignorace faktury
echo "<div style=\"padding-bottom: 10px; font-weight: bold; \">Zvolená akce: Přepínání ignorace faktury</div>";

 while ( list($id_faktury, $hodnota) = each($_GET) )
 {

  if ( ( $id_faktury != "akce" and $hodnota >= 0) )
  {

   $uprava=mysql_query("UPDATE faktury_neuhrazene SET ignorovat='$hodnota' WHERE id=".$id_faktury." Limit 1 ");

   echo "id polozky: ".$id_faktury.", hodnota: ".$hodnota.", ulozeno: ";
   if( $uprava == 1){ echo "Ano"; }
   else
   { 
     echo "Ne";
     echo ", chyba: ".mysql_error();
   }
   
   echo "<br>";

  } // konec if id_polozky != rucni_ok

 } // konec while
 
} // konec if akce == ignorovat
else
{
//form pro zvoleni akce

// echo "akce: $akce";

$pocet_sloupcu="3";

echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"GET\">

    <table border=\"1\">
    
    <tr>
	<td>Zvolte akci: </td>

	<td width=\"40px; \" >&nbsp;</td>
	
	<td>Vyberte fakturu: </td>
        
    </tr>
    <tr><td colspan=\"".$pocet_sloupcu."\" ><br></td></tr>
    
    <tr>
	<td>
	    <select name=\"akce\" size=\"1\" >
		<option value=\"0\" class=\"select-nevybrano\" >Nevybráno</option>
		<option value=\"ingorovat\">ignorování faktury</option>
	    </select>
	</td>

	<td>&nbsp;</td>
	
	<td>
	    <select name=\"id_faktury\" size=\"1\" >
	    
	    <option value=\"0\" class=\"select-nevybrano\" >Nevybráno</option>";
	    
	    $dotaz_faktury = mysql_query("SELECT * FROM faktury_neuhrazene ");
	    //$dotaz_faktury_radku
	    
	    while( $data_faktury = mysql_fetch_array($dotaz_faktury) )
	    {
		echo "<option value=\"".$data_faktury["id"]."\">".$data_faktury["id"].",";
		echo " č.f.: ".$data_faktury["Cislo"]."</option>";
	    
	    }
	    
	    echo "
	    <option value=\"xxx\">další</option>
	    
	    </select>
	</td>

    </tr>
    
    <tr><td colspan=\"".$pocet_sloupcu."\" ><br></td></tr>
    
    <tr>
	<td colspan=\"".$pocet_sloupcu."\" align=\"center\" >
	    <input type=\"submit\" name=\"odeslano\" value=\"OK\" >
	</td>
    </tr>
    
    </table>
    
    </form>";

}

?>

<!-- konec vlastniho obsahu -->
 </td>
  </tr>
  
 </table>

</body> 
</html> 


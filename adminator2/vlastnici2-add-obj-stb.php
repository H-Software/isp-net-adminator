<?php

require("include/main.function.shared.php");
include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if( !( check_level($level,138) ) )
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

<title>Adminator 2 - přiřazení objektu STB</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 <tr>
  <td colspan="2" height="50"> přiřazení objektu: </td>
 </tr>
 
 <tr>
  <td colspan="2"><br></td>
 </tr>
  
 <tr>
  <td valign="top" colspan="2">
  
  <?
  
  $send=$_GET["send"];
  $objekt=$_GET["objekt"];
  $id_vlastnika=$_GET["id_vlastnika"];
  
  if ( isset($send) )
  {
  // tady to ulozime
  
    if ( !( ereg('^([[:digit:]]+)$',$objekt) ) ){ echo " Špatný formát proměnné objekt"; exit; }
    if ( !( ereg('^([[:alnum:]]+)$',$id_vlastnika) ) ){ echo " Špatný formát proměnné id_komplu"; exit; }
  
    $pole3 = "<b>akce: prirazeni objektu typu STB k vlastnikovi; </b><br>";

    $res = mysql_query("UPDATE objekty_stb SET id_cloveka = '$id_vlastnika' WHERE id_stb = '$objekt' Limit 1 ");
							      	 
    if( $res ){ echo "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3>\n"; }
    else { echo "<div style=\"color: red; \">Chyba! Data v databázi nelze změnit. </div><br>\n"; }
		   
     $pole3 .= " [id_stb]=> ".$objekt.", [id_vlastnika]=> ".$id_vlastnika;
     // $pole3 .= ", akci provedl: ".$nick.", vysledek akce dle postgre: ".$res.", datum provedeni akce: ".$datum;
     
     if( $res == 1){ $vysledek_write="1"; }
     $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole3','$nick','$vysledek_write')");
      
  }
  else
  {
    
    $dotaz = mysql_query("SELECT * FROM objekty_stb WHERE id_cloveka is NULL ORDER BY popis");
    $dotaz_radku =mysql_num_rows($dotaz);
  
    if( $dotaz_radku == 0 )
    {
	echo "<br><br>Žádné objekty typu STB k přiřazení! <br><br>";
    }
    else
    {
	echo "<form method=\"get\" action=\"".$_SERVER["PHP_SELF"]."\" >";
	echo "Vyberte objekt STB k přiřazení: <br><br>";
	echo "<select name=\"objekt\" size=\"15\" >";
      
	while ($data2=mysql_fetch_array($dotaz) )
	{
  
	    echo "<option value=\"".$data2["id_stb"]."\"> ";
	    echo $data2["popis"]." --  ".$data2["mac_adresa"]."  </option>";
  
	}
	
	echo "</select>";
  
	echo "<br><br><br><input type=\"hidden\" name=\"id_vlastnika\" value=\"".$_GET["id_vlastnika"]."\" >";
  
	echo "<input type=\"submit\" value=\"OK\" name=\"send\" >";
  
	echo "</form>";
  
     } // konec else jestli je radku nula
  
  } // konec else jestli zobratzujeme nebo ukladame
  
  
  ?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 


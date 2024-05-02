<?php

require_once("include/config.php"); 
require_once("include/check_login.php");

require_once("include/check_level.php");



if( !( check_level($level,152) ) )
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

require ("include/charset.php"); 

?>

<title>Adminator 2 - objekty stb</title> 

</head> 
<body> 

<?php require("head.php"); ?> 
<?php require("category.php"); ?> 
 
 <tr>
  <td colspan="2" height="20" bgcolor="silver" >
   <?php require("objekty-subcat-inc.php"); ?>
   </td>
  </tr>
        
  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->

<?php
 
 $pocet_sloupcu = "8";
 
 echo "<div style=\"padding-top: 15px; padding-bottom: 15px; \" >
	<span style=\" padding-left: 5px; 
	font-size: 16px; font-weight: bold; \" >
	.:: Odpárování Set-Top-Boxu ::. </span> 
       </div>";
       
 $id_stb = intval($_GET["id"]);

 $odeslano = $_GET["odeslano"];
 
 if( isset($odeslano) )
 {
    //povrzeno, takze odpárovat

    echo "<div style=\"padding: 10px; \">";
     
    $rs = $conn_mysql->query("UPDATE objekty_stb SET id_cloveka = NULL WHERE id_stb = '".intval($id_stb)."' LIMIT 1");
    
    if( $rs === true){
    
	$vysledek_write=1;
	echo "<div style=\"color: green; font-weight: bold;\">SetTopBox úspešně odpárován.</div>";
    }
    else{
	echo "<div style=\"color: red; font-weight: bold;\">SetTopBox nelze odpárovat, vyskytla se chyba.</div>";
	
	//echo mysql_error();
    }
    
    //ulozit do archivu zmen
    $az_akce = "<b> akce: odparovani stb objektu ; </b><br>";
    
    $az_akce .= " <b>[id_stb]</b> => ".$id_stb."";
    
    $rs_az_add = $conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
                            "('".$conn_mysql->real_escape_string($az_akce)."',".
                            "'".$conn_mysql->real_escape_string(\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email)."',".
                            "'".intval($vysledek_write)."')");
   
    if( $rs_az_add === true){
	echo "<div style=\"padding-top: 10px; color: green; font-weight: bold;\">Záznam úspešně vložen do archivu změn.</div>";
    }
    else{
	echo "<div style=\"padding-top: 10px; color: red; font-weight: bold;\">Chyba při vkládání do archivu změn.</div>";
	
	//echo mysql_error();
    }
    
    echo "</div>";
 }
 else
 { //zobrazit potvrzujici dialog
 
   echo "<br><span style=\"font-size: 20px; \">Opravdu chcete odřadit tento objekt: </span><br><br>";

  $rs = $conn_mysql->query("SELECT popis, ip_adresa FROM objekty_stb WHERE id_stb = '".intval($id_stb)."' ");
  $rs_radku = mysql_num_rows($rs);

  if( $rs_radku == 0 )
  {
    echo "<br>Chyba! Nelze nacist puvodni data o objektu! <br>";
  }
  else
  {
    echo "<form action=\"\" method=\"GET\" >";

    while( $data=mysql_fetch_array($rs) )
    {

	echo "<b>dns jméno</b>: ".htmlspecialchars($data["popis"])."<br><br>";

	echo "<b>ip adresa</b>: ".htmlspecialchars($data["ip_adresa"])."<br>";

    } // konec while

    echo "<input type=\"hidden\" name=\"id\" value=\"".intval($id_stb)."\" > ";

    echo "<br><br><input type=\"submit\" value=\"OK\" name=\"odeslano\" >";

    echo "</form>";

  } // konec jestli jestli je radku nula
 
 }
 
?>
 <!-- konec vlastniho obsahu -->
  </td>
  </tr>
  
 </table>

</body>
</html>

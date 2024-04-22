<?php

require("include/main.function.shared.php");
require("include/config.php"); 
include ("include/check_login.php");
include ("include/check_level.php");

if ( !( check_level($level,48) ) )
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

<title>Adminator 2 - přiřazení objektu</title> 

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
  
    if ( !( preg_match('/^([[:digit:]]+)$/',$objekt) ) ){ echo " Špatný formát proměnné objekt"; exit; }
    if ( !( preg_match('/^([[:alnum:]]+)$/',$id_vlastnika) ) ){ echo " Špatný formát proměnné id_komplu"; exit; }
  
    $pole3 = "<b>akce: prirazeni objektu k vlastnikovi; </b><br>";

     $obj_upd = array( "id_cloveka" => $id_vlastnika );     
     $obj_id = array( "id_komplu" => $objekt );
     
     $res = pg_update($db_ok2, 'objekty', $obj_upd, $obj_id);
	 
    if ($res) { echo "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3>\n"; }
    else { echo "<div style=\"color: red; \">Chyba! Data v databázi nelze změnit. </div><br>\n".pg_last_error($db_ok2); }
		   
     $pole3 .= " [id_komplu]=> ".$objekt.", [id_vlastnika] => ".$id_vlastnika;
     // $pole3 .= ", akci provedl: ".$nick.", vysledek akce dle postgre: ".$res.", datum provedeni akce: ".$datum;
     
     if ( $res == 1){ $vysledek_write="1"; }
      
     $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole3','$nick','$vysledek_write')");
      
  }
  else
  {
    if( $_GET["mod"] == 1 )
    {
     //pouze wifi objekty k vlastnikumFO ..
     
     //vyber dle tarifu
     $dotaz_f = $conn_mysql->query("SELECT * FROM tarify_int WHERE typ_tarifu = '0' ");
    
     $i = 0;
	
     while( $data_f = $dotaz_f->fetch_array() )
     {
        if( $i == 0 ){ $tarif_sql .= "AND ( "; }
        if( $i > 0 ){ $tarif_sql .= " OR "; }
	  
        $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";
	       
        $i++;
     }
     if( $i > 0 ){ $tarif_sql .= " ) "; }
					    
    }
    
    $dotaz = pg_query("SELECT * FROM objekty WHERE id_cloveka is NULL ".$tarif_sql." order by dns_jmeno");
    $dotaz_radku = pg_num_rows($dotaz);
  
    if( $dotaz_radku == 0 )
    {
	echo "<br><br>Žádné objekty k přiřazení! <br><br>";
    }
    else
    {
	echo "<form method=\"get\" action=\"".$_SERVER["PHP_SELF"]."\" >";
	echo "Vyberte objekt k přiřazení: <br><br>";
	echo "<select name=\"objekt\" size=\"15\" >";
      
	while ($data2=pg_fetch_array($dotaz) )
	{
  
	    echo "<option value=\"".$data2["id_komplu"]."\"> ";
	    echo $data2["dns_jmeno"]." --  ".$data2["ip"]."  </option>";
  
	}
  
  
	echo "</select>";
  
	echo "<br><br><br> <input type=\"hidden\" name=\"id_vlastnika\" value=\"".$_GET["id_vlastnika"]."\" >";
  
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


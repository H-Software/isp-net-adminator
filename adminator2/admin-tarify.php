<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,131) ) )
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

<title>Adminator 2 - nastavení </title> 

</head> 
<body> 

<?php include ("head.php"); ?> 

<?php include ("category.php"); ?> 

 <tr>
  <td colspan="2" height="" bgcolor="silver">
  <?php include("admin-subcat-inc.php"); ?>
 </tr>

 <tr>
  <td colspan="2">
 <?php
 
  $update_id = $_GET["update_id"];
  $erase_id = $_GET["erase_id"];
  
  echo "<div style=\"padding-top: 10px; padding-bottom: 10px; font-weight: bold; font-size: 16px; \">
	Nastavení tarifů";
	
  if( isset($update_id) ){ echo "  - Úprava"; }
  elseif( isset($erase_id) ){ echo "  - Smazání"; }
  else{ echo "  - Výpis"; }
  
  echo "</div>";
  
  //kontrola promennych zde ...
  
  if( isset($update_id) )
  {
    if( !( ereg('^([[:digit:]])+$',$update_id) ) )
    { $error .= "<div>Chyba! Update id není ve správném formátu. </div>"; }
  }

  if( isset($erase_id) )
  {
    if( !( ereg('^([[:digit:]])+$',$erase_id) ) )
    { $error .= "<div>Chyba! Erase id není ve správném formátu. </div>"; }
  }
  
  if( isset($update_id) )
  {
    if( isset($send) )
    {
     //budeme ukladat ..
     echo "budeme ukladat ...";
    
    }
    else
    {
      //zobrazeni formu pro update ...
      echo "zobrazeni formu pro update ....";
    
    }
    
  } //konec if isset update_id
  elseif( isset($erase_id) )
  {
    if( isset($send) )
    {
     //budeme ukladat ..
     echo "budeme mazat ...";
    
    }
    else
    {
      //zobrazeni formu pro erase ...
      echo "zobrazeni formu pro erase ....";
    
    }
    
  } //konec if isset erase_id
  else
  {
   //mod vypis ...
   
  echo "<table border=\"0\" width=\"1000px\" >";
  
  $style1 = "border-bottom: 2px solid black; border-right: 1px dashed gray; ";
  $style2 = "border-bottom: 1px solid gray; border-right: 1px dashed gray; ";
  
  echo "
	<tr>
	  <td style=\"".$style1."\"><b>id tarifu</b></td>
	  <td style=\"".$style1."\"><b>zkratka</b></td>
	  <td style=\"".$style1."\"><b>název</b></td>
	  <td style=\"".$style1."\"><b>typ</b></td>
	  <td style=\"".$style1."\"><b>garant</b></td>
	  
	  <td style=\"".$style1."\"><b>cena bez DPH</b></td>
	  <td style=\"".$style1."\"><b>cena s DPH</b></td>
	  
	  <td style=\"".$style1."\"><b>Rychlost<br> download</b></td>
	  <td style=\"".$style1."\"><b>Rychlost<br> upload</b></td>
	 
	  <td style=\"".$style1."\"><b>Agregace</b></td>
	  <td style=\"".$style1."\"><b>Agregace<br> smluvní</b></td>
	
	  <td style=\"".$style1."\"><b>Počet <br>klientů</b></td>
	  
	  <td style=\"".$style1."\"><b>úprava</b></td>
	  <td style=\"".$style1."\"><b>smazat</b></td>

	 </tr>
	";
  
  echo "<tr><td colspan=\"14\" ><br></td></tr>";
  
  if( ( ereg('^([[:digit:]]+)$',$_GET["id_tarifu"]) ) )
  {
    $id_tarifu = $_GET["id_tarifu"];
    
    $dotaz_tarify = mysql_query(" SELECT * FROM tarify_int WHERE id_tarifu = '".intval($id_tarifu)."' ORDER BY id_tarifu");
    $dotaz_tarify_radku = mysql_num_rows($dotaz_tarify);
  }
  else
  {
    $dotaz_tarify = mysql_query(" SELECT * FROM tarify_int ORDER BY id_tarifu");
    $dotaz_tarify_radku = mysql_num_rows($dotaz_tarify);
  }
  
  if( $dotaz_tarify_radku == 0 )
  {
   echo "
	<tr>
	  <td colspan=\"6\" >Žádné záznamy v databázi</td>
	</tr>
	";
  }
  else
  {
   
   while( $data = mysql_fetch_array($dotaz_tarify) )
   {
    echo "
	<tr >
	  <td style=\"".$style2."\" colspan=\"\" >".$data["id_tarifu"]."</td>
	  <td style=\"".$style2."\" colspan=\"\" >".$data["zkratka_tarifu"]."</td>
	  <td style=\"".$style2."\" colspan=\"\" >".$data["jmeno_tarifu"]."</td>
	  	  
	  <td style=\"".$style2."\" colspan=\"\" >";
	  
	  if ( $data["typ_tarifu"] == 0 )
	  { echo "wifi tarif"; }
	  elseif ( $data["typ_tarifu"] == 1 )
	  { echo "optický tarif"; }
	  else
	  { echo $data["typ_tarifu"]; }
	  
	  echo "</td>
	  
	  <td style=\"".$style2."\" colspan=\"\" >";
	  
	  //echo $data["garant"];
	  if ( $data["garant"] == 1 )
	  { echo "Ano"; }
	  elseif ( $data["garant"] == 0 )
	  { echo "Ne"; }
	  else
	  { echo $data["garant"]; }
	  
	  echo "</td>
	  
	  <td style=\"".$style2."\" colspan=\"\" >".$data["cena_bez_dph"]."</td>
	  <td style=\"".$style2."\" colspan=\"\" >".$data["cena_s_dph"]."</td>

	  <td style=\"".$style2."\" colspan=\"\" >".$data["speed_dwn"]."</td>
	  <td style=\"".$style2."\" colspan=\"\" >".$data["speed_upl"]."</td>

	  <td style=\"".$style2."\" colspan=\"\" >".$data["agregace"]."</td>
	  <td style=\"".$style2."\" colspan=\"\" >".$data["agregace_smlouva"]."</td>
	  
	  <td style=\"".$style2."\" colspan=\"\" >";
	  
	  //zjisteni poctu lidi
	  $id_tarifu = $data["id_tarifu"];
	  
	  $dotaz_lidi = pg_query("SELECT * FROM objekty WHERE id_tarifu = '$id_tarifu' ");
	  $dotaz_lidi_radku = pg_num_rows($dotaz_lidi);
 	 
	  echo $dotaz_lidi_radku;
	   
	  echo "</td>
	  
	  <td style=\"".$style2."\" colspan=\"\" >
	    <a href=\"".$_SERVER["PHP_SELF"]."?update_id=".$data["id_tarifu"]."\" >upravit</a>
	  </td>
	  <td style=\"".$style2."\" colspan=\"\" >
	    <a href=\"".$_SERVER["PHP_SELF"]."?erase_id=".$data["id_tarifu"]."\" >smazat</a>
	  </td>

	</tr>
	"; 
   }
   
  } //konec else if radku == 1
  
  echo "</table>";
  
 } // konec hlavniho else ..
 
 ?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html>
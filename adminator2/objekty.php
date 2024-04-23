<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,1) ) )
{
// neni login

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

<title>Adminator2 - Objekty</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 <tr>
   <td colspan="2" height="20" bgcolor="silver">
    <? include("objekty-subcat-inc.php"); ?>
  </td>
 </tr>
  
 <form name="form1" method="GET" <? echo 'action="'.$_SERVER["PHP_SELF"].'">'; ?>
 
<tr>
    <td colspan="2" style="border: 0px; " >
    
    <hr width="16%" align="left">
    
    <input type="radio" name="es" value="1" <? if ( !( isset($_GET["es"]) ) ) { echo "checked"; } ?> > <label>všichni | </label>
    <input type="radio" name="es" value="2" <? if ( ( $_GET["es"] == 2 ) ) { echo "checked"; }  ?> > <label>platiči | </label>
    <input type="radio" name="es" value="3" <? if ( ( $_GET["es"] == 3 ) ) { echo "checked"; }  ?> > <label>NEplatici | </label>
    
    <input type="radio" name="es" value="4" <? if ( ( $_GET["es"] == 4 ) ) { echo "checked"; }  ?> > <label>apčka | </label>
    <input type="radio" name="es" value="5" <? if ( ( $_GET["es"] == 5 ) ) { echo "checked"; }  ?> > <label>garantované | </label>
    <input type="radio" name="es" value="6" <? if ( ( $_GET["es"] == 6 ) ) { echo "checked"; }  ?> > <label>veřejné |</label>
    
    <input type="radio" name="es" value="7" <? if ( ( $_GET["es"] == 7 ) ) { echo "checked"; }  ?> > <label>bez vlastníka |</label>
    <input type="radio" name="es" value="8" <? if ( ( $_GET["es"] == 8 ) ) { echo "checked"; }  ?> > <label>zakázaný net |</label>
    <input type="radio" name="es" value="9" <? if ( ( $_GET["es"] == 9 ) ) { echo "checked"; }  ?> > <label>šikana |</label>
    <hr width="16%" align="left">
    </td>
 </tr>
 
<?
   
  //promena pro update objektu
   if ( check_level($level,29) ) { $update_povolen="true"; }
   if ( check_level($level,33) ) { $mazani_povoleno="true"; }
   if ( check_level($level,34) ) { $garant_akce="true"; }
   if ( check_level($level,59) ) { $export_povolen="true"; }
      
?>

 <tr>
    <td colspan="2" style="border: 0px; " >
    
    <?
    //zde kontrola promennych
    global $mod_vypisu;
    $mod_vypisu = $_GET["mod_vypisu"];
    
    if( isset($mod_vypisu) )
    {
     if( !( preg_match('/^([[:digit:]])+$/',$mod_vypisu) ) )
     {
      echo "<div style=\"color: red; font-weight: bold; \" >Chyba! Nesouhlasi vstupni data. (mod vypisu) </div>";
      exit;
     }
    }  
    
    $dns_find = $_GET["dns_find"];
    
    if( ( strlen($dns_find) > 0 ) )
    {
     if( !( preg_match('/^([[:alnum:]]|_|-|\.|\%)+$/',$dns_find) ) )
     {
      echo "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v poli \"Hledání dle dns\". (Povolené: a-z,A-Z,0-9,-, _,. )</div>";
      exit;
     }    
    }
    
    $ip_find=$_GET["ip_find"];
    
    if( ( strlen($ip_find) > 0 ) )
    {
     if( !( preg_match('/^([[:digit:]]|\.|/)+$/',$ip_find) ) )
     {
      echo "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v poli \"Hledání dle ip adresy\". (Povolené: a-z,A-Z,0-9,-, _,. )</div>";
      exit;
     }    
    }
    
    $es = $_GET["es"];
    
    if( ( strlen($es) > 0 ) )
    {
     if( !( preg_match('/^([[:digit:]])+$/',$es) ) )
     {
      echo "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v poli \"Sekundární hledání\". </div>";
      exit;
     }    
    }
    
    $razeni = $_GET["razeni"];

    if( ( strlen($razeni) > 0 ) )
    {
     if( !( preg_match('/^([[:digit:]])+$/',$razeni) ) )
     {
      echo "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v promenné \"razeni\". </div>";
      exit;
     }    
    }
    
    $list=$_GET["list"];       
   
    if( ( strlen($list) > 0 ) )
    {
     if( !( preg_match('/^([[:digit:]])+$/',$list) ) )
     {
      echo "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v promenné \"list\". </div>";
      exit;
     }    
    }
    
    //konec kontroly promennych
        
    echo '
     <input type="submit" value="NAJDI" name="najdi"> 
    <input type="hidden" name="odeslano" value="true">    
    ';

    // tafy generovani exportu
    if ( $export_povolen == true )
    { objekt_a2::export_vypis_odkaz(); }	
    
    echo "<span style=\"padding-right: 25px; \" >
	    <span style=\"padding-right: 15px; \">mód objektů:</span> 
    
	    <select size=\"1\" name=\"mod_vypisu\" >
	      <option value=\"1\" "; if( $mod_vypisu == 1 ) echo " selected "; echo ">bezdrátová síť</option>
	      <option value=\"2\" "; if( $mod_vypisu == 2 ) echo " selected "; echo ">optická síť</option>
	    </select>
	  </span>";
        
    ?>
      
    <label>Hledání podle dns: </label><input type="text" name="dns_find" value="<? 
    
    if( (strlen($dns_find) ==0 ) ){ echo "%"; }
    else{ echo $dns_find; }
    echo "\">"; ?>
    
    <span style="padding-left: 40px;"></span>
    <label> Hledání podle ip: </label><input type="text" name="ip_find" <? echo 'value="'.$ip_find.'">'; ?>
    
    </td>
   </tr>
    
  <tr>
   <td colspan="2" style="border: 0px; " >
    <hr width="16%" align="left"> 
   </td>
  <tr>
    
  <tr>
  <td colspan="2">
  <?
  
  //$dns_find=$_GET["dns_find"];
  
  if ( ( strlen($dns_find) > 0 ) ){ $co=1; $sql=$dns_find; }  
  if ( ( strlen($ip_find) > 0  ) ){ $co=2; $sql=$ip_find; }
  
  $objekt = new objekt_a2;

  $objekt->vypis_tab(1);

  $objekt->vypis_tab_first_rows($mod_vypisu);

 //sem řazení

 //vnejsi tab
 echo "\n <tr >";

 objekt_a2::vypis_razeni_a2();

 //konec vnejsi tab
 echo "</tr></form>";
 
 
 list($se,$order) = objekt_a2::select($es,$razeni);

 global $dotaz_source;

 $tarif_sql = "";
   
  if( $mod_vypisu == 1 )
  { 
    $dotaz_f = $conn_mysql->query("SELECT * FROM tarify_int WHERE typ_tarifu = '0' ");
    
    $i = 0;
    
    while( $data_f = $dotaz_f->fetch_array() )
    {
     if( $i == 0 ){ $tarif_sql .= "AND ( "; }
     if( $i > 0 ){ $tarif_sql .= " OR "; }
     
     $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"].""; 
     
     $i++;
   }
   
   if( $i > 0 ){ $tarif_sql .= " ) "; }
   
  }
  elseif( $mod_vypisu == 2 )
  { 
    try {
      $dotaz_f = $conn_mysql->query("SELECT * FROM tarify_int WHERE typ_tarifu = '1' ");
    } catch (Exception $e) {
      die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
    }
    
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
  // echo "dotaz_tarif: ".$tarif_sql." /";
   
 if( $co==1)
 {
   $sql="%".$sql."%";
   
   $dotaz_source = "SELECT * FROM objekty WHERE dns_jmeno LIKE '$sql' ".$se.$tarif_sql.$order;
 }
 elseif( $co==2 ){ $dotaz_source = "SELECT * FROM objekty WHERE ip <<= '$sql' ".$se.$tarif_sql.$order; }
 elseif( $co==3 ){ $dotaz_source = "SELECT * FROM objekty WHERE id_cloveka=".$id; }
 else
 {
  echo ""; 
  exit;
 }
 
 global $list;
    
 
   $poradek="es=".$es."&najdi=".$najdi."&odeslano=".$odeslano."&dns_find=".$dns_find."&ip_find=".$ip_find."&razeni=".$razeni;
   $poradek .= "&mod_vypisu=".$mod_vypisu;
  
   //vytvoreni objektu
   $listovani = new c_listing_objekty("./objekty.php?".$poradek."&menu=1", 30, $list, "<center><div class=\"text-listing2\">\n", "</div></center>\n", $dotaz_source);

    if(($list == "")||($list == "1")){ $bude_chybet = 0;  } //pokud není list zadán nebo je první bude ve výběru sql dotazem chybet 0 záznamů
    else
    { $bude_chybet = (($list-1) * $listovani->interval); }   //jinak jich bude chybet podle závislosti na listu a intervalu

    $interval=$listovani->interval;

    $dotaz_final=$dotaz_source." LIMIT ". intval($interval)." OFFSET ".$bude_chybet." ";
  
  $listovani->listInterval();
  
  $objekt->conn_mysql = $conn_mysql;
  $objekt->conn_pqsql = $db_ok2;
  
  $objekt->vypis($sql,$co,0,$dotaz_final);
     
  $objekt->vypis_tab(2);  
 
  $listovani->listInterval(); 
  

 ?>
  
   </td>
  </tr> 
 </table>

</body> 
</html> 


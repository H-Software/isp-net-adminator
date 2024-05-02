<?php

require("include/main.function.shared.php");
require("include/config.php"); 

require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,78) ) )
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

<title>Adminator 2 :: výpovědi smluv</title>

</head>

<body>

<? include ("head.php"); ?>

<? include ("category.php"); ?>

   
 <tr>
 <td colspan="2" height="20" bgcolor="silver" >

    <span style="margin-left: 40px; "><a href="vlastnici-cat.php" class="odkaz-uroven-vys" >| O úrověn výš |</a></span>
 
    <span style="margin-left: 40px; "><a href="vypovedi.php" >Výpis výpovědí </a></span>
    
    <span style="margin-left: 40px; "><a href="vypovedi-vlozeni.php" > Vložení výpovědi </a></span>
	 
    <span style="margin-left: 40px; "><a href="vypovedi-plaintisk.php" > Tisk nevyplněné žádosti </a></span>	
 
 </td>
  </tr>
 
  <tr>
  <td colspan="2">
  
  <!-- zacatek vlastniho obsahu -->
  
  <?
  //zde filtr atd
  echo "<div style=\"padding-top: 20px; padding-left: 10px; font-size: 18px; padding-bottom: 20px; \" >
	    Filtrování záznamů:
  
    </div>";
    
  // vypis 
  echo "<table border=\"0\" width=\"100%\" >";
  
  echo "<tr>";
    echo "<td><b>id žádosti: </b></td>";
    echo "<td><b>vlastník:</b></td>";
    echo "<td><b>v archivu:</b></td>";
    
    echo "<td width=\"100px\"><b>datum uzavření smlouvy: </b></td>";
    echo "<td width=\"100px\"><b>datum vložení žádosti: </b></td>";
    echo "<td width=\"100px\"><b>datum výpovědi: </b></td>";
    echo "<td><b>Výpovědní lhůta: </b></td>";
    echo "<td width=\"200px\"><b>uhrazení výpovědní lhůty: </b></td>";
    echo "<td><b>důvod výpovědi: </b></td>";
  echo "</tr>";
    
    try {
      $dotaz = $conn_mysql->query("SELECT * FROM vypovedi");
      $dotaz_radku=$dotaz->num_rows;
    } catch (Exception $e) {
      die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
    }
    
    while($data=$dotaz->fetch_array() ):
    
      echo "<tr>";

      list ($rok1, $mesic1, $den1 ) = explode ("-", $data["datum_vlozeni"]);
      list ($rok2, $mesic2, $den2 ) = explode ("-", $data["datum_uzavreni"]);
      list ($rok3, $mesic3, $den3 ) = explode ("-", $data["datum_vypovedi"]);

      $datum_vlozeni = $den1."-".$mesic1."-".$rok1;
      $datum_uzavreni = $den2."-".$mesic2."-".$rok2;
      $datum_vypovedi = $den3."-".$mesic3."-".$rok3;


      $uhrazeni_vypovedni_lhuty=$data["uhrazeni_vypovedni_lhuty"];
      
      if ( $uhrazeni_vypovedni_lhuty == 1 )
      { $uhrazeni_vypovedni_lhuty  = " Hotově "; }
          elseif ( $uhrazeni_vypovedni_lhuty == 2 )
      { $uhrazeni_vypovedni_lhuty  = " Převodem "; }
      elseif ( $uhrazeni_vypovedni_lhuty == 3 )
      { $uhrazeni_vypovedni_lhuty  = " Doběhnutím trvalého příkazu "; }		

      $id_vlastnika=$data["id_vlastnika"];
      
      $firma="0";
      
      $dotaz_firma=pg_query("SELECT * FROM vlastnici WHERE id_cloveka LIKE '$id_vlastnika' ");
      $dotaz_firma_radku=pg_num_rows($dotaz_firma);
      
      if ( $dotaz_firma_radku <> 1)
      {
        echo "<td>".$data["id_vypovedi"]." ";
        
        echo "<span style=\"color: grey; font-weight: bold; \"> Nelze</span></td>";
        echo "<td><span style=\"color: grey; font-weight: bold; \">? ($id_vlastnika) </span></td>";
        echo "<td><span style=\"color: grey; font-weight: bold; \">?</span></td>";
      }
      else 
      {
        while ( $data_firma=pg_fetch_array($dotaz_firma) )
        { $firma=$data_firma["firma"]; $archiv=$data_firma["archiv"]; }
    	
        $firma2 = $firma + 1;
  
        echo "<td>".$data["id_vypovedi"]." ";
  
        echo "<a href=\"vypovedi-tisk.php?tisk=1&id_vlastnika=".$data["id_vlastnika"]."&datum_uzavreni=".$data["datum_uzavreni"];
        echo "&datum_vypovedi=".$data["datum_vypovedi"]."&duvod=".$data["duvod"]."&datum_vlozeni=".$data["datum_vlozeni"];
        echo "&firma=".$firma2."&vypovedni_lhuta=".$data["vypovedni_lhuta"]."&uhrazeni_vypovedni_lhuty=".$data["uhrazeni_vypovedni_lhuty"]."\" > TISK </a>";
        
        echo "</td>";
  
        if ( $archiv == 1)
        { echo "<td><a href=\"vlastnici-archiv.php?find_id=".$data["id_vlastnika"]."\" >".$data["id_vlastnika"]."</a></td>"; }
        elseif ( $firma == 1 )
        { echo "<td><a href=\"vlastnici2.php?find_id=".$data["id_vlastnika"]."\" >".$data["id_vlastnika"]."</a></td>"; }
        else
        { echo "<td><a href=\"vlastnici.php?find_id=".$data["id_vlastnika"]."\" >".$data["id_vlastnika"]."</a></td>"; }
        
        if ( $archiv == 1)
        { echo "<td>Ano</td>"; }
        else
        { echo "<td>Ne</td>"; }
    
    
      } // konec else pocer radku 1
    
      echo "<td>".$datum_uzavreni."</td>";
      echo "<td>".$datum_vlozeni."</td>";
      echo "<td>".$datum_vypovedi."</td>";
      
      if ( $data["vypovedni_lhuta"] == 1 )
      { echo "<td> Ano </td>"; }
      else
      { echo "<td> Ne </td>"; }
    
      echo "<td>".$uhrazeni_vypovedni_lhuty."</td>";
      
      echo "<td>".$data["duvod"]."</td>";
      
      echo "</tr>";
    
    endwhile;
    
  echo "</table>";
  
  ?>
  
  <!-- konec vlastniho obsahu -->
 
  </td>
  </tr>
  
 </table>

</body> 
</html> 

 
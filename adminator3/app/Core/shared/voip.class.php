<?php

global $cesta;

// previous name: class.voip.main.php
// probably not used at the moment

class voip
{

    public function zjisteni_vlastnika($id_vlastnika)
    {

    $vlastnik_dotaz=pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '$id_vlastnika'");
    $vlastnik_radku=pg_num_rows($vlastnik_dotaz);
    while ($data_vlastnik=pg_fetch_array($vlastnik_dotaz))
    { 
	$firma_vlastnik=$data_vlastnik["firma"]; $archiv_vlastnik=$data_vlastnik["archiv"]; 
	
	$popis_vlastnika = " ".$data_vlastnik["prijmeni"]." ".$data_vlastnik["jmeno"].", ";
        $popis_vlastnika .= $data_vlastnik["ulice"]." ".$data_vlastnik["mesto"].", ";             
	$popis_vlastnika .= "VS: ".$data_vlastnik["vs"]." ";
		     	
    }

      				
    if ( $archiv_vlastnik == 1)
    { echo "<a href=\"/adminator2/vlastnici-archiv.php?find_id=".$id_vlastnika."\" "; }
    elseif ($firma_vlastnik == 1 )
    { echo "<a href=\"/adminator2/vlastnici2.php?find_id=".$id_vlastnika."\" "; }
    else
    { echo "<a href=\"/adminator2/vlastnici.php?find_id=".$id_vlastnika."\" "; }

    echo "title=\"Detail vlastníka: ".$popis_vlastnika."\" >".$id_vlastnika."</a>\n";
    
    }

    public function vypis_cisla_query($dotaz_sql)
    {
	  $this->dotaz_rs = mysql_query($dotaz_sql);
	  $this->dotaz_rs_radku = mysql_num_rows($this->dotaz_rs);    
	  
      return $this->dotaz_rs_radku;
    }
    
    public function vypis_cisla($mod)
    {
     
     if ( $mod == 2) //zobrazeni u vlastnika
     { echo "<table border=\"0\" width=\"100%\" >"; }
     else
     { 
      echo "<table border=\"0\" width=\"75%\" align=\"center\" cellpadding=\"5px\" ";
      echo " style=\"font-size: 13px; border-bottom: 1px solid grey; \" >"; 
     }

     $pocet_sloupcu = "7";
     
    if( $mod == 1)
    {
     $styl_1_radka = " style=\"border-bottom: 1px solid black; \" ";
    echo "
     <tr ".$styl_1_radka." >
      <td ".$styl_1_radka." ><b>id čísla</b></td>
      <td ".$styl_1_radka." ><b>číslo</b></td>

      <td ".$styl_1_radka." ><b>id vlastníka</b></td>
      <td ".$styl_1_radka." ><b>typ systému</b></td>

      <td ".$styl_1_radka." ><b>zobrazení hovorů</b></td>

      <td ".$styl_1_radka." ><b>úprava</b></td>
      <td ".$styl_1_radka." ><b>smazaní</b></td>

     </tr>

     <tr><td colspan=\"".$pocet_sloupcu."\"><br></td></tr>";
    }
    
    while( $data = mysql_fetch_array($this->dotaz_rs) )
    {
     echo "<tr>";

     echo "<td class=\"voip-vypis-radek\" >";
       if ( $mod == 2){ echo "<span style=\"color: grey;\">id čísla: </span>"; }
     echo $data["id_cisla"]."</td>";

     echo "<td class=\"voip-vypis-radek\">";
       if ( $mod == 2){ echo "<span style=\"color: grey;\">tel. číslo: </span>"; }
     echo $data["cislo"]."</td>";

     echo "<td class=\"voip-vypis-radek\">";
       if ( $mod == 2){ echo "<span style=\"color: grey;\">id vlastníka: </span>"; }
     
     $id_vlastnika = $data["id_vlastnika"];
     
     if( $mod == 1 ){ $this->zjisteni_vlastnika($id_vlastnika); }
     else{ echo $id_vlastnika; }
     
     echo "</td>";

     echo "<td class=\"voip-vypis-radek\">";
       if ( $mod == 2 ){ echo "<span style=\"color: grey;\">typ systému: </span>"; }
       if ( $data["typ_systemu"] == 1 ){ echo "Net4Net"; }
       elseif( $data["typ_systemu"] == 2 ){ echo "Dialtelecom"; }
       elseif( $data["typ_systemu"] == 0 ){ echo "nevybráno"; }
       else{ echo $data["typ_systemu"]; }
     echo "</td>";

     echo "<td class=\"voip-vypis-radek\">";
	echo "<a href=\"/adminator2/voip/voip-hovory.php?item=1&typ_systemu=".$data["typ_systemu"];
	echo "&cislo=".$data["cislo"]."\" >Zobrazit hovory</a>";
    
     echo "</td>";

     echo "<td class=\"voip-vypis-radek\">
    	    <a href=\"/adminator2/voip/voip-cisla-edit.php?update_id=".$data["id_cisla"]."\" >úprava</a>
	   </td>";
     
     echo "<td class=\"voip-vypis-radek\">     	    
    	    <a href=\"/adminator2/voip/voip-cisla-erase.php?erase_id=".$data["id_cisla"]."\">smazání</a> 	   
	   </td>";
     
     echo "</tr>";
    } //konec while
    echo "</table>";
    
  } // konec funkce

    
}  //konec tridy

?>

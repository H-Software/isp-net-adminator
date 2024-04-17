<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,105) ) )
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

$id_vlastnika=$_GET["id_vlastnika"];

$dotaz=$conn_mysql->query("SELECT * FROM opravy WHERE ( id_vlastnika = ' " . intval($id_vlastnika) . "' and id_predchozi_opravy = 0 ) order by id_predchozi_opravy, datum_vlozeni ");

$dotaz_radku = $dotaz->num_rows;

if ( $id_vlastnika < 1)
{ echo "<div style=\"\">Chyba! Nelze vybrat vlastníka! Chyba Vstupních dat. </div>"; }
else
{

$pocet_bunek="11";

// echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"GET\" >";

echo "<table border=\"0\" width=\"100%\" >";

echo "<tr><td colspan=\"".$pocet_bunek."\" ><br></tr>";

echo "<tr>
        <td colspan=\"3\"><span style=\"font-size: 18px; font-weight: bold; \" >Výpis Závad/oprav </span></td>
	<td colspan=\"4\" >";

echo "   <form action=\"opravy-index.php\" method=\"get\" >
         <input type=\"hidden\" name=\"typ\" value=\"1\" >
         <input type=\"hidden\" name=\"id_vlastnika\" value=\"".$id_vlastnika."\" >
						     
     <input type=\"submit\" name=\"ok\" value=\" Vložit závadu/opravu k vybranému vlastníkovi \" ></form>";
									 
	
echo "</td>
    </tr>";
    
    
echo "<tr><td colspan=\"".$pocet_bunek."\" style=\"border-bottom: 1px solid black; \" ><br></tr>";

echo "<tr>
        <td style=\"border-bottom: 1px dashed black; \" colspan=\"2\" ><b>id :</b></td>
        <td style=\"border-bottom: 1px dashed black; \" ><b>datum vložení: </b></td>
        <td style=\"border-bottom: 1px dashed black; \"><b>priorita: </b></td>
        <td style=\"border-bottom: 1px dashed black; \"><b>v řešení: </b></td>
        <td style=\"border-bottom: 1px dashed black; \"><b>vyřešeno: </b></td>
        <td style=\"border-bottom: 1px dashed black; \"><b>vložil: </b></td>
    </tr>
    <tr>
	<td style=\"border-bottom: 1px dashed black; \" ><br></td>
        <td style=\"border-bottom: 1px dashed black; \" colspan=\"10\" ><b>text: </b></td>
    </tr>";
    
echo "<tr><td colspan=\"".$pocet_bunek."\" ><br></tr>";

if ( $dotaz_radku == 0)
{
  echo "<div style=\"\" >Žádné opravy/závady v databázi neuloženy. </div>"; 

  exit;
}
    
while($data=$dotaz->fetch_array() )
{
      
 $id_opravy = $data["id_opravy"];
 
 $dotaz2=$conn_mysql->query("SELECT * from opravy WHERE id_predchozi_opravy = '$id_opravy' order by datum_vlozeni ");
 $dotaz2_radku=$dotaz2->num_rows;
  
 $class = "opravy-tab-line1";
  
  echo "<tr>
        <td class=\"".$class."\" rowspan=\"2\" colspan=\"2\" ><span style=\"font-weight: bold; \">".$data["id_opravy"]."</span></td>";
 
    echo "<td class=\"".$class."\" >".$data["datum_vlozeni"]."</td>
        <td class=\"".$class."\" >";

	// if ( $dotaz2_radku == 0)
	{
	 if ( $data["priorita"] == 0) echo "Nízká";
         elseif ( $data["priorita"] == 1) echo "Normální";
         elseif ( $data["priorita"] == 2) echo "Vysoká";
         else echo "Nelze zjistit";
	}
	// else{ echo "<br>"; }
	
	echo "</td>
        <td class=\"".$class."\" >";
        
	if ( ( $dotaz2_radku == 0 or $data["v_reseni"] == 1 ) )
	{
            if ( $data["v_reseni"] == 0 ) echo "Ne";
            elseif ( $data["v_reseni"] == 1 ) echo "Ano (".$data["v_reseni_kym"].") ";
            else echo "Nelze zjistit";
	}
	else{ echo "<br>"; }
	
        echo "</td>
        <td class=\"".$class."\" >";
        
	if ( ( $dotaz2_radku == 0 or $data["vyreseno"] == 1 ) )
	{
	    if ( $data["vyreseno"] == 0 ) echo "Ne";
            elseif ( $data["vyreseno"] == 1 ) echo "Ano (".$data["vyreseno_kym"].") ";
            else echo "Nelze zjistit";
	}
	else{ echo "<br>"; }
	
        echo "</td>
        <td class=\"".$class."\" >";
        if ( ( strlen($data["vlozil"]) > 1 ) ){ echo $data["vlozil"]; }
        else { echo "<br>"; }

        echo "</td>";
    
    echo "</tr><tr>";

$pocet_bunek2 = $pocet_bunek;

 echo "";
 echo "<td colspan=\"".$pocet_bunek2."\" >
		<span style=\"font-weight: bold; \">".$data["text"]."</span></td>";
		
 echo "</tr>";

  //sem ekundarni zaznamy
  if( $dotaz2_radku > 0 )
  {
    $class = "opravy-tab-line2";
    
   while($data2=$dotaz2->fetch_array() )
   {
    
   echo "<tr>
	<td class=\"".$class."\" rowspan=\"2\" width=\"25px\" ><br></td>
        <td class=\"".$class."\" rowspan=\"2\" colspan=\"\" >".$data2["id_opravy"]."</td>";

    echo "<td class=\"".$class."\" >".$data2["datum_vlozeni"]."</td>
        <td class=\"".$class."\" >";
  
	// if ( $dotaz2_radku == 0)
	{
	 if ( $data2["priorita"] == 0) echo "Nízká";
         elseif ( $data2["priorita"] == 1) echo "Normální";
         elseif ( $data2["priorita"] == 2) echo "Vysoká";
         else echo "Nelze zjistit";
	}
	// else{ echo "<br>"; }
	
        echo "</td>
        <td class=\"".$class."\" >";
            if ( $data2["v_reseni"] == 0 ) echo "Ne";
            elseif ( $data2["v_reseni"] == 1 ) echo "Ano (".$data2["v_reseni_kym"].") ";
            else echo "Nelze zjistit";

        echo "</td>
        <td class=\"".$class."\" >";
            if ( $data2["vyreseno"] == 0 ) echo "Ne";
            elseif ( $data2["vyreseno"] == 1 ) echo "Ano (".$data2["vyreseno_kym"].") ";
            else echo "Nelze zjistit";

        echo "</td>
        <td class=\"".$class."\" >";
        if ( ( strlen($data2["vlozil"]) > 1 ) ){ echo $data2["vlozil"]; }
        else { echo "<br>"; }

        echo "</td>";
    
    echo "</tr><tr>";

    $pocet_bunek2 = $pocet_bunek - 1;
    
    // echo "<td colspan=\"\" ><br></td>";
    echo "<td colspan=\"".$pocet_bunek2."\" >
		<span style=\"font-weight: bold; \">".$data2["text"]."</span></td>";
  
  } // konec druhyho while
  
  } // konec if id_predchozi_opravy > 0
  
  
  } // konec while
      
 echo " </table>

 </form>

";

} // konec else id_vlastnika < 1

?>

 <!-- konec vlastniho obsahu -->
  </td>
  </tr>
  
 </table>

</body>
</html>

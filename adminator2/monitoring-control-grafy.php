<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,55) ) )
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

<title>Adminator 2 - monitoring</title>

</head>

<body>

<?php include ("head.php"); ?> 

<?php include ("category.php"); ?> 

 <tr>
  <td colspan="2"><?php require("monitoring-cat.php"); ?></td>
 </tr>
 
 <tr>
  <td colspan="2">
  
  <!-- zacatek vlastniho obsahu -->
  
  <?php
  
  $typ=$_GET["typ"];
  
  global $typ;
  
  $dotaz=mysql_query("SELECT * FROM mon_grafy WHERE typ_grafu='2' order by id ");
  $pocet_bunek=10;
  
  $dotaz_radku=mysql_num_rows($dotaz);
  
  if ( $dotaz_radku == 0 )
  { echo "Žádné grafy v databázi nenalezeny. "; }
  else
  {
  
  echo "<table border=\"1\" width=\"100%\" >";
  
  echo "<tr><td colspan=\"".$pocet_bunek."\"> Výpis grafů: </td></tr>";
  
  echo "<tr><td colspan=\"".$pocet_bunek."\"><br></td></tr>";
  
    echo "<tr><td><b>id: </b></td>";
    echo "<td><b>ip adresa: </b></td>";
    echo "<td><b>popis: </b></td>";
    echo "<td><b>Kategorie: </b></td>";
    
    echo "<td><b>šablona grafu: <br><span style=\"color: grey; font-size: 12px; \">(velikost pingu)</span></b></td>";
    
    echo "<td><b>Zapnutý alarm: </b></td>";
    echo "<td><b>Stav alarmu: </b></td>";
        
    echo "<td><b>Upravit: </b></td>";
    echo "<td><b>Smazat: </b></td>";
    
    echo "</tr>";
  
  
  while ( $data=mysql_fetch_array($dotaz) )
  {
  
    echo "<tr>";
    
    if ( $typ == 1)
    { // pingy
	echo "<td>".$data["id"]."</td>";
	echo "<td>".$data["ip1"]."</td>";
    
	if ( $data["sablona_grafu"] == 1)
	{ 
	    echo "<td><a href=\"https://monitoring.simelon.net/mon/www-generated/ping_".$data["popis1"].".php\" target=\"_new\" >".$data["popis1"]."</a></td>";     
	}
	elseif ( $data["sablona_grafu"] == 2 )
	{ echo "<td><a href=\"https://monitoring.simelon.net/mon/www-generated/pingloss_".$data["popis1"].".php\" target=\"_new\" >".$data["popis1"]."</a></td>"; }
	else
	{ echo "<td>".$data["popis1"]."</td>"; }    
    
	// zacatek zjistovani kategorie
	$cat_id=$data["cat"];
    
	$dotaz_cat=mysql_query("SELECT jmeno FROM kategorie WHERE id='".intval($cat_id)."' ");
    
	while( $data2=mysql_fetch_array($dotaz_cat) )
	{ $cat_jmeno=$data2["jmeno"]; }
    
	echo "<td>".$cat_jmeno." (".htmlspecialchars($data["cat"]).") </td>";
    
	// konec zjistovani a vypisu kategorie
    
	//sablona grafu
	echo "<td> ";
    
	if ( $data["sablona_grafu"] == 1)
	{ echo " Ping "; }
	elseif ( $data["sablona_grafu"] == 2 )
	{ 
    	    echo " Ping&Loss";
	    echo "<br>";
    	    echo"<span style=\"color: grey; font-size: 12px; \"> ( ".$data["ping_size"].")</span>"; 
	}
	else
	{ echo " Nelze zjistit "; }
    
	echo "</td>";
    
    //alarm
    echo "<td>";

    $alarm=$data["alarm"];

    if( $alarm==1 ){ echo "Ano"; }
    elseif($alarm==0 ){ echo "Ne"; }
    else{ echo "Nelze zjistit"; }

    echo "</td>\n";
    //konec alarmu

     echo "<td>";
     $alarm_stav=$data["alarm_stav"];

     if ($alarm_stav==2){ echo "poplach"; }
     elseif ($alarm_stav==1){ echo "warning"; }
     elseif ($alarm_stav==0) { echo "klid"; }
     else { echo "NA"; }

     echo " <span style=\"color: grey; font-size: 12px; \" >(CW: ".$data["warn"]." CM: ".$data["mail"].")</span> </td>\n";

    }    
	
    $typ=$data["typ_grafu"];
    
    $typ=$typ - 1;
    																			           // update a erase, generovani vzdy
    echo '<td><form method="POST" action="monitoring-control-grafy-add.php" >';
    echo '<input type="hidden" name="update_id" value="'.intval($data["id"]).'" >';
    echo '<input type="hidden" name="typ" value="'.$typ.'" >';
    echo '<input type="submit" name="sended" value="Update"></td></form>';
    
    echo '<td><form method="POST" action="monitoring-control-grafy-del.php" >';
    echo '<input type="hidden" name="erase-id" value="'.intval($data["id"]).'" >';
    echo '<input type="submit" name="sended" value="Smazat" ></td></form>';
    
    echo "</tr>";
  
  
  } // konec while
  
  echo "</table>";
  
  } // konec else dotaz_radku == 0
  
  ?>
    
  <!-- konec vlastniho obsahu -->
 
  </td>
  </tr>
  
 </table>

</body> 
</html> 


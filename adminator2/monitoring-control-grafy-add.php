<?php

require_once ("include/config.php"); 
require_once ("include/check_login.php");

require_once ("include/check_level.php");

require_once ("include/class.php");

if( !( check_level($level,56) ) )
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

<title>Adminator 2 - monitoring</title> 

</head> 

<body> 

<?php require ("head.php"); ?> 

<?php require ("category.php"); ?> 

 
 <tr>
  <td colspan="2" ><? require("monitoring-cat.php"); ?></td>
 </tr>
 
 <tr>
  <td colspan="2">
   
  <!-- zacatek vlastniho obsahu -->
  
  <?php
  
  $typ=$_GET["typ"];
  
  $jmeno_grafu=$_POST["jmeno_grafu"];
  $ip=$_POST["ip"];
  
  $cat=$_POST["cat"];
  
  $kategorie=$cat;
  
  $alarm=$_POST["alarm"];
  $ping_size=$_POST["ping_size"];
  
  $sablona_grafu=$_POST["sablona_grafu"];
  
  $mac=$_POST["mac"];	

  $oidindex = $_POST["oidindex"];
  $fastload = $_POST["fastload"];
    
  if( ! (isset($typ) ) ) { $typ=$_POST["typ"]; }
  
  $send=$_POST["send"];
  $odeslano=$_POST["odeslano"];
  
  $update_id=$_POST["update_id"];
  
  if ( isset($send) )
  { // odeslalo se, budete ukladat
  
    if ( $typ == 1 )
    {
        //pingy .. asi ..
     
	echo "<H3>Zadáno: </H3><br>";
  
    echo "<b>Název grafu: </b> ".$jmeno_grafu." <br>";
    echo "<b>IP adresa: </b>".$ip." <br>";
    echo "<b>Id kategorie: </b>".$kategorie."<br>";
    
    
    echo "<b>Alarm: </b>";
    if ( $alarm == 0){ echo "Ne"; }
    elseif ( $alarm == 1){ echo "Ano"; }
    else { echo "Nelze zjistit"; }
    echo "<br>";
    
    // tady dodelat testovani promennych
    
     if ( ! ( ereg('^([[:alnum:]]|\.|\-|\_)+$',$jmeno_grafu) ) )
     { $error="true";  echo " <br>Název grafu není ve správném formátu!<br>  "; }
         
     if ( ! ( ereg('^([[:digit:]]{1,3})\.([[:digit:]]{1,3})\.([[:digit:]]{1,3})\.([[:digit:]]{1,3})$',$ip) ) )
     { $error="true";  echo " <br>IP adresa není ve správném formátu!<br>  "; }
	 
     
     if ( isset($error) )
     { exit; }
     
    $typ_grafu=$typ+1;
    
    $cesta="/srv/www/htdocs.ssl/monitoring/data/";
    // $alarm="0";

    // ukladani
    if ( ($update_id > 1) )
    { // update 
    
     $uprava=mysql_query("UPDATE mon_grafy SET popis1='$jmeno_grafu', ip1='$ip' , cat='$kategorie',
                             alarm='$alarm',sablona_grafu ='$sablona_grafu', ping_size = '$ping_size' where id='$update_id' Limit 1 ");
				 
				 
     if ($uprava) { echo "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněna.</div></H3><br>\n"; }
      else { echo "<div style=\"color: red; \">Chyba! Data v databázi nelze změnit. </div><br>\n"; }

     $pole = "<b>akce: pridani/zmena  grafu;</b><br> ";
    
     $pole .= "data: [typ_grafu]=> 2, [ip]=> ".$ip." , [jmeno_grafu]=> ".$jmeno_grafu;
     $pole .= " , [kategorie]=> ".$kategorie." ,[sablona_grafu]=> ".$sablona_grafu.", ping_size: ".$ping_size.", [alarm]=> ".$alarm."";
    
     if ( $uprava == 1){ $vysledek_write="1"; }
    
    }
    else
    { // pridani
    
     $add=mysql_query("insert into mon_grafy (typ_grafu, cesta1, ip1, popis1, cat, alarm, sablona_grafu, ping_size) 
    		    values ('$typ_grafu','$cesta', '$ip', '$jmeno_grafu','$kategorie','$alarm','$sablona_grafu','$ping_size' ) ");

     if ($add) { echo "<br><H3><div style=\"color: green; \" >Data do databáze úspěšně uložena.</div></H3><br>\n"; }
     else { echo "<div style=\"color: red; \">Chyba! Data do databáze nelze uložit. </div><br>\n"; }     

     $pole = "<b>akce: pridani/zmena  grafu;</b><br>";
     
     $pole .= "data: [typ_grafu]=> 2, [ip]=> ".$ip." , [jmeno_grafu]=> ".$jmeno_grafu;
     $pole .= " , [kategorie]=> ".$kategorie." ,[sablona_grafu]=> ".$sablona_grafu." , [alarm]=> ".$alarm."";
    
     if ( $add == 1){ $vysledek_write="1"; }
     
    }
        
     $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write') ");

     //zde reset monitoringu 2
     Aglobal::work_handler("18"); //monitoring - Monitoring II - Feeder-restart
     Aglobal::work_handler("22"); //monitoring - Monitoring II - checker-restart
                                          	       
    }
    else
    {
	echo "Chyba! Nelze ukladat, chyba vstupních dat! ";
	exit;
    } // konec else typ
  
  } // konec if isset send
  elseif ( $typ == 1 )
  { // pingy
  
   if( !(isset($odeslano)) )
   {
   
     // zde zjistime predchozi data
    $dotaz=mysql_query("SELECT * FROM mon_grafy WHERE id='$update_id' LIMIT 1");
    	
    while ( $data=mysql_fetch_array($dotaz) )
    { $ip=$data["ip1"]; $jmeno_grafu=$data["popis1"]; $cat=$data["cat"]; 
    $alarm=$data["alarm"]; $sablona_grafu=$data["sablona_grafu"]; 
    
    $ping_size=$data["ping_size"];
    }
  
   }    
    
  $pocet_sloupcu=3;
  
  echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\" name=\"form1\" >";
  
  echo "<table border=\"0\" width=\"100%\" >";
  
  echo "<tr><td colspan=\"".$pocet_sloupcu."\" >Přidání grafu - ping : </td></tr>";
  
  echo "<tr><td colspan=\"".$pocet_sloupcu."\" ><br></td></tr>";
  
  echo "<tr>
	    <td width=\"25%\" ><label>Jméno grafu: </label></td>
	    <td><input type=\"text\" name=\"jmeno_grafu\" value=\"".$jmeno_grafu."\" size=\"33\" > </td>
	    <td><div style=\"padding-left: 20px; margin-right: 20px; \">
		    Povolené znaky: a-z, A-Z, 0-9, tečka, pomlčka, dolní podtržítko 
		</div></td>
	</tr>";
 
  echo "<tr><td colspan=\"".$pocet_sloupcu."\" ><br></td></tr>";
  
  echo "<tr>
            <td  ><label>IP adresa: </label></td>
            <td><input type=\"text\" name=\"ip\" value=\"".$ip."\" size=\"20\" > </td>
            <td><div style=\"padding-left: 20px; margin-right: 20px; \">
                    Pouze číslice oddělené tečkou
                </div></td>
        </tr>";

  echo "<tr><td colspan=\"".$pocet_sloupcu."\" ><br></td></tr>";
  
  echo "<tr><td><label>Kategorie: </label></td>";
 
  echo "<td>";
  
  // sem kategorii
  $dotaz_cat=mysql_query("select * from kategorie where sablona=2" );
  
  $dotaz_cat_radku=mysql_num_rows($dotaz_cat);
   
  if ($dotaz_cat_radku==0) echo "Žádná kategorie v databázi.";
    else
    {
      echo '<select size="1" name="cat" >';
		
    while ($data_cat=mysql_fetch_array($dotaz_cat) ):
		    
        echo "<option value=\"".$data_cat["id"]."\" "; 
	
	if ( $data_cat["id"] == $cat ){ echo " selected "; }
	
	echo " >".$data_cat["jmeno"]."</option> \n";
			    
    endwhile;
				
    echo "</select>";
    
    }
					
   echo "</td> <td><br></td> </tr>";
   
   echo "<tr><td colspan=\"".$pocet_sloupcu."\" ><br></td></tr>";

   echo "<tr><td><label>Šablona grafu: </label></td>";

   echo "<td><select size=\"1\" name=\"sablona_grafu\" onChange=\"self.document.forms.form1.submit()\" > ";

	echo "<option value=\"1\" "; 
	    if ( ( $sablona_grafu == 1 ) ){ echo " selected "; } echo ">Ping</option>";
	echo "<option value=\"2\" "; if ($sablona_grafu == 2){ echo " selected "; } echo ">Ping&Loss</option>";
	
    echo "</td></tr>";

   echo "<tr><td colspan=\"".$pocet_sloupcu."\" ><br></td></tr>";
   
   echo "<tr><td><label>Velikost pingu: </label></td>";
  
   echo "<td>";
   
   if ( $sablona_grafu == 2)
   { echo "<input type=\"text\" name=\"ping_size\" value=\"".$ping_size."\" >"; }
   else { echo "<span style=\"color: grey; \">Není dostupné. </span>"; }
   
   echo "</td>";

    echo "</tr>";

   echo "<tr><td colspan=\"".$pocet_sloupcu."\" ><br></td></tr>";
   
   echo "<tr><td><label>Alarm</td>";
   
   echo "<td><select size=\"1\" name=\"alarm\"  > ";
   
    	echo " <option value=\"0\" "; 
	if ( $alarm == 0){ echo " selected "; }
	echo " >Ne</option>";
	
	echo " <option value=\"1\" ";
	if ( $alarm == 1){ echo " selected "; } 
	
	echo " >Ano</option>
		</select>
		</td>
		
		<td><br></td></tr> \n";
   
   echo "<tr><td colspan=\"".$pocet_sloupcu."\" ><br></td></tr>";
   
  echo "<tr><td colspan=\"2\" align=\"center\" >
		<input type=\"hidden\" name=\"typ\" value=\"".$typ."\" >
		<input type=\"hidden\" name=\"odeslano\" value=\"yes\" >
		<input type=\"hidden\" name=\"update_id\" value=\"".$update_id."\" > 
		<input type=\"submit\" name=\"send\" value=\"Přidat\" >
	</td>
	<td><br></td>
	</tr>";	
	
  echo "</table>";
  
  echo "</form>";
  
  } // konec if typ == 1
  else
  { 
    echo "Chyba! Nesprávné vstupní data! "; 
    exit; 
  }
  
  ?>
  
  <!-- konec vlastniho obsahu -->
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 


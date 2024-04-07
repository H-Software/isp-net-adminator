<?php

require_once ("include/config.php"); 
require_once ("include/check_login.php");

require_once ("include/check_level.php");

if ( !( check_level($level,4) ) )
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

<title>Adminator2 - Topology</title> 

</head> 

<body>

<?php require ("head.php"); ?> 

<?php require ("category.php"); ?> 

<tr>
 <td colspan="2" bgcolor="silver" >
   <?php require ("topology-cat2.php"); ?>
 </td>
</tr>

<tr>
 <td colspan="2">
  							  
<?php

  $odeslano = $_POST["odeslano"];
  $jmeno=$_POST["jmeno"];
  $adresa=$_POST["adresa"];
  $pozn=$_POST["pozn"];
  $ip_rozsah=$_POST["ip_rozsah"];
 
  $typ_vysilace=$_POST["typ_vysilace"];
  $stav = $_POST["stav"];

  $router_id = $_POST["router_id"];
  $typ_nodu = $_POST["typ_nodu"];
  
  $filter_router_id = $_POST["filter_router_id"];
  
 //kontrola platnych udaju
 if( isset($odeslano) )
 {
   if( ereg("/",$ip_rozsah) )
   {
    $error .= "<div style=\"color: red; \" ><H4>Pole \"IP rozsah\" obsahuje nepovolený znak \"/\" !</H4></div>";
   }

   if( ereg(".254$",$ip_rozsah) )
   {
    $error .= "<div style=\"color: red; \" ><H4>Pole \"IP rozsah\" nemůže končit .254, neplatný subnet!</H4></div>";
   }
     
   if ( ($typ_nodu < 1) or ($typ_nodu > 2) ) {
    $error .= "<div style=\"color: red; \" ><H4>Špatná hodnota u prvku \"Mód nodu\"!</H4></div>";      
   }

   if ( $filter_router_id == 0 ) {
    $error .= "<div style=\"color: red; \" ><H4>Špatná hodnota u prvku \"Router ID\"!</H4></div>";            
   }
   
   if ( $router_id == 0 ) {
    $error .= "<div style=\"color: red; \" ><H4>Špatná hodnota u prvku \"Router, kde se provádí filtrace\"!</H4></div>";            
   }
   
 } //konec if isset odeslano
 
 echo "<div style=\"padding-bottom: 10px; padding-top: 10px; font-size: 18px; \">Přidání lokality/nodu</div>";
	
if( ( isset($_POST["jmeno"]) and !isset($error) ) )
{

  //budeme ukladat  	
  echo "<b>Zadáno do formuláře : </b><br><br>";
    
  echo "<b>Nazev</b>: ".$jmeno."<br>";
  echo "<b>Adresa</b>: ".$adresa."<br>";
  echo "<b>Poznamka</b>: ".$pozn."<br>";
  echo "<b>IP rozsah</b>: ".$ip_rozsah."<br>";

  echo "<br>";
  			      
  echo "<b>Typ vysílače</b>: ".$typ_vysilace."<br>";
  echo "<b>Stav</b>: ".$stav."<br>";
 
  echo "<b>Router id</b>: ".$router_id."<br>";
  
  echo "<b>Router id filtrace</b>: ".$filter_router_id."<br>";
  
  echo "<b>Mód nodu</b>: ".$typ_nodu."<br>";
  
  $add=mysql_query("INSERT INTO nod_list (jmeno, adresa, pozn, ip_rozsah,typ_vysilace,stav,router_id,typ_nodu, filter_router_id) 
			VALUES ('$jmeno','$adresa','$pozn','$ip_rozsah','$typ_vysilace','$stav','$router_id','$typ_nodu', '$filter_router_id') ");
					
  if($add){ echo "<br><span style=\"color: green; font-size: 18px; \">Záznam úspěšně vložen.</span><br><br>"; }
  else 
  { 
    echo "<span style=\"color: red; font-weight: bold; font-size: 16px; \">Záznam nelze vložit do databáze. </span>"; 
  
    echo "<div>chyba: ".mysql_errno().": ".mysql_error()."</div>\n";
        
  }
	  	  
  // pridame to do archivu zmen
  $pole="<b>akce: pridani nodu ; </b><br>";
  $pole .= "[nazev]=> ".$jmeno.", [adresa]=> ".$adresa.", [poznamka]=> ".$pozn.", [ip_rozsah]=> ".$ip_rozsah;
  $pole .= ", [typ_vysilace]=> ".$typ_vysilace.", [stav]=> ".$stav.", [router_id]=> ".$router_id.", ";
  $pole .= " [typ_nodu]=> ".$typ_nodu.", [filter_router_id]=> ".$filter_router_id;
  
  if ( $add == 1){ $vysledek_write="1"; }
  $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
                    "('".mysql_real_escape_string($pole)."','".
                	mysql_real_escape_string($nick)."','".
                	mysql_real_escape_string($vysledek_write)."') ");
                                                                    
                                                                    			     
}
else
{
 //zobrazime formular

 echo $error;
 									  
 print '
	 <form method="POST" action="" >
	 <table border="0" width="100%" id="table2">
	 <tr>
	 <td width="25%"><label>Jméno lokality/nodu: </label></td>
	 <td><input type="text" name="jmeno" size="30" value="'.htmlspecialchars($jmeno).'" ></td>
	 </tr>
	
	 <tr>
	 <td><label>Adresa nodu (umístění) : </label></td>
	 <td><input type="text" name="adresa" size="40" value="'.htmlspecialchars($adresa).'" ></td>
	 </tr>
	
	<tr>
	 <td><label>Poznámka : </label></td>
	 <td><textarea name="pozn" cols="30" rows="3">'.htmlspecialchars($pozn).'</textarea></td>
	</tr>
	
	
	 <tr>
	    <td><label>IP rozsah pro lokalitu/nod: </label></td>
	    <td><input type="text" name="ip_rozsah" size="20" value="'.htmlspecialchars($ip_rozsah).'" ></td>
	</tr>
			     
	<tr>
	  <td><br></td>
	  <td></td>
	</tr>
	
	<tr>
	  <td><label>Typ vysílače: </label></td>
	  <td>
	    <select name="typ_vysilace" size="1" >';
	     					 
	     echo "<option value=\"0\" "." class=\"select-nevybrano\" > Není zvoleno </option> \n";
	     echo "<option value=\"1\" "; if( $typ_vysilace == 1 ){ echo " selected "; } echo "> Metallic </option>\n";
	     echo "<option value=\"2\" "; if( $typ_vysilace == 2 ){ echo " selected "; } echo "> ap-2,4GHz-OMNI </option>\n";
	     echo "<option value=\"3\" "; if( $typ_vysilace == 3 ){ echo " selected "; } echo "> ap-2,4Ghz-sektor </option>\n";
	     echo "<option value=\"4\" "; if( $typ_vysilace == 4 ){ echo " selected "; } echo "> ap-2.4Ghz-smerovka </option>\n";
	     echo "<option value=\"5\" "; if( $typ_vysilace == 5 ){ echo " selected "; } echo "> ap-5.8Ghz-OMNI </option>\n";
	     echo "<option value=\"6\" "; if( $typ_vysilace == 6 ){ echo " selected "; } echo "> ap-5.8Ghz-sektor</option>\n";
	     echo "<option value=\"7\" "; if( $typ_vysilace == 7 ){ echo " selected "; } echo "> ap-5.8Ghz-smerovka </option>\n";
	     echo "<option value=\"8\" "; if( $typ_vysilace == 8 ){ echo " selected "; } echo "> jiné </option>\n";
	
	 echo '</select>
	   </td>
	</tr>
	
	<tr>
	   <td><label>Stav: </label></td>
	   <td><select name="stav" >';
				   
	   echo "<option value=\"0\" class=\"select-nevybrano\" > Není zvoleno </option>\n";
	   echo "<option value=\"1\" "; if ( $stav == 1 ){ echo " selected "; } echo "> v pořádku </option>\n";
	   echo "<option value=\"2\" "; if ( $stav == 2 ){ echo " selected "; } echo "> vytížen </option>\n";
	   echo "<option value=\"3\" "; if ( $stav == 3 ){ echo " selected "; } echo "> přetížen </option>\n";
								   
							   
	  echo '</select>
	   </td>
	</tr>
										   																												                 </td>
	<tr>
	  <td><br></td>
	  <td></td>
	</tr>
  
	<tr>
            <td><label>Router id: </label></td>
	    <td>';
		      
	    echo "<select name=\"router_id\" size=\"1\" >\n";
	    echo "<option value=\"0\" class=\"select-nevybrano\" > není zvoleno </option>\n";
				
	    $dotaz_parent = mysql_query("SELECT id, nazev, ip_adresa FROM router_list ORDER BY nazev");					    
	    while( $data_parent=mysql_fetch_array($dotaz_parent))
	    {
	      echo "\t\t\t<option value=\"".intval($data_parent["id"])."\" ";
	      if ( $data_parent["id"] == $router_id )echo " selected ";
	      echo "> ".htmlspecialchars($data_parent["nazev"])." ( ".htmlspecialchars($data_parent["ip_adresa"])." ) </option>\n";
	    }

            echo "</select>\n";

       echo '</td>
        </tr>';

   echo "<tr>
             <td>Mód nodu:</td>
             <td>\n";

         echo "<select size=\"1\" name=\"typ_nodu\" >\n";

          echo "\t\t\t<option value=\"0\" style=\"color: gray; \" ";
                 if($typ_nodu == 0 )echo " selected ";
          echo " >Nezvoleno</option>\n";
 
          echo "\t\t\t<option value=\"1\" style=\"color: #CC0033; \" ";
                 if($typ_nodu == 1 )echo " selected ";
         echo " >Bezdrátová síť</option>\n";

/*
         echo "\t\t\t<option value=\"2\" style=\"color: #e37d2b; font-weight: bold;\" ";
                 if($typ_nodu == 2 )echo " selected ";
         echo " >Optická síť</option>\n";
*/
	echo "</select>\n";
	
    echo "</td>
         </tr>\n";

    echo '<tr>
             <td><label>Router, kde se provádí filtrace: </label></td>
	  <td>';
		       
    echo "<select name=\"filter_router_id\" size=\"1\" >\n";
				 
    $dotaz_parent = mysql_query("SELECT id,nazev,ip_adresa FROM router_list ORDER BY nazev");
    echo "<option value=\"0\" class=\"select-nevybrano\" > není zvoleno </option>\n";
						     
    while( $data_parent = mysql_fetch_array($dotaz_parent) )
    {
	echo "\t\t\t<option value=\"".intval($data_parent["id"])."\" ";
	
	if( $data_parent["id"] == $filter_router_id ){ echo " selected "; }
	echo "> ".htmlspecialchars($data_parent["nazev"])." ( ".htmlspecialchars($data_parent["ip_adresa"])." ) </option>\n";
    }

    echo "</select>\n";

    echo '</td>
        </tr>

	<tr>
	  <td><br></td>
	  <td></td>
	</tr>';
																			
    echo '</tr>
	  <td><br></td>
	  <td></td>
	</tr>

	 <tr>
	 <td></td>
	 <td><input type="submit" value="OK" name="odeslano" >
	 
	 </td>
       </tr>
      </table>
	
	</form>';
     }
  ?>

  </td>
  </tr>
   </table>

</body>
</html>

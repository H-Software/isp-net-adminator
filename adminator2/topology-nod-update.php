<?php

require("include/main.function.shared.php");
require_once("include/config.php"); 
require_once("include/check_login.php");
require_once("include/check_level.php");

require_once ("include/class.php");

if( !( check_level($level,25) ) )
{
 // neni level
 header("Location: nolevelpage.php");
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require_once ("include/charset.php"); 
?>

<title>Adminator2 - Topology - Nod/lokalita</title>

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
 
 echo "<div style=\"padding-bottom: 10px; padding-top: 10px; font-size: 18px; font-weight: bold; \">";
 echo "Úprava lokality/nodu</div>";
 
if( $_POST["jmeno_new"] )
{
  //budeme updatovat
  $jmeno=$_POST["jmeno_new"];
  $adresa=$_POST["adresa_new"];
  $pozn=$_POST["pozn_new"];
  $ip_rozsah=$_POST["ip_rozsah_new"];
  $umisteni_aliasu=$_POST["umisteni_aliasu_new"];
  $id_new=$_POST["update_id_new"];
  $mac=$_POST["mac_new"];
	 
  $typ_vysilace=$_POST["typ_vysilace"];
  $stav=$_POST["stav"];
  $router_id=$_POST["router_id"];	 	 
  $filtrace=$_POST["filtrace_new"];
  
  $id=$_POST["update_id"];
  $typ_nodu = $_POST["typ_nodu"];
  $vlan_id = $_POST["vlan_id"];

  $filter_router_id = $_POST["filter_router_id"];
  $device_type_id = intval($_POST["device_type_id"]);
  
  $rid_recom = $_POST["rid_recom"];
}

if( ( $_POST["B1"] == "OK") )
{  
  $dotaz_router = mysql_query("SELECT nazev FROM router_list WHERE id = '$router_id'");
  if( (mysql_num_rows($dotaz_router) == 1 ))
  { 		      
    while( $data_parent=mysql_fetch_array($dotaz_router))
    { $nazev_routeru = $data_parent["nazev"]." (".$router_id.")"; }
  }
  else{ $nazev_routeru = $router_id; }

  echo "<b><H4>Zadáno do formuláře:</b></H4>";
  echo "<b>Název</b>: ".$jmeno."<br>";
  echo "<b>Adresa</b>: ".$adresa."<br>";
  echo "<b>Poznámka</b>: ".$pozn."<br>";
  echo "<b>IP rozsah</b>: ".$ip_rozsah."<br>";
  echo "<b>Typ vysílače</b>: ";
  
    if( $typ_vysilace == 0 ){ echo "Nezvoleno"; }
    elseif( $typ_vysilace == 1 ){ echo "Metallic"; }
    elseif( $typ_vysilace == 2 ){ echo "ap-2,4GHz-OMNI"; }
    elseif( $typ_vysilace == 3 ){ echo "ap-2,4Ghz-sektor"; }
    elseif( $typ_vysilace == 4 ){ echo "ap-2.4Ghz-smerovka"; }
    elseif( $typ_vysilace == 5 ){ echo "ap-5.8Ghz-OMNI"; }
    elseif( $typ_vysilace == 6 ){ echo "ap-5.8Ghz-sektor"; }
    elseif( $typ_vysilace == 7 ){ echo "ap-5.8Ghz-smerovka"; }
    elseif( $typ_vysilace == 8 ){ echo "jiné"; }
    else{ echo $typ_vysilace; }						
  
  echo "<br>";			
  echo "<b>stav vysílače</b>: ";
  
    if( $stav == 0 ){ echo "Není zvoleno"; }
    elseif( $stav == 1 ){ echo "v pořádku "; }
    elseif( $stav == 2 ){ echo "vytížen"; }
    elseif( $stav == 3 ){ echo "přetížen"; }
    else{ echo $stav; }
				  
  echo "<br>";			
  echo "<b>Router</b>: ".$nazev_routeru."<br>";	      

  echo "<b>Typ nodu</b>: ".$typ_nodu."<br>";	      
  echo "<b>vlan id</b>: ".$vlan_id."<br>";
  echo "<b>id routeru, kde se filtrujou IP</b>: ".$filter_router_id."<br>";
  	
  echo "<b>Typ(model) koncového zařízení (switche):</b> ".$device_type_id."<br>";
  
  $pole="<b>akce: uprava nodu;</b><br>";
  //$pole .= "puvodni data: ";
	  
  $vysledek=mysql_query("select * from nod_list where id=".$id_new );
  $radku=mysql_num_rows($vysledek);
	
  if ($radku==0)
  { 
    echo "<div style=\"padding: 5px; color: red; font-weight: bold; \">Chyba! Nelze zjistit přechozí hodnoty! </div>"; 
    $pole .= "[error] => nelze zjistit predchozi hodnoty, ";
  }
  else
  {
    while ($zaznam=mysql_fetch_array($vysledek)):
        
     $pole_puvodni_data["jmeno"]=$zaznam["jmeno"]; $pole_puvodni_data["adresa"]=$zaznam["adresa"];
     $pole_puvodni_data["ip_rozsah"]=$zaznam["ip_rozsah"]; $pole_puvodni_data["typ_vysilace"]=$zaznam["typ_vysilace"];
     $pole_puvodni_data["stav"]=$zaznam["stav"]; $pole_puvodni_data["router_id"]=$zaznam["router_id"];
     $pole_puvodni_data["pozn"]=$zaznam["pozn"]; $pole_puvodni_data["typ_nodu"]=$zaznam["typ_nodu"];
    
     $pole_puvodni_data["vlan_id"]=$zaznam["vlan_id"];
     $pole_puvodni_data["filter_router_id"]=$zaznam["filter_router_id"];
     $pole_puvodni_data["device_type_id"]=$zaznam["device_type_id"];
    
    endwhile;	
  }
	
  $uprava=mysql_query("UPDATE nod_list SET jmeno='$jmeno', adresa='$adresa' , pozn='$pozn', ip_rozsah='$ip_rozsah', 
		     typ_vysilace='$typ_vysilace',stav='$stav',router_id='$router_id',
		     typ_nodu = '$typ_nodu', vlan_id = '$vlan_id', filter_router_id = '$filter_router_id',
		     device_type_id = '$device_type_id' WHERE id=".$id_new." Limit 1 ");
		
  if($uprava){ echo "<br><span style=\"color: green; font-size: 18px; \">Záznam úspěšně upraven.</span><br><br>"; }
  else{ 
    echo "<div style=\"color: red; font-weight: bold; font-size: 16px; \">Chyba! Záznam nelze upravit. </div>"; 
    echo "<div>chyba: ".mysql_errno().": ".mysql_error()."</div>\n";
  }

  //ulozeni do archivu zmen
  require("topology-nod-update-inc-archiv-zmen.php");  

  //automaticke restarty
  if( ereg(".*Routeru, kde se provádí filtrace.*", $pole3) )
  {
     Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart
  }

  if( ereg(".*<b>Routeru</b>.*", $pole3) )
  {
     Aglobal::work_handler("1");	//reinhard-3 (ros) - restrictions (net-n/sikana)
     Aglobal::work_handler("20"); 	//reinhard-3 (ros) - shaper (client's tariffs)
  
     Aglobal::work_handler("24");	//reinhard-5 (ros) - restrictions (net-n/sikana)
     Aglobal::work_handler("23");	//reinhard-5 (ros) - shaper (client's tariffs)
  
     Aglobal::work_handler("13");	//reinhard-wifi (ros) - shaper (client's tariffs)
     Aglobal::work_handler("2");	//reinhard-wifi (ros) - restrictions (net-n/sikana)

     Aglobal::work_handler("14"); 	//(trinity) filtrace-IP-on-Mtik's-restart
        
  }                                          

  if( ereg(".*vlan_id.*", $pole3) )
  {
    Aglobal::work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update
    
    Aglobal::work_handler("4"); //reinhard-fiber - radius    
    Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
  }
  
  if( ereg(".*změna.*koncového.*zařízení.*", $pole3) )
  {
    Aglobal::work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update
    
    Aglobal::work_handler("4"); //reinhard-fiber - radius    
    Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)   
  }
  
}
else
{
  //zobrazime formular
  $id=$_POST["update_id"];
	
  if(!($id))
  { $id =$_POST["update_id_new"]; }
  
  $vysledek=$conn_mysql->query("select * from nod_list where id=".intval($id)."");
  $radku=$vysledek->num_rows;
	
  if($radku==0)
  { 
    echo "<div style=\"padding: 5px; color: red; font-weight: bold; \">";
    echo "Chyba! Nelze zjistit původní hodnoty!</div>";
  }
  else
  {
    while ($zaznam=$vysledek->fetch_array()):
		
      $id=$zaznam["id"];
      $jmeno=$zaznam["jmeno"];
	$adresa=$zaznam["adresa"];
	$pozn=$zaznam["pozn"];
	$ip_rozsah=$zaznam["ip_rozsah"];
	$umisteni_aliasu=$zaznam["umisteni_aliasu"];
	$mac=$zaznam["mac"];
	$filtrace=$zaznam["filtrace"];
	$typ_vysilace=$zaznam["typ_vysilace"];
	
	$stav=$zaznam["stav"];
	$typ_nodu =$zaznam["typ_nodu"];
	
	$router_id=$zaznam["router_id"];
	$vlan_id = $zaznam["vlan_id"];
	$filter_router_id = $zaznam["filter_router_id"];

	$device_type_id = $zaznam["device_type_id"];
	
    endwhile;	
  }				  
  
  //checkem jestli se macklo na tlacitko "OK" :)
  if( ereg("^OK$",$_POST["B1"]) ) { echo ""; }
  else
  {  
    print "<div class=\"objekty-add-no-click-ok\"><h4>Data neuloženy, nebylo použito ".
    "tlačítko \"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>"; 
  }
	
  //zde kontrola zda jiz jsme odeslali $_POST["jmeno_new"] 

  echo '
	 <form method="POST" action="">
	 <table border="0" width="950px;" id="table2" name="form1" >
	
	 <tr>
	 <td width="25%"><label>Jméno lokality/nodu: </label></td>
	 <td><input type="text" name="jmeno_new" size="30" value="'.$jmeno.'"></td>
	 </tr>

	 <tr>
	 <td><label>Adresa nodu (umístění) : </label></td>
	 <td><input type="text" name="adresa_new" size="40" value="'.$adresa.'"></td>
	 </tr>

	<tr>
	 <td><label>Poznámka : </label></td>
	 <td><textarea name="pozn_new" cols="30" rows="3">'.$pozn.'</textarea></td>
	</tr>

	 <tr>
	    <td><label>IP rozsah pro lokalitu/nod: </label></td>
	    <td><input type="text" name="ip_rozsah_new" size="20" value="'.$ip_rozsah.'"></td>
	</tr>
	
	<tr>
	    <td><label>Typ vysílače: </label></td>
	    <td>
		<select name="typ_vysilace" size="1" >';
			
		    echo "<option value=\"0\" "." class=\"select-nevybrano\" > Není zvoleno </option>";
		    echo "<option value=\"1\" "; if ( $typ_vysilace == 1 ){ echo " selected "; } echo "> Metallic </option>";
		    echo "<option value=\"2\" "; if ( $typ_vysilace == 2 ){ echo " selected "; } echo "> ap-2,4GHz-OMNI </option>";
		    echo "<option value=\"3\" "; if ( $typ_vysilace == 3 ){ echo " selected "; } echo "> ap-2,4Ghz-sektor </option>";
		    echo "<option value=\"4\" "; if ( $typ_vysilace == 4 ){ echo " selected "; } echo "> ap-2.4Ghz-smerovka </option>";
		    echo "<option value=\"5\" "; if ( $typ_vysilace == 5 ){ echo " selected "; } echo "> ap-5.8Ghz-OMNI </option>";
		    echo "<option value=\"6\" "; if ( $typ_vysilace == 6 ){ echo " selected "; } echo "> ap-5.8Ghz-sektor</option>";
		    echo "<option value=\"7\" "; if ( $typ_vysilace == 7 ){ echo " selected "; } echo "> ap-5.8Ghz-smerovka </option>";
		    echo "<option value=\"8\" "; if ( $typ_vysilace == 8 ){ echo " selected "; } echo "> jiné </option>";
		
	echo '</select>	
	    </td>
	</tr>
	
	<tr>
	    <td><label>Stav: </label></td>
	    <td><select name="stav" >';
	    
	echo "<option value=\"0\" "." class=\"select-nevybrano\" > Není zvoleno </option>";
	echo "<option value=\"1\" "; if ( $stav == 1 ){ echo " selected "; } echo "> v pořádku </option>";
	echo "<option value=\"2\" "; if ( $stav == 2 ){ echo " selected "; } echo "> vytížen </option>";
	echo "<option value=\"3\" "; if ( $stav == 3 ){ echo " selected "; } echo "> přetížen </option>";
	    
	echo '</select></td>
	</tr>
	
	<tr>
	  <td><label>Router id: (na kterém routeru IP alias visí)</label></td>
	  <td>';
	  
	  echo "<select name=\"router_id\" size=\"1\" >";
	  
	  $dotaz_parent = $conn_mysql->query("SELECT * FROM router_list order by nazev");		      
	  echo "<option value=\"0\" class=\"select-nevybrano\" > není zvoleno </option>";
	  
	  while( $data_parent=$dotaz_parent->fetch_array())
	  {
	      echo "<option value=\"".$data_parent["id"]."\" ";      
	      if ( $data_parent["id"] == $router_id )echo " selected ";
	      echo "> ".$data_parent["nazev"]." ( ".$data_parent["ip_adresa"]." ) </option>\n";
	  }
	  echo "</select>\n";
												  
	 echo '</td>
	</tr>';

	echo "<tr>
	  <td><br></td>
	  <td></td>
	</tr>";

	echo "<tr>
	  <td>Mód nodu:</td>
	  <td>";
	  
	  echo "<select size=\"1\" name=\"typ_nodu\" >";
	  
	    echo "<option value=\"0\" style=\"color: gray; \" "; 
		if($typ_nodu == 0 )echo " selected ";
	    echo " >Nezvoleno</option>";
	  
	    echo "<option value=\"1\" style=\"color: #CC0033; \" ";
		if($typ_nodu == 1 )echo " selected ";
	    echo " >Bezdrátová síť</option>";
	    
	    echo "<option value=\"2\" style=\"color: #e37d2b; font-weight: bold;\" ";
		if($typ_nodu == 2 )echo " selected ";
	    echo " >Optická síť</option>";
	
	  echo "</select>";
	  								
	  echo "</td>
	    </tr>";
	
	echo "<tr>
	  <td><br></td>
	  <td></td>
	</tr>\n";
	
	echo "<tr>
	  <td>Vlan id:</td>
	  <td><input type=\"text\" name=\"vlan_id\" size=\"10\" value=\"".$vlan_id."\" ></td>
	 </tr>\n";
		
        if($typ_nodu == 1 )
	{
	 
	 echo '<tr>
	   <td><br></td>
	   <td></td>
	  </tr>

	 <tr>
	  <td><label>Router, kde se provádí filtrace: </label></td>
	  <td>';
	  
	  echo "<select name=\"filter_router_id\" size=\"1\" >";
	  
	  if($rid_recom == "yes")
	  { $sql_filtr = "SELECT id,nazev,ip_adresa FROM router_list WHERE (filtrace = 1) ORDER BY nazev"; }
	  else
	  { $sql_filtr = "SELECT id,nazev,ip_adresa FROM router_list ORDER BY nazev"; }
	  
	  $dotaz_parent = $conn_mysql->query($sql_filtr);		      
	  echo "<option value=\"0\" class=\"select-nevybrano\" > není zvoleno </option>\n";
	  
	  while( $data_parent = $dotaz_parent->fetch_array())
	  {
	      echo "<option value=\"".$data_parent["id"]."\" ";      
	      if( $data_parent["id"] == $filter_router_id ){ echo " selected "; }
	      echo "> ".$data_parent["nazev"]." ( ".$data_parent["ip_adresa"]." ) </option>\n";
	  }
	  echo "</select>\n";
	
	  echo "<span style=\"padding-left: 40px;\">Pouze doporučené: 
		<input type=\"checkbox\" name=\"rid_recom\" value=\"yes\" onclick=\"this.form.submit();\" ";
		if($rid_recom == "yes"){ echo " checked "; } echo " ></span>";
	
	  echo '</td>
	    </tr>';
	
	} 											  
	else{
	  echo "<input type=\"hidden\" name=\"filter_router_id\" value=\"114\" >\n";
	}
	
	 echo '<tr>
	  <td colspan="2"><br></td>
	  <td></td>
	</tr>';

	if($typ_nodu == 2 )
	{
 	  echo ' <tr>
	    <td><label>Typ(model) koncového zařízení (switche): </label></td>
	    <td>';

	  echo "<select name=\"device_type_id\" size=\"1\" >";
	    echo "<option value=\"0\" >default :: AT-8000S/24</option>\n";
	    echo "<option value=\"1\" "; if($device_type_id == 1){ echo " selected "; }
		 echo " >h3c s3100 (26tp-ei) - with mac-vlan</option>\n";
	
	    echo "<option value=\"2\" "; if($device_type_id == 2){ echo " selected "; }
		 echo " >h3c s3100 (26tp-ei) - with DVA</option>\n";
	
	    
	  echo "</select>";
	  
	  echo '</td>
	   </tr>';
	}
	
	echo '<tr>
	  <td colspan="2"><br></td>
	  <td></td>
	</tr>';
					    
	echo '<tr>
	  <td><input type="hidden" name="update_id_new" value="'.$id.'">&nbsp;</td>
	  <td><input type="submit" value="OK" name="B1"></td>
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


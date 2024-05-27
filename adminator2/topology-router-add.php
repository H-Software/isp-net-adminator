<?php

require("include/main.function.shared.php");
require_once("include/config.php"); 
require_once("include/check_login.php");
require_once("include/check_level.php");

if ( !( check_level($level,86) ) )
{ // neni level

 $stranka='nolevelpage.php';
 header("Location: ".$stranka);
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require("include/charset.php"); 

?>

<title>Adminator2 - Topology - Router add/change</title>

</head>

<body>

<?php include ("head.php"); ?> 

<?php include ("category.php"); ?> 

<tr>
 <td colspan="2" bgcolor="silver" >
   <?php include("topology-cat2.php"); ?>
 </td>
</tr>
       
 <tr>
 <td colspan="2">

 <?php

  $ag = new Aglobal();      
  $ag->conn_mysql = $conn_mysql;
  $ag->conn_pgsql = $db_ok2;
  
  //naplneni promenejch
  $nod_find = $_POST["nod_find"];
  $odeslat = $_POST["odeslat"];	  
  
  $selected_nod = $_POST["selected_nod"];

  $nazev=$_POST["nazev"];
  $ip_adresa=$_POST["ip_adresa"];
  $parent_router=$_POST["parent_router"];
  $monitoring=$_POST["monitoring"];
  $monitoring_cat=$_POST["monitoring_cat"];
  $alarm=$_POST["alarm"];
	  
  $filtrace=$_POST["filtrace"];
  $mac=$_POST["mac"];
  $monitoring_cat=$_POST["monitoring_cat"];
  $update_id=$_POST["update_id"];
  
  $poznamka = $_POST["poznamka"];
  
  //kontrola spravnosti promennych
  
  if($odeslat == "OK"){ //zda je odesláno
  
    //monitoring potrebuje i monitoring kategorii
    if( ($monitoring_cat == 0) )
    {
      echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">
       Nelze uložit, musíte vybrat kategorii pro monitoring. </div>";
	     
      $error=1;
    }

    if( ($monitoring == 1) )
    {
    
      //test api a spravnosti konfigurace routeru  
      $rs_test = $ag->test_router_for_monitoring($update_id);
      
      if($rs_test[0] === false)
      {
        echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">".
    		"Nelze uložit s parametrem \"<b>Monitoring - Ano</b>\", selhala kontrola nastavení či stavu routeru pro monitoring.</div>";

        echo "<div style=\"color: grey;\" >výpis testu: <pre>".htmlspecialchars($rs_test[1])."</pre></div>";	     
      
        $error=1;
      
      } //end if rs_test === false
      
    } //end od if monitoring == 1
   
    //nadrazený router musí být vyplnen
    if( !(intval($parent_router) > 0) )
    {
      echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">
       Nelze uložit, je třeba vyplnit pole \"Nadřazený router\" (kvůli filtraci a QoSu na reinhardech). </div>";
    
      $error=1;
    }
    
    //kontrola IP adresy
    if( (strlen($ip_adresa) > 0) ){
      
        if( !(objektypridani::validateIpAddress($ip_adresa)) )
        {
            echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">".
    	    "IP adresa (".$ip_adresa.") není ve správném formátu !!!</div>";

	    $error=1;
        }
    }
    
    //check dns nazvu
    if( (strlen($nazev) > 0) ){

	//kontrola správnosti zadání
        $dns_check=ereg('^([[:alnum:]]|\.|-)+$',$nazev);
	
	if( !($dns_check) )
	{
	    echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">".
		    "DNS záznam (".$nazev.") není ve správnem formátu !!!</div>";
  
    	    $error=1;    
	}
    
	//kontrola delky
	if( (strlen($nazev) > 40 ) ){
	
	    echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">".
		    "DNS záznam (".$nazev.") je moc dlouhý!!! Maximální délka je 40 znaků.</div>";
  
    	    $error=1;
	}
    }
     
    //kontrola mac adresy
    if( (strlen($mac) > 0) ){

	$mac_check=ereg('^([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})$',$mac);

        if( !($mac_check) )
        {
            echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">".
		"MAC adresa (".$mac.") není ve správném formátu !!!</div>";
    	    
    	    $error=1;
        }
    }
    
    //povinné údaje
    if( (strlen($nazev) == 0) or (strlen($ip_adresa) == 0) or (strlen($parent_router) == 0) ){

      echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">
       Nelze uložit, nejsou vyplněny všechny potřebné údaje. (Název, IP adresa, Nadřazený router). </div>";
    
      $error=1;
    
    }
    
  } //konec if odeslat == OK
  
  echo "<div style=\"padding-bottom: 10px; padding-top: 10px; font-size: 18px; \">Přidání/úprava routeru </div>";
  
  if( ($odeslat == "OK") and ($error != "1") )
  {
    //proces ukladani ..    

    //vypsat co se vlozilo
    echo "<b>Zadáno do formuláře : </b><br><br>";
    
    echo "<b>Název: </b>".$nazev."<br>";
    echo "<b>IP adresa: </b>".$ip_adresa."<br>";
    echo "<b>MAC: </b>".$mac."<br>";
    
    echo "<br>";
    
    echo "<b>Nadřazený router: </b>";
	$parent_router_name = mysql_result(mysql_query("SELECT nazev FROM router_list WHERE id = '".intval($update_id)."' "), 0);

    echo $parent_router_name." (id: ".$parent_router.")<br>";

    echo "<br>";
    
    echo "<b>Monitorování: </b>";
	    if($monitoring == 1){ echo "Ano"; }
	    elseif($monitoring == 0){ echo "Ne"; }
	    else{ echo "nelze zjistit"; }
    echo "<br>";
    echo "<b>Monitorování kategorie: </b>";
	$monitoring_cat_name = mysql_result(mysql_query("SELECT jmeno FROM kategorie WHERE id = '".intval($monitoring_cat)."' "), 0);	    
    echo $monitoring_cat_name." (id: ".$monitoring_cat.")<br>"; 

    echo "<br>";

    echo "<b>Alarm: </b>";
    	    if($alarm == 1){ echo "Ano"; }
	    elseif($alarm == 0){ echo "Ne"; }
	    else{ echo "nelze zjistit"; }
    echo "<br>";    
    echo "<b>Filtrace: </b>";
    	    if($filtrace == 1){ echo "Ano"; }
	    elseif($filtrace == 0){ echo "Ne"; }
	    else{ echo "nelze zjistit"; }
    echo "<br>";         
    echo "<b>Nadřazený nod (kvůli filtraci): </b>";
	$nod_name = mysql_result(mysql_query("SELECT jmeno FROM nod_list WHERE id = '".intval($selected_nod)."' "), 0);	    
    
    echo $nod_name." (id: ".$selected_nod.")<br>";         

    echo "<br><b>Poznámka: </b>".addslashes($poznamka)."<br>";         
    	  
    if( $update_id > 0 )
    {
	
	$pole="<b>akce: uprava routeru;</b><br>";
          
	// prvne zjistime puvodni hodnoty
	$dotaz_top=$conn_mysql->query("SELECT nazev, ip_adresa, parent_router, mac, monitoring, 
					monitoring_cat, alarm, filtrace, id_nodu, poznamka 
				FROM router_list WHERE id = '".intval($update_id)."' ");
				
        $dotaz_top_radku = $dotaz_top->num_rows();

        if( $dotaz_top_radku < 1)
        { 
	 echo "<span style=\"color: red; font-size: 16px; font-weight: bold;\">
          <p> Chyba! Nelze načíst zdrojové hodnoty pro úpravu. </p></span>";
        }
        else
        {
          while($data_top=mysql_fetch_array($dotaz_top)):
	  
	   $pole_puvodni_data["nazev"] = $data_top["nazev"];
	   $pole_puvodni_data["ip_adresa"] = $data_top["ip_adresa"];
	   $pole_puvodni_data["parent_router"] = $data_top["parent_router"];
	   $pole_puvodni_data["mac"] = $data_top["mac"];
	   $pole_puvodni_data["monitoring"] = $data_top["monitoring"];
	   $pole_puvodni_data["monitoring_cat"] = $data_top["monitoring_cat"];
	   $pole_puvodni_data["alarm"] = $data_top["alarm"];
	   $pole_puvodni_data["filtrace"] = $data_top["filtrace"];
	   $pole_puvodni_data["id_nodu"] = $data_top["id_nodu"];
	   $pole_puvodni_data["poznamka"] = $data_top["poznamka"];
	  
	  endwhile;
	}
	
	$poznamka = addslashes($poznamka);
	
	if( strlen($mac) <= 0 )
	{ $mac = "00:00:00:00:00:00"; }
	 	
	$uprava=$conn_mysql->query("UPDATE router_list SET nazev='$nazev', ip_adresa='$ip_adresa', parent_router='$parent_router',
	            		mac='$mac', monitoring='$monitoring', monitoring_cat='$monitoring_cat', alarm='$alarm',
				filtrace='$filtrace', id_nodu='$selected_nod', poznamka = '$poznamka' WHERE id=".intval($update_id)." Limit 1 ");
			    
	if($uprava){ echo "<br><span style=\"color: green; font-size: 18px; \">Záznam úspěšně upraven.</span><br><br>"; }
        else{ echo "<div style=\"color: red; font-weight: bold; font-size: 16px; \">Záznam nelze upravit v databázi. </div>"; }
		       
	//ulozeni do archivu zmen
	require("topology-router-add-inc-archiv-zmen.php");
        
        //automatické restarty
	if( ereg(".*změna.*Alarmu.*z.*", $pole3) )
	{
	  //kvuli alarmu
	  Aglobal::work_handler("15"); 		//trinity - Monitoring I - Footer-restart
        }
	
	if( ereg(".*změna.*Monitorování.*", $pole3) or ereg(".*změna.*Monitoring kategorie.*", $pole3) )
	{
	  //kvuli monitoringu - feeder asi nepovinnej
	  Aglobal::work_handler("18"); 		//monitoring - Monitoring II - Feeder-restart
	  Aglobal::work_handler("22"); //monitoring - Monitoring II - checker-restart	  
        }
	
	if( ereg(".*změna.*Nadřazený router.*", $pole3) )
	{
	     Aglobal::work_handler("1");        //reinhard-3 (ros) - restrictions (net-n/sikana)
	     Aglobal::work_handler("20");       //reinhard-3 (ros) - shaper (client's tariffs)
	          
	     Aglobal::work_handler("24");       //reinhard-5 (ros) - restrictions (net-n/sikana)
	     Aglobal::work_handler("23");       //reinhard-5 (ros) - shaper (client's tariffs)
	                    
	     Aglobal::work_handler("13");       //reinhard-wifi (ros) - shaper (client's tariffs)
	     Aglobal::work_handler("2");        //reinhard-wifi (ros) - restrictions (net-n/sikana)
	                              
	     Aglobal::work_handler("14");       //(trinity) filtrace-IP-on-Mtik's-restart
	     
	}
	
	if( ereg(".*změna.*Připojného bodu.*", $pole3) )
	{
	     Aglobal::work_handler("14");	//(trinity) filtrace-IP-on-Mtik's-restart
	}

	if( ereg(".*změna.*Filtrace.*", $pole3) )
	{
	     Aglobal::work_handler("14");	//(trinity) filtrace-IP-on-Mtik's-restart
	}
		
	if( ereg(".*změna.*", $pole3) )
	{ 
	  //radsi vzdy (resp. zatim)
	  Aglobal::work_handler("19"); 		//trinity - adminator - synchro_router_list
        }
    
    }
    else
    {
          // rezim pridani	    
    	  $poznamka = addslashes($poznamka);
	    
	  if( strlen($mac) <= 0 )
	  { $mac = "00:00:00:00:00:00"; }
	 
          $add=$conn_mysql->query("INSERT INTO router_list (nazev,ip_adresa, parent_router,mac, monitoring, alarm, monitoring_cat, filtrace, id_nodu, poznamka) 
					    VALUES ('$nazev','$ip_adresa','$parent_router','$mac','$monitoring','$alarm','$monitoring_cat', '$filtrace', '$selected_nod', '$poznamka' ) ");

          if($add){ echo "<br><div style=\"color: green; font-size: 18px; \">Záznam úspěšně vložen.</div><br>"; }
          else 
	  { 
	    echo "<div style=\"color: red; font-weight: bold; font-size: 16px; \">Záznam nelze vložit do databáze. </div>";
	    echo "<div style=\"\">".mysql_error()."</div>";
	  }
	 
          // pridame to do archivu zmen
          $pole="<b>akce: pridani routeru;</b><br>";
          $pole .= " nazev: ".$nazev.", ip adresa: ".$ip_adresa.", monitoring: ".$monitoring.", monitoring_cat: ".$monitoring_cat;
	  $pole .= " alarm: ".$alarm.", parent_router: ".$parent_router.", mac: ".$mac.", filtrace: ".$filtrace.", id_nodu: ".$selected_nod;

	  if( $add == 1 ){ $vysledek_write=1; }	
          $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write') ");
  
          Aglobal::work_handler("13"); //reinhard-wifi (ros) - shaper (client's tariffs)
          Aglobal::work_handler("20"); //reinhard-3 (ros) - shaper (client's tariffs)
    	  Aglobal::work_handler("23"); //reinhard-5 (ros) - shaper (client's tariffs)
                                       
          Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart
          
	//automatické restarty
	if( $alarm == 1 )
	{
	  //kvuli alarmu
	  Aglobal::work_handler("15"); //trinity - Monitoring I - Footer-restart
        }
	
	if( $monitoring == 1 )
	{
	  //kvuli monitoringu
	  Aglobal::work_handler("18"); //monitoring - Monitoring II - Feeder-restart
	  Aglobal::work_handler("22"); //monitoring - Monitoring II - checker-restart	  
        }
	 
	//radsi vzdy (resp. zatim)
	Aglobal::work_handler("19"); //trinity - adminator - synchro_router_list
 		
    } //konec if/else update_id > 0

    //
    // akce pri uprave i pri vlozeni
    //
    
    //nic :-)
	
  } // konec odeslat == OK
  else
  {
    //nechceme ukladat, tj. zobrazit form
    
    //pokud update, tak zjistit predchozi hodnoty
    if( $update_id > 0 and ($odeslat != "OK") )
    {
	// nacteni promennych, pokud se nedna o upravu a neodeslal sem form
		
	$dotaz_top=$conn_mysql->query("SELECT * FROM router_list WHERE id = '".intval($update_id)."' ");
        $dotaz_top_radku=$dotaz_top->num_rows;

        if ( $dotaz_top_radku < 1)
        { echo "<span style=\"color: red; font-size: 16px; font-weight: bold;\">
              <p> Chyba! Nelze načíst zdrojové hodnoty pro úravu. </p></span>";
        }
	else
        {
            while($data_top=$dotaz_top->fetch_array()):
	
		if($nazev == "") 	 $nazev=$data_top["nazev"];
		if($ip_adresa == "")     $ip_adresa=$data_top["ip_adresa"];
		if($parent_router == "") $parent_router=$data_top["parent_router"];
		    
		if($mac == "")           $mac=$data_top["mac"];
		if($filtrace == "")      $filtrace=$data_top["filtrace"];	    
		if($monitoring == "")    $monitoring=$data_top["monitoring"];
		    
		if($monitoring_cat == "") $monitoring_cat=$data_top["monitoring_cat"];
		if($alarm == "")         $alarm=$data_top["alarm"];
		if($poznamka == "")      $poznamka = $data_top["poznamka"];
		if($selected_nod == "")  $selected_nod = $data_top["id_nodu"];
		    
	    endwhile;
	}

    }
	
        //zobrazime formular

         print '
         <form method="POST" action="" name="form1">
         <table border="0" width="100%" id="table2">
         <tr>
    	    <td width="200px"><label>Název: </label></td>
	    <td><input type="text" name="nazev" size="30" value="'.$nazev.'"></td>
         </tr>

         <tr>
            <td><label>IP adresa : </label></td>
    	    <td><input type="text" name="ip_adresa" size="20" value="'.$ip_adresa.'" ></td>
         </tr>

         <tr>
            <td><label>Nadřazený router: </label></td>';
	 
	 echo "<td>";
    	 
	    echo "<select name=\"parent_router\" size=\"1\" >";
	    
	    $dotaz_parent = $conn_mysql->query("SELECT * FROM router_list ORDER BY nazev");
	    
	    echo "<option value=\"0\" class=\"select-nevybrano\" > není zvoleno </option>";
	    
	    while( $data_parent=$dotaz_parent->fetch_array())
	    {
		echo "<option value=\"".$data_parent["id"]."\" ";
		
		if ( $data_parent["id"] == $parent_router){ echo " selected "; }
		echo "> ".$data_parent["nazev"]." ( ".$data_parent["ip_adresa"]." ) </option>";
	    }
	    echo "</select></td>";
	    
	 echo "</tr>";

	echo "<tr><td><br></td></tr>";
	
	echo "<tr>";
	
	    echo "<td><label>MAC: </label></td>";
	    echo "<td><input type=\"text\" name=\"mac\" size=\"20\" maxlength=\"17\" value=\"".$mac."\" ></td>";
	    
	echo "</tr>";
	
	echo "<tr>";
		
        echo " <td><label>Monitoring: </label></td>";
	echo " <td>";
	
	    echo "<select name=\"monitoring\" size=\"1\" >";
	    
		echo "<option value=\"0\" ";
		if ( ($monitoring == 0) or !isset($monitoring) ){ echo " selected "; }
		echo " > Ne </option>";
		
		echo "<option value=\"1\" ";
		if ( $monitoring == 1){ echo " selected "; }
		echo "> Ano </option>";
		
	    echo "</select>";
	    	    
	//klik na pregenerovaní fajlů
	echo "<span style=\"padding-left: 100px;\">Ruční vynucené přegenerování souborů (pro monitoring2) ".
	      "<a target=\"_new\" href=\"https://monitoring.adminator.net/mon/www/rb_all.php?ip=".$ip_adresa."&only_create=only_create\">zde</a>".
	      "</span>";
	
	echo "</td>";
	
	echo "</tr>";
	
	echo "<tr>";
		
        echo " <td><label>Monitoring kategorie: </label></td>";
	echo " <td>";
	
	    echo "<select name=\"monitoring_cat\" size=\"1\" >";
	    
	    $dotaz_cat = $conn_mysql->query("SELECT * FROM kategorie WHERE sablona LIKE 4 order by id");
	
		echo "<option value=\"0\" class=\"select-nevybrano\"> Není zvoleno </option>";
		
	    while( $data_cat=$dotaz_cat->fetch_array())
	    {
		echo "<option value=\"".$data_cat["id"]."\" ";
		
		if ( $data_cat["id"] == $monitoring_cat){ echo " selected "; }
		echo "> ".$data_cat["jmeno"]." </option>";
	    }	
	    
	    echo "</select>";
	    	    
	echo "</td>";
	
	echo "</tr>";
		
	echo "<tr>";
		
        echo " <td><label>Alarm: </label></td>";
	echo " <td>";
	
	    echo "<select name=\"alarm\" size=\"1\" >";
	    
		echo "<option value=\"0\" "; 
		    if ( $alarm == 0 or !isset($alarm) ){ echo " selected "; } 
		    echo "> Ne </option>";
		
		echo "<option value=\"1\" ";
		    if ( $alarm == 1){ echo " selected "; } 
		    echo " > Ano </option>";
		
	    echo "</select>";
	    	    
	echo "</td>";
	
	echo "</tr>";

	echo "
        <tr>
          <td colspan=\"2\">&nbsp;</td>
        </tr>";

	echo "
        <tr>
          <td>Nadřazený nod: (kvůli filtraci)</td>
          <td>";

        $sql_nod = "SELECT * from nod_list WHERE ( jmeno LIKE '%$nod_find%' ";
        $sql_nod .= " OR ip_rozsah LIKE '%$nod_find%' OR adresa LIKE '%$nod_find%' ";
        $sql_nod .= " OR pozn LIKE '%$nod_find%' ) ORDER BY jmeno ASC ";

       $vysledek=$conn_mysql->query($sql_nod);
       //$vysledek=$conn_mysql->query("SELECT * from nod_list ORDER BY jmeno ASC" );
       $radku=$vysledek->num_rows;

       print '<select size="1" name="selected_nod" onChange="self.document.forms.form1.submit()" >';

       if( ($radku==0) )
       {
         echo "<option value=\"0\" style=\"color: gray; \" selected >nelze zjistit / žádný nod nenalezen </option>";
       }
       else
       {
         echo '<option value="0" style="color: gray; font-style: bold; "';
          if( (!isset($selected_nod)) ){ echo "selected"; }
         echo ' > Není vybráno</option>';

          while ($zaznam2=$vysledek->fetch_array() )
          {
            echo '<option value="'.$zaznam2["id"].'"';
              if ( ( $selected_nod == $zaznam2["id"]) ){ echo " selected "; }
            echo '>'." ".$zaznam2["jmeno"]." ( ".$zaznam2["ip_rozsah"]." )".'</option>'." \n";
          } //konec while
        } //konec else

        print '</select>';
  
	echo "</td>
        </tr>";
	
	echo "
        <tr>
          <td></td>
	  <td><span style=\"padding-right: 20px;\">hledání:</span>
	    <input type=\"text\" name=\"nod_find\" size=\"30\" value=\"".$nod_find."\" >
	    <span style=\"padding-left: 20px;\">
		<input type=\"button\" value=\"Filtrovat nody\" name=\"G\" onClick=\"self.document.forms.form1.submit()\" >
	    </span>
	  </td>
	</tr>";

	echo "
        <tr>
          <td colspan=\"2\"><br></td>
        </tr>";
		
	echo "<tr>";
		
        echo " <td><label>Filtrace: </label></td>";
	echo " <td>";
	
	    echo "<select name=\"filtrace\" size=\"1\" >";
	    
		echo "<option value=\"0\" "; 
		    if ( $filtrace == 0 or !isset($filtrace) ){ echo " selected "; } 
		    echo "> Ne </option>";
		
		echo "<option value=\"1\" ";
		    if ( $filtrace == 1){ echo " selected "; } 
		    echo " > Ano </option>";
		
	    echo "</select>";
	    	    
	echo "</td>";	
	echo "</tr>";
	

	echo "
        <tr>
          <td colspan=\"2\"><br></td>
        </tr>";

	echo "
        <tr>
          <td>Poznámka</td>
	  <td><textarea name=\"poznamka\" rows=\"8\" cols=\"40\">".$poznamka."</textarea></td>
        </tr>";

	echo '
        <tr>
          <td><br></td>
          <td></td>
        </tr>

         <tr>
         <td></td>
         <td><input type="hidden" name="update_id" value="'.$update_id.'"><input type="submit" value="OK" name="odeslat">

          </td>
         </tr>

         </table>

        </form>';
     }
      
    ?>

    <!-- konec vlastniho obsahu -->	
  </td>
  </tr>
  
 </table>

</body> 
</html> 

<?php

//funkce

    //function to validate ip address format in php by Roshan Bhattarai(http://roshanbh.com.np)
    function validateIpAddress($ip_addr)
    {
        //first of all the format of the ip address is matched
        if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip_addr))
        {
          //now all the intger values are separated
          $parts=explode(".",$ip_addr);
          //now we need to check each part can range from 0-255
          foreach($parts as $ip_parts)
          {
            if(intval($ip_parts)>255 || intval($ip_parts)<0)

            return false; //if number is not within range of 0-255
          }

          return true;
        }
        else
          return false; //if format of ip address doesn't matches
    }

    function checkcislo($cislo)
    {
     $rra_check=ereg('^([[:digit:]]+)$',$cislo);

     if ( !($rra_check) )
     {
      global $fail;     $fail="true";
      global $error;    $error .= "<H4>Zadaný číselný údaje ( ".$cislo." ) není ve  správném formátu !!! </H4>";
     }

    } //konec funkce check cislo

    function checkdns ($dns)
    {
    $dns_check=ereg('^([[:alnum:]]|\.|-)+$',$dns);
    if ( !($dns_check) )
    {
     global $fail;      $fail="true";
     global $error;     $error .= "<div class=\"objekty-add-fail-dns\"><H4>DNS záznam ( ".$dns." ) není ve správnem formátu !!! </H4></div>";
    }

    } // konec funkce check rra


?>

<?php
    
if (  ( $update_id > 0 ) ) { $update_status=1; }

if( ( $update_status==1 and !( isset($send) ) ) )
{
//rezim upravy

  $dotaz_upd = pg_query("SELECT * FROM objekty WHERE id_komplu='".intval($update_id)."' ");
  $radku_upd=pg_num_rows($dotaz_upd);
 
  if ( $radku_upd==0 ) echo "Chyba! Požadovaná data nelze načíst! ";
  else
  {
    
    while($data=pg_fetch_array($dotaz_upd)):
    
    // primy promenny 
    $dns=$data["dns_jmeno"];  
    $ip=$data["ip"];	 
    $mac=$data["mac"];
    $typ=$data["typ"];	$pozn=$data["poznamka"]; 
    $selected_nod=$data["id_nodu"];

    $sikana_text=$data["sikana_text"];
    $client_ap_ip=$data["client_ap_ip"];
    
    $id_tarifu=$data["id_tarifu"];

    // neprimy :) -> musi se zkonvertovat
    
    $dov_net_l=$data["dov_net"];	
    if( $dov_net_l =="a" ){ $dov_net=2; }
    else{ $dov_net=1; }    
    
    $verejna_l=$data["verejna"];	
    
    if( $data["tunnelling_ip"] == "1")
    { //tunelovaná verejka 
      $typ_ip = "4";
      
      $tunnel_user = $data["tunnel_user"];
      $tunnel_pass = $data["tunnel_pass"];
      
    } 
    elseif( $verejna_l=="99" ) 
    { $typ_ip="1"; }
    else { 
      $typ_ip="2"; 
      $vip_rozsah=$verejna_l; 
    }
    
    $sikana_status_l=$data["sikana_status"]; 
    if( preg_match("/a/",$sikana_status_l) ) { $sikana_status=2; } else { $sikana_status=1; }
    $sikana_cas_l=$data["sikana_cas"];
    if( strlen($sikana_cas_l) > 0 ) { $sikana_cas=$sikana_cas_l; }
     
    endwhile;
    
  }
    
}
else
{
// rezim pridani, ukladani

 $dns=$_POST["dns"];		$ip=$_POST["ip"];			$typ=$_POST["typ"];	

 $typ_ip=$_POST["typ_ip"];	$dov_net=$_POST["dov_net"];		$id_tarifu = $_POST["id_tarifu"];
 $mac=$_POST["mac"];		$verejna=$_POST["verejna"];
 $typ_ip=$_POST["typ_ip"];	$vip_rozsah=$_POST["vip_rozsah"];	$pozn=$_POST["pozn"];

 //systémove
 $send=$_POST["send"];	
 $selected_nod=$_POST["selected_nod"];

 // dalsi
 $sikana_status = $_POST["sikana_status"];	 $sikana_cas = $_POST["sikana_cas"];	$sikana_text = $_POST["sikana_text"];

 //$vip_snat_lip = $_POST["vip_snat_lip"];
 $client_ap_ip = $_POST["client_ap_ip"];

 $tunnel_user = $_POST["tunnel_user"];
 $tunnel_pass = $_POST["tunnel_pass"];

}

require_once("include/class.php");

  //co mame: v promeny selected_nod mame id nodu kam se to bude pripojovat
  // co chcete: ip adresu , idealne ze spravnyho rozsahu :)

objektypridani::generujdata($selected_nod, $typ_ip, $dns, $conn_mysql); 

if( (strlen($ip) > 0) )  { objektypridani::checkip($ip); }

if( ( strlen($dns) > 0 ) )  { objektypridani::checkdns($dns); }
if( ( strlen($mac) > 0 ) ) { objektypridani::checkmac($mac); }	
if( (strlen($sikana_cas) > 0 ) ) { objektypridani::checkcislo($sikana_cas); }
if( (strlen($selected_nod) > 0 ) ) { objektypridani::checkcislo($selected_nod); }

if( (strlen($client_ap_ip) > 0 ) ) { objektypridani::checkip($client_ap_ip); }

if( $sikana_status == 2 ) { 

	objektypridani::checkSikanaCas($sikana_cas); 
	
	objektypridani::checkSikanaText($sikana_text); 

}


if( $typ_ip == 4 )
{
  if( (strlen($tunnel_user) > 0 ) ){ objektypridani::check_l2tp_cr($tunnel_user); }
  if( (strlen($tunnel_pass) > 0 ) ){ objektypridani::check_l2tp_cr($tunnel_pass); }
}

// jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
if( ( ($dns != "") and ($ip != "") ) and ( $selected_nod > 0 ) and ( ($id_tarifu >= 0) ) ):

if( ( $update_status!=1 ) )
{
 $ip_find=$ip."/32";

 //zjisti jestli neni duplicitni dns, ip
 $MSQ_DNS = pg_query("SELECT ip FROM objekty WHERE dns_jmeno LIKE '$dns' ");
 $MSQ_IP = pg_query("SELECT ip FROM objekty WHERE ip <<= '$ip_find' ");
    
 if (pg_num_rows($MSQ_DNS) > 0){ $error .= "<h4>Dns záznam ( ".$dns." ) již existuje!!!</h4>"; $fail = "true"; }
 if (pg_num_rows($MSQ_IP) > 0){ $error .= "<h4>IP adresa ( ".$ip." ) již existuje!!!</h4>"; $fail = "true"; }

 //duplicitni tunnel_pass/user
 if($typ_ip==4)
 {
   $MSQ_TUNNEL_USER = pg_query("SELECT tunnel_user FROM objekty WHERE tunnel_user LIKE '$tunnel_user' ");
   $MSQ_TUNNEL_PASS = pg_query("SELECT tunnel_pass FROM objekty WHERE tunnel_pass LIKE '$tunnel_pass' ");
   
   if(pg_num_rows($MSQ_TUNNEL_USER) > 0)
   { $error .= "<h4>Login k tunelovacímu serveru (".$tunnel_user.") již existuje!!!</h4>"; $fail = "true"; }
   if(pg_num_rows($MSQ_TUNNEL_PASS) > 0)
   { $error .= "<h4>Heslo k tunelovacímu serveru (".$tunnel_pass.") již existuje!!!</h4>"; $fail = "true"; }  
 }
 
}

// check v modu uprava
if ( ( $update_status==1 and (isset($odeslano)) ) )
{
 $ip_find=$ip."/32";
 
 //zjisti jestli neni duplicitni dns, ip
 $MSQ_DNS2 = pg_exec($db_ok2, "SELECT * FROM objekty WHERE ( dns_jmeno LIKE '$dns' AND id_komplu != '".intval($update_id)."' ) ");
 $MSQ_IP2 = pg_exec($db_ok2, "SELECT * FROM objekty WHERE ( ip <<= '$ip_find' AND id_komplu != '".intval($update_id)."' ) ");

 if(pg_num_rows($MSQ_DNS2) > 0){ $error .= "<h4>Dns záznam ( ".$dns." ) již existuje!!!</h4>"; $fail = "true"; }
 if(pg_num_rows($MSQ_IP2) > 0){ $error .= "<h4>IP adresa ( ".$ip." ) již existuje!!!</h4>"; $fail = "true"; }


 //duplicitni tunnel_pass/user
 if($typ_ip==4)
 {
   $MSQ_TUNNEL_USER = pg_query("SELECT tunnel_user FROM objekty WHERE ( tunnel_user LIKE '$tunnel_user' AND id_komplu != '".intval($update_id)."' ) ");
   $MSQ_TUNNEL_PASS = pg_query("SELECT tunnel_pass FROM objekty WHERE ( tunnel_pass LIKE '$tunnel_pass' AND id_komplu != '".intval($update_id)."' ) ");
   
   if(pg_num_rows($MSQ_TUNNEL_USER) > 0)
   { $error .= "<h4>Login k tunelovacímu serveru (".$tunnel_user.") již existuje!!!</h4>"; $fail = "true"; }
   if(pg_num_rows($MSQ_TUNNEL_PASS) > 0)
   { $error .= "<h4>Heslo k tunelovacímu serveru (".$tunnel_pass.") již existuje!!!</h4>"; $fail = "true"; }  
 }
 
}

// checknem stav vysilace a filtraci
try {
  $msq_stav_nodu = $conn_mysql->query("SELECT stav, router_id FROM nod_list WHERE id= '".intval($selected_nod)."' ");
  $msq_stav_nodu_radky = $msq_stav_nodu->num_rows;
} catch (Exception $e) {
  die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
}

while ($data=$msq_stav_nodu->fetch_array() )
{ $stav_nodu = $data["stav"]; $router_id = $data["router_id"]; }

if ( $stav_nodu == 2 )
{ $info .= "<div style=\"color: orange; \" ><h4>UPOZORNĚNÍ: Tento přípojný bod je vytížen, mohou se vyskytovat problémy. </h4></div>"; }
elseif ( ( $stav_nodu == 3 ) and ( $update_status == 1 ) )
{ $info .= "<div style=\"color: orange; \"><h4>UPOZORNĚNÍ: Tento přípojný bod je přetížen. </h4></div>"; }
elseif ( $stav_nodu == 3)
{ $fail="true"; $error .= "<div style=\"color: red; \" ><h4>Tento přípojný bod je přetížen, vyberte prosím jiný. </h4></div>";  }

// kontrola jestli se muze povolit inet / jestli jsou pozatavené fakturace
$poz_fakt_clovek=pg_query("SELECT id_cloveka, dov_net FROM objekty WHERE id_komplu = '".intval($update_id)."' ");
$poz_fakt_clovek_radku=pg_num_rows($poz_fakt_clovek);

while ($data_poz_f_clovek=pg_fetch_array($poz_fakt_clovek))
{ $id_cloveka=$data_poz_f_clovek["id_cloveka"]; 
    $dov_net_puvodni=$data_poz_f_clovek["dov_net"];
}

if ( ( ($id_cloveka > 1) and ( $update_status==1 ) ) )
{

$pozastavene_fakt=pg_query("SELECT billing_suspend_status FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka)."' ");
$pozastavene_fakt_radku=pg_num_rows($pozastavene_fakt);

if ( $pozastavene_fakt_radku == 1)
{
 while ( $data_poz_fakt=pg_fetch_array($pozastavene_fakt) )
 { $billing_suspend_status = intval($data_poz_fakt["billing_suspend_status"]); }
}
else
{ echo "Chyba! nelze vybrat vlastníka."; }

// echo "debug: id_fakturacni_skupiny: ".$pozastavene_fakturace_id." id_cloveka: $id_cloveka ,dov_net-puvodni: $dov_net_puvodni , povolen inet: $dov_net";

if( $billing_suspend_status == 1)
{
 // budeme zli
 // prvne zjisteni predchoziho stavu
 

 if( ( ($dov_net_puvodni == "n") and ($dov_net == 2 ) ) )
 {
    $fail="true"; 
    $error.="<div class=\"objekty-add-mac\" >Klient má pozastavené fakturace. Před povolením internetu je potřeba změnit u vlastníka pole \"Pozastavené fakturace\". </div>"; 
 }
 
}

} // konec if jestli id_cloveka > 1 and update == 1

//checkem jestli se macklo na tlacitko "OK" :)
if( preg_match("/^OK$/",$odeslano) ) { echo ""; }
else 
{ 
    $fail="true"; 
    $error.="<div class=\"objekty-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>"; 
}

//ulozeni
if ( !( isset($fail) ) ) 
{ 
 // priprava promennych
 
 if ( $dov_net == 2 ) 
 { $dov_net_w ="a"; } 
 else { $dov_net_w="n"; }
 
 if ( $typ == 3 ) { $dov_net_w="a"; }
 
 if ($typ_ip == 1)
 { $verejna_w="99"; } 
 elseif( $typ_ip == 3 )
 { 
    $verejna_w=$vip_rozsah;
    //$vip_snat="1";    
 }
 elseif( $typ_ip == 4 )
 {
  //tunelovane ip adresy
  $tunnelling_ip=1; //flag pro selekci tunelovanych ip
  $verejna_w=$vip_rozsah; //flag ze je jedna o verejnou (asi jen pro DNS)
  
  $tunnel_user_w = $tunnel_user;
  $tunnel_pass_w = $tunnel_pass;
     
 }
 else
 {
   //obyc verejka 
    $verejna_w=$vip_rozsah; 
    $tunnelling_ip="0"; 
 }
 
 if( $sikana_status =="2" )
 { $sikana_status_w='a'; } 
 else
 { $sikana_status_w='n'; }
    
 $sikana_cas = intval($sikana_cas);
 
 if($update_status =="1")
 {
    // rezim upravy
     
    if( !(check_level($level,29) ) ) 
    {
	echo "<br><div style=\"color: red; font-size: 18px; \" >Objekty nelze upravovat, není dostatečné oprávnění. </div><br>";
        exit;
    }
    else
    {
	//prvne stavajici data docasne ulozime 
	$pole2 .= "<b>akce: uprava objektu; </b><br>";
    	 
	$sql_rows = "id_komplu, dns_jmeno, ip, mac, client_ap_ip, dov_net, id_tarifu, typ, poznamka, verejna, ";
	$sql_rows .= "sikana_status, sikana_cas, sikana_text, upravil, id_nodu, ";
	$sql_rows .= "tunnelling_ip, tunnel_user, tunnel_pass";
    
	$vysl4=pg_query("SELECT ".$sql_rows." FROM objekty WHERE id_komplu='".intval($update_id)."' ");

	if( ( pg_num_rows($vysl4) <> 1 ) )
	{ echo "<div>Chyba! Nelze zjistit puvodni data pro ulozeni do archivu </div>"; }
	else  
	{ 
    	    while ($data4=pg_fetch_array($vysl4) ){
	
		$pole_puvodni_data["id_komplu"]=$data4["id_komplu"];		
		$pole_puvodni_data["dns_jmeno"]=$data4["dns_jmeno"];	
		$pole_puvodni_data["ip"]=$data4["ip"];
		$pole_puvodni_data["mac"]=$data4["mac"];		
		$pole_puvodni_data["client_ap_ip"]=$data4["client_ap_ip"];	
		$pole_puvodni_data["dov_net"]=$data4["dov_net"];	
		$pole_puvodni_data["id_tarifu"]=$data4["id_tarifu"];
		$pole_puvodni_data["typ"]=$data4["typ"];
		$pole_puvodni_data["poznamka"]=$data4["poznamka"];	
		$pole_puvodni_data["verejna"]=$data4["verejna"];
		$pole_puvodni_data["sikana_status"]=$data4["sikana_status"];	
		$pole_puvodni_data["sikana_cas"]=$data4["sikana_cas"];
		$pole_puvodni_data["sikana_text"]=$data4["sikana_text"];
		$pole_puvodni_data["upravil"]=trim($data4["upravil"]);	
		$pole_puvodni_data["id_nodu"]=$data4["id_nodu"];
	
		$pole_puvodni_data["tunnelling_ip"]=$data4["tunnelling_ip"];	
		$pole_puvodni_data["tunnel_user"]=$data4["tunnel_user"];
		$pole_puvodni_data["tunnel_pass"]=$data4["tunnel_pass"];	
	
    	    }
          
	} // konec else if radku <> 1

        $obj_upd = array( "dns_jmeno" => $dns, "ip" => $ip,
                 "client_ap_ip" => $client_ap_ip, "dov_net" => $dov_net_w,"id_tarifu" => $id_tarifu,
	         "typ" => $typ, "poznamka" => $pozn, "verejna" => $verejna_w,
	         "mac" => $mac, "upravil" => $nick, "sikana_status" => $sikana_status_w,
		 "sikana_cas" => $sikana_cas, "sikana_text" => $sikana_text, "id_nodu" => $selected_nod );
    				
	if( $typ_ip == 4)
	{
    	    $obj_upd["tunnelling_ip"] = $tunnelling_ip; 

    	    $obj_upd["tunnel_user"] = $tunnel_user_w;
    	    $obj_upd["tunnel_pass"] = $tunnel_pass_w;
	}   
	else
	{ 
            $obj_upd["tunnelling_ip"] = "0"; 
	}
    
	$obj_id = array( "id_komplu" => $update_id );
	$res = pg_update($db_ok2, 'objekty', $obj_upd, $obj_id);

    } // konec else jestli je opravneni
    
    if($res){ echo "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3>\n"; }
    else{ 
	    echo "<br><H3><div style=\"color: red; \">".
		"Chyba! Data v databázi nelze změnit. </div></h3>\n".pg_last_error($db_ok2); 
    }
	     
    //ted zvlozime do archivu zmen
    require("objekty-add-inc-archiv.php");				     

    $updated="true";
    
 }
 else
 {
    // rezim pridani
    
    $sql_rows = "";
    $sql_values = "";
    
    $obj_add_i = 1;
    
//    $sql_rows = "dns_jmeno, ip, id_tarifu, dov_net, typ, poznamka, verejna, pridal, id_nodu, ".
//		    "sikana_status, sikana_cas, sikana_text ";

    $obj_add = array( "dns_jmeno" => $dns, "ip" => $ip, "id_tarifu" => $id_tarifu, "dov_net" => $dov_net_w, 
			"typ" => $typ, "poznamka" => $pozn, "verejna" => $verejna_w, "pridal" => $nick, "id_nodu" => $selected_nod,
                	"sikana_status" => $sikana_status_w, "sikana_cas" => $sikana_cas, "sikana_text" => $sikana_text );

    if($typ_ip == 4){
        $obj_add["tunnelling_ip"] = $tunnelling_ip;
        
        $obj_add["tunnel_user"] = $tunnel_user_w;
        $obj_add["tunnel_pass"] = $tunnel_pass_w;
                                        
    }
    	
    if( (strlen($client_ap_ip) > 0) ){
	$obj_add["client_ap_ip"] = $client_ap_ip;
    }
	
    if( (strlen($mac) > 0) ){
	$obj_add["mac"] = $mac;
    }
		
	                                                                     
    foreach ($obj_add as $key => $val) {
	
	if($obj_add_i > 1){
	     $sql_rows .= ", ";
	     $sql_values .= ", ";
	}
	$sql_rows .= $conn_mysql->real_escape_string($key);
	
	$sql_values .= "'".$conn_mysql->real_escape_string($val)."'";
	
	$obj_add_i++;	
    }

    $sql = "INSERT INTO objekty (".$sql_rows.") VALUES (".$sql_values.") ";
    	
    $res = pg_query($sql);
        
    if( !($res === false) ) 
    { 
	echo "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze.</div></H3>\n"; 
    } 
    else
    { 
    	    echo "<H3><div style=\"color: red; padding-top: 20px; padding-left: 5px; \">".
    		    "Chyba! Data do databáze nelze uložit. </div></H3>\n";
    	    
    	    echo "<div style=\"color: red; padding-bottom: 10px; padding-left: 5px; \" >".
    		pg_last_error($db_ok2).
      	        "</div>";
      	    
      	    echo "<div style=\"padding-left: 5px; \">sql: ".$sql."</div>";
    }
	
    // pridame to do archivu zmen
    require("objekty-add-inc-archiv-wifi-add.php");
	
 } // konec else - rezim pridani

}
else {} // konec else ( !(isset(fail) ), muji tu musi bejt, pac jinak nefunguje nadrazeny if-elseif

elseif ( isset($send) ): 
$error = "<h4>Chybí povinné údaje !!! (aktuálně jsou povinné:  dns, ip adresa, přípojný bod, tarif) </H4>"; 
endif; 

if ($update_status==1)
{ echo '<h3 align="center">Úprava objektu</h3>'; } 
else 
{ echo '<h3 align="center">Přidání nového objektu</h3>'; }

// jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
if( (isset($error)) or (!isset($send)) ): 
echo $error; 

echo $info;

// vlozeni vlastniho formu
require("objekty-add-inc.php");


elseif ( ( isset($writed) or isset($updated) ) ): ?> 

<table border="0" width="50%" >
    <tr>
     <td align="right">Zpět na objekty </td>
     <td><form action="objekty.php" method="GET" ><input type="hidden" <?php echo "value=\"".$dns."\""; ?> name="dns_find" >
     <input type="submit" value="ZDE" name="odeslat" > </form></td>
</table>

<br>
Objekt byl přidán/upraven , zadané údaje:<br><br> 
<b>Dns záznam</b>: <?php echo $dns; ?><br> 
<b>IP adresa</b>: <?php echo $ip; ?><br> 
<b>client ap ip </b>: <?php echo $client_ap_ip; ?><br>

<?php echo "<br><b>Typ objektu </b>:";?>
 
<?php if ($typ == 1) { echo "platiči"; } elseif ($typ == 2) { echo "Free"; } elseif ($typ == 3) { echo "AP"; }
    else { echo "chybný výběr"; } ?>
    
    <br> 

<b>Linka</b>: 
<?php 
 //echo "id tarifu: ".$id_tarifu;
 try {
  $vysledek4 = $conn_mysql->query("SELECT jmeno_tarifu, zkratka_tarifu FROM tarify_int WHERE id_tarifu='".intval($id_tarifu)."' ");
  $radku4 = $vysledek4->num_rows;
 } catch (Exception $e) {
    die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
 }
 
 if($radku4==0) echo "Nelze zjistit tarif";
 else 
 {
  while( $zaznam4=$vysledek4->fetch_array() )
  { echo $zaznam4["jmeno_tarifu"]." (".$zaznam4["zkratka_tarifu"].") "; }
 }
 
?> <br>
<b>Povolet NET</b>: <?php if ($dov_net == 2 ) { echo "Ano"; } else { echo "Ne"; } ?><br>
<br>
<b>MAC </b>: <?php echo $mac; ?><br> 
<br>
<b>Poznámka</b>: <?php echo $pozn; ?><br>
<b>Přípojný bod</b>:
<?php
	  try {
      $vysledek3 = $conn_mysql->query("SELECT jmeno,id FROM nod_list WHERE id='".intval($selected_nod)."'");
      $radku3 = $vysledek3->num_rows;
    } catch (Exception $e) {
      die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
    }

    if($radku3==0) echo "Nelze zjistit ";
    else 
    {
         while ($zaznam3=$vysledek3->fetch_array() )
         { echo $zaznam3["jmeno"]." (".$zaznam3["id"].") ".''; }
    }
    
echo "<br><br><b>Šikana: </b>"; 
if( $sikana_status==2) 
{ 
  echo "Ano"; 

  echo "<br><b>Šikana - počet dní: </b>".$sikana_cas;
  echo "<br><b>Šikana - text: </b>".$sikana_text;
} 
elseif($sikana_status==1){ echo "Ne"; }
else { echo "Nelze zjistit"; }

endif; 

?> 

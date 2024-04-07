<?php
    
if (  ( $update_id > 0 ) ) { $update_status=1; }

//nacitani predchozich dat ...
if ( ( $update_status==1 and !( isset($send) ) ) )
{
 //rezim upravy,takze nacitame z databaze ...

 $dotaz_upd = pg_query("SELECT * FROM objekty WHERE id_komplu='".intval($update_id)."' ");
 $radku_upd=pg_num_rows($dotaz_upd);
 
 if ( $radku_upd==0 ) echo "Chyba! Požadovaná data nelze načíst! ";
 else
 {
    
   while (  $data=pg_fetch_array($dotaz_upd) ):
    
    // primy promenny 
    $dns=$data["dns_jmeno"];
    $ip=$data["ip"];	     
    $mac=$data["mac"];
    $selected_nod = $data["id_nodu"];
    $id_tarifu = $data["id_tarifu"];
    
    $typ = $data["typ"];
    $typ_ip = $data["typ_ip"];
    $port_id = $data["port_id"];
    
    $dov_net_l = $data["dov_net"];
    if ( $dov_net_l =="a" ){ $dov_net=2; }else{ $dov_net=1; }
    
    $pozn = $data["poznamka"];
 
    $sikana_text = $data["sikana_text"];

    $sikana_status_l=$data["sikana_status"]; 
    if ( ereg("a",$sikana_status_l) ){ $sikana_status=2; } else { $sikana_status=1; }
    
    $sikana_cas_l=$data["sikana_cas"];  
    if ( strlen($sikana_cas_l) > 0 ){ $sikana_cas=$sikana_cas_l; }  
      
    $verejna_l=$data["verejna"];
      
    if( $verejna_l=="99" ) { $typ_ip="1"; }
    else 
    { 
	$typ_ip="2"; 
	$vip_rozsah="10.1"; 	
    }
	    
    $another_vlan_id = $data["another_vlan_id"];
    
   endwhile;
    
  }  
}
else
{
 // rezim pridani, nacitame z POSTu

 $dns=$_POST["dns"];	
 $ip=$_POST["ip"];
 	
 $typ_ip = $_POST["typ_ip"];
 $selected_nod = $_POST["selected_nod"];
 
 $id_tarifu = $_POST["id_tarifu"];
 
 $mac = $_POST["mac"];
 $typ = $_POST["typ"];
 $dov_net = $_POST["dov_net"];
 
 $pozn = $_POST["pozn"];
 
 $sikana_status = $_POST["sikana_status"];
 $sikana_text = $_POST["sikana_text"];
 $sikana_cas = $_POST["sikana_cas"];
 
 $port_id = $_POST["port_id"];
 $another_vlan_id = $_POST["another_vlan_id"];
 
}

require("include/class.php");

//co mame: v promeny selected_nod mame id nodu kam se to bude pripojovat
// co chcete: ip adresu , idealne ze spravnyho rozsahu :)

objektypridanifiber::generujdata($selected_nod,$id_tarifu); 


//kontrola vlozenych promennych ..
if( (strlen($ip) > 0) ){ objektypridani::checkip($ip); }

if( ( strlen($dns) > 0 ) ){ objektypridani::checkdns($dns); }
if( ( strlen($mac) > 0 ) ){ objektypridani::checkmac($mac); }

if( (strlen($sikana_cas) > 0 ) ){ objektypridani::checkcislo($sikana_cas); }
//if( (strlen($selected_nod) > 0 ) ){ objektypridani::checkcislo($selected_nod); }
    
// jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
if( ( ($dns != "") and ($ip != "") ) and ( $selected_nod > 0 ) and ( ($id_tarifu >= 0) ) and ($mac != "") ):

//kontrola dulplicitnich udaju
if ( ( $update_status!=1 ) )
{ 
 $ip_find=$ip."/32";

 //zjisti jestli neni duplicitni dns, ip
 $MSQ_DNS = pg_exec($db_ok2, "SELECT * FROM objekty WHERE dns_jmeno LIKE '$dns' ");
 $MSQ_IP = pg_exec($db_ok2, "SELECT * FROM objekty WHERE ip <<= '$ip_find' ");
    
 if (pg_num_rows($MSQ_DNS) > 0){ $error .= "<h4>Dns záznam ( ".$dns." ) již existuje!!!</h4>"; $fail = "true"; }
 if (pg_num_rows($MSQ_IP) > 0){ $error .= "<h4>IP adresa ( ".$ip." ) již existuje!!!</h4>"; $fail = "true"; }
}

// check v modu uprava
if ( ( $update_status==1 and (isset($odeslano)) ) )
{
 $ip_find=$ip."/32";
 
 //zjisti jestli neni duplicitni dns, ip
 $MSQ_DNS2 = pg_exec($db_ok2, "SELECT * FROM objekty WHERE ( dns_jmeno LIKE '$dns' AND id_komplu != '$update_id' ) ");
 $MSQ_IP2 = pg_exec($db_ok2, "SELECT * FROM objekty WHERE ( ip <<= '$ip_find' AND id_komplu != '$update_id' ) ");

 if(pg_num_rows($MSQ_DNS2) > 0){ $error .= "<h4>Dns záznam ( ".$dns." ) již existuje!!!</h4>"; $fail = "true"; }
 if(pg_num_rows($MSQ_IP2) > 0){ $error .= "<h4>IP adresa ( ".$ip." ) již existuje!!!</h4>"; $fail = "true"; }
}

// checknem stav vysilace a filtraci
$msq_stav_nodu=mysql_query("SELECT * FROM nod_list WHERE id= '$selected_nod' ");
$msq_stav_nodu_radky=mysql_num_rows($msq_stav_nodu);
 
while ($data=mysql_fetch_array($msq_stav_nodu) )
{ $stav_nodu = $data["stav"]; $router_id = $data["router_id"]; }

if ( $stav_nodu == 2 )
{ $info .= "<div style=\"color: orange; \" ><h4>UPOZORNĚNÍ: Tento přípojný bod je vytížen, mohou se vyskytovat problémy. </h4></div>"; }
elseif ( ( $stav_nodu == 3 ) and ( $update_status == 1 ) )
{ $info .= "<div style=\"color: orange; \"><h4>UPOZORNĚNÍ: Tento přípojný bod je přetížen. </h4></div>"; }
elseif ( $stav_nodu == 3)
{ $fail="true"; $error .= "<div style=\"color: red; \" ><h4>Tento přípojný bod je přetížen, vyberte prosím jiný. </h4></div>";  }

// kontrola jestli se muze povolit inet / jestli jsou pozatavené fakturace
$poz_fakt_clovek=pg_query("SELECT * FROM objekty WHERE id_komplu = '$update_id' ");
$poz_fakt_clovek_radku=pg_num_rows($poz_fakt_clovek);

while($data_poz_f_clovek=pg_fetch_array($poz_fakt_clovek))
{ $id_cloveka=$data_poz_f_clovek["id_cloveka"]; 
    $dov_net_puvodni=$data_poz_f_clovek["dov_net"];
}

if ( ( ($id_cloveka > 1) and ( $update_status==1 ) ) )
{

$pozastavene_fakt=pg_query("SELECT billing_suspend_status FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka)."' ");
$pozastavene_fakt_radku=pg_num_rows($pozastavene_fakt);

 
if( $pozastavene_fakt_radku == 1)
{
 while ( $data_poz_fakt=pg_fetch_array($pozastavene_fakt) )
 { $billing_suspend_status = intval($data_poz_fakt["billing_suspend_status"]); }
}
else
{ echo "Chyba! nelze vybrat vlastníka."; }

if( $billing_suspend_status == 1 ) 
{
 // budeme zli
 // prvne zjisteni predchoziho stavu

 if( ( ($dov_net_puvodni == "n") and ($dov_net == 2 ) ) )
 {
    $fail="true"; 
    $error.="<div class=\"objekty-add-mac\" >Klient má pozastavené fakturace. Před povolením internetu je potřeba změnit u vlastníka fakturační skupinu. </div>"; 
 }
 
}

} // konec if jestli id_cloveka > 1 and update == 1

//checkem jestli se macklo na tlacitko "OK" :)
if( ereg("^OK*",$odeslano) ) { echo ""; }
else { $fail="true"; $error.="<div class=\"objekty-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>"; }

//ukladani udaju ...
if( !( isset($fail) ) ) 
{ 
 // priprava promennych
 
 if( $dov_net == 2 ) { $dov_net_w ="a"; } else { $dov_net_w="n"; }
 if( $sikana_status =="2" ){ $sikana_status_w='a'; } else { $sikana_status_w='n'; }
  
 if ($typ_ip == 1){ 
    $verejna_w="99"; 
    //$vip_snat="0"; 
 }
 else{ 
    $verejna_w="1"; 
    //$vip_snat="0"; 
 }
	       
 if( $another_vlan_id == 0 ){ $another_vlan_id = ""; }
 
 if( $update_status =="1" )
 {
    
    if ( !( check_level($level,29) ) ) 
    {
     echo "<br><div style=\"color: red; font-size: 18px; \" >Objekty nelze upravovat, není dostatečné oprávnění. </div><br>";
     exit;
    }
    
    // rezim upravy
    
    //prvne stavajici data docasne ulozime 
    $pole2 .= "<b>akce: uprava objektu; </b><br>";
    	 
    $vysl4=pg_query("select * from objekty WHERE id_komplu='$update_id' ");

     if( ( pg_num_rows($vysl4) <> 1 ) ){ echo "<p>Chyba! Nelze zjistit puvodni data pro ulozeni do archivu </p>"; }
     else  
     { 
       while ($data4=pg_fetch_array($vysl4) ):
	
	$pole_puvodni_data["id_komplu"]=$data4["id_komplu"];	

	$pole_puvodni_data["dns_jmeno"]=$data4["dns_jmeno"];	
	$pole_puvodni_data["ip"]=$data4["ip"];

	$pole_puvodni_data["id_tarifu"] = $data4["id_tarifu"];
	$pole_puvodni_data["dov_net"] = $data4["dov_net"];
	$pole_puvodni_data["typ"] = $data4["typ"];
	$pole_puvodni_data["poznamka"] = $data4["poznamka"];
	
	$pole_puvodni_data["mac"] = $data4["mac"];
	$pole_puvodni_data["upravil"] = $data4["upravil"];
	$pole_puvodni_data["id_nodu"] = $data4["id_nodu"];
	
	$pole_puvodni_data["sikana_status"] = $data4["sikana_status"];
	$pole_puvodni_data["sikana_text"] = $data4["sikana_text"];
	$pole_puvodni_data["sikana_cas"] = $data4["sikana_cas"];
	
	$pole_puvodni_data["port_id"] = $data4["port_id"];
	$pole_puvodni_data["verejna"] = $data4["verejna"];

	$pole_puvodni_data["another_vlan_id"] = $data4["another_vlan_id"];
	
	if( $data4["verejna"] == 99 )
	{ $pole_puvodni_data["typ_ip"] = "1"; }
	else
	{ $pole_puvodni_data["typ_ip"] = "2"; }
	
       endwhile;   
          
     } // konec else if radku <> 1

     $obj_upd = array( "dns_jmeno" => $dns, "ip" => $ip, "id_tarifu" => $id_tarifu,
		     "dov_net" => $dov_net_w, "typ" => $typ, "poznamka" => $pozn, "mac" => $mac,
		     "upravil" => $nick , "id_nodu" => $selected_nod, "sikana_status" => $sikana_status_w,
		      "sikana_cas" => $sikana_cas, "sikana_text" => $sikana_text, "port_id" => $port_id,
		      "verejna" => $verejna_w, "another_vlan_id" => $another_vlan_id );	
    							
     $obj_id = array( "id_komplu" => $update_id );
     $res = pg_update($db_ok2, 'objekty', $obj_upd, $obj_id);
    
     if($res) { echo "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3>\n"; }
     else{ echo "<br><H3><div style=\"color: red; \">Chyba! Data v databázi nelze změnit. </div></h3>\n".pg_last_error($db_ok2); }
	     
     //ted zvlozime do archivu zmen
    
     //workaround
     $obj_upd["typ_ip"] = $typ_ip;
    
     require("objekty-add-inc-archiv-fiber.php");				     

     $updated="true";
    
    }
    else
    {
    // rezim pridani        
    $obj_add = array( "dns_jmeno" => $dns, "ip" => $ip, "id_tarifu" => $id_tarifu,
		     "dov_net" => $dov_net_w, "typ" => $typ, "poznamka" => $pozn, "mac" => $mac,
		     "pridal" => $nick , "id_nodu" => $selected_nod, "sikana_status" => $sikana_status_w,
		      "sikana_cas" => $sikana_cas, "sikana_text" => $sikana_text, "port_id" => $port_id,
		      "verejna" => $verejna_w, "another_vlan_id" => $another_vlan_id );	
    
    $res = pg_insert($db_ok2, 'objekty', $obj_add);
    
    //zjistit, krz kterého reinharda jde objekt
    $inserted_id = Aglobal::pg_last_inserted_id($db_ok2, "objekty");
                    
    if ($res) { echo "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze.</div></H3>\n"; } 
    else
    { echo "<br><H3><div style=\"color: red; \">Chyba! Data do databáze nelze uložit. </div></H3>\n"; }	
	
    // pridame to do archivu zmen
    $pole="<b> akce: pridani objektu ; </b><br>";
    
    $pole .= "[id_komplu]=> ".intval($inserted_id)." ";
            
    //foreach ($obj_add as $key => $val) { $pole=$pole." [".$key."] => ".$val."\n"; }
  
    foreach ($obj_add as $key => $val) {

            if( (strlen($val) > 0) ){
                //pokud v promenne neco, tak teprve resime vlozeni do Archivu zmen

                //nahrazovani na citelné hodnoty
                if($key == "id_tarifu"){

                    $rs_tarif = mysql_query("SELECT jmeno_tarifu FROM tarify_int WHERE id_tarifu = '".intval($val)."' ");
                    $tarif = mysql_result($rs_tarif,0, 0);
                    $pole .= " <b>tarif</b> => ".$tarif." ,";

                }
                elseif($key == "id_nodu"){
                    $rs_nod = mysql_query("SELECT jmeno FROM nod_list WHERE id = '".intval($val)."' ");
                    $nod = mysql_result($rs_nod, 0, 0);
                    $pole .= " <b>přípojný bod</b> => ".$nod." ,";

                }
		elseif( $key == "typ"){

                    if( $val == 1){ $typ = "poc (platici)"; }
                    elseif($val == 2){ $typ = "poc (free)"; }
                    elseif($val == 3){ $typ = "AP"; }
                    else
                    { $typ = $val; }

                    $pole .= " <b>Typ</b> => ".$typ." ,";

                }
                elseif( $key == "verejna"){

                    if( $val == "99"){ $vip = "Ne"; }
                    elseif($val == "1"){ $vip = "Ano"; }
                    else
                    { $vip = $val; }
                    
                    $pole .= " <b>Veřejná IP</b> => ".$vip." ,";
                }
		else
		{
		    $pole=$pole." <b>[".$key."]</b> => ".$val."\n";
		}
	    
	    }
	    
    }
     
    if( $res == 1){ $vysledek_write="1"; }
    
    $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
		    "('".mysql_real_escape_string($pole)."','".
			mysql_real_escape_string($nick)."','".
			mysql_real_escape_string($vysledek_write)."') ");
     
    $writed = "true"; 
    
    //ted automaticky pridavani restartu
    
    //asi vše :-)
    Aglobal::work_handler("3"); //rh-fiber - iptables
    Aglobal::work_handler("4"); //rh-fiber - radius
    Aglobal::work_handler("5"); //rh-fiber - shaper
    Aglobal::work_handler("6"); //reinhard-fiber - mikrotik.dhcp.leases.erase
    Aglobal::work_handler("7"); //trinity - sw.h3c.vlan.set.pl update
                                     
    Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
                                                 
    // konec else - rezim pridani
    }

}else {} // konec else ( !(isset(fail) ), muji tu musi bejt, pac jinak nefunguje nadrazeny if-elseif

elseif ( isset($send) ): 
$error = "<h4>Chybí povinné údaje !!! (aktuálně jsou povinné:  dns, ip adresa, přípojný bod, tarif) </H4>"; 
endif; 

if ($update_status==1)
{ echo '<h3 align="center">Úprava objektu</h3>'; } 
else 
{ echo '<h3 align="center">Přidání nového objektu</h3>'; }

// jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
if ( (isset($error)) or (!isset($send)) ): 
echo $error; 

echo $info;

// vlozeni vlastniho formu
 include ("objekty-add-inc-form-fiber.php");

elseif ( ( isset($writed) or isset($updated) ) ): ?> 

<table border="0" width="50%" >
    <tr>
     <td align="right">Zpět na objekty </td>
     <td><form action="objekty.php" method="GET" >
     <input type="hidden" <? echo "value=\"".$dns."\""; ?> name="dns_find" >
     
     <input type="hidden" value="2" name="mod_vypisu" >
     
     <input type="submit" value="ZDE" name="odeslat" > </form></td>
<!--
     <td align="right">Restart (optika all) </td>
     <td><form action="work.php" method="POST" ><input type="hidden" name="akce" value="true" >
    	 <input type="hidden" name="optika" value="1" >
        <input type="submit" value="ZDE" name="odeslat" > </form> </td>
    </tr>
-->

</table>

<br>
Objekt byl přidán/upraven , zadané údaje:<br><br> 
<b>Dns záznam</b>: <?echo $dns; ?><br> 
<b>IP adresa</b>: <?echo $ip; ?><br> 

<? echo "<br><b>Typ objektu </b>:";?>
 
<? if ($typ == 1) { echo "platiči"; } elseif ($typ == 2) { echo "Free"; } elseif ($typ == 3) { echo "AP"; }
    else { echo "chybný výběr"; } ?>
    
    <br> 

<b>Linka</b>: 
<? 
 echo "id tarifu: ".$id_tarifu; 
 //if ( $tarif == 2 ) { echo "Metropolitní"; } else { echo "Small city"; } 
?> <br>
<b>Povolet NET</b>: <? if ($dov_net == 2 ) { echo "Ano"; } else { echo "Ne"; } ?><br>
<br>
<b>Poznámka</b>: <? echo $pozn; ?><br>
<b>Přípojný bod</b>:
<?
    $vysledek3=mysql_query("select * from nod_list WHERE id=".$selected_nod );
    $radku3=mysql_num_rows($vysledek3);
    if($radku3==0) echo "Nelze zjistit ";
    else 
    {
         while ($zaznam3=mysql_fetch_array($vysledek3) )
	 { echo $zaznam3["jmeno"]." (".$zaznam3["id"].") ".''; }
    }
	  
// echo "data nejak upravena";

echo "<br><br><b>Šikana: </b>"; 
if( $sikana_status==2) 
{ 
  echo "Ano"; 

  echo "<br><b>Šikana - počet dní: </b>".$sikana_cas;
  echo "<br><b>Šikana - text: </b>".$sikana_text;
} 
elseif($sikana_status==1){ echo "Ne"; }
else { echo "Nelze zjistit"; }

echo "<br><b>Číslo portu (ve switchi)</b>: ".$port_id."<br>";

 echo "<br><b>Typ IP adresy</b>: ";
  if( $typ_ip == "2") echo "Veřejná";
  elseif( $typ_ip == "1") echo "Neveřejná";
  else echo "Nelze zjistit";

 echo "<br><b>Přílušnost MAC k jiné vlaně (ve domov. switchi)</b>: ";
  if( ($another_vlan_id == "NULL") or ($another_vlan_id == "") )
  { echo "Vypnuto"; }
  else
  { echo "vlan id: ".$another_vlan_id; }
  
 echo "<br>";

endif; 

?> 

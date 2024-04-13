<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");
require("include/class.php"); 

if( !( check_level($level,136) ) ) 
{
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

 <title>Adminator2 - Přidání/úprava Set-Top-Boxu</title> 

</head>
<body> 

<?php require ("head.php"); ?>
<?php require ("category.php"); ?>

 <tr>
  <td colspan="2" height="20" bgcolor="silver">
    <?php require("objekty-subcat-inc.php"); ?>
  </td>
 </tr>
 
<tr>
  <td colspan="2">

<?php
 
 //vytvoreni objektu
 $stb = new stb_a2($conn_mysql);

 $update_id = $_POST["update_id"];

 global $odeslano;
 $odeslano = $_POST["odeslano"];

 global $nod_find;
 $nod_find = $_POST["nod_find"];
 
 //hidden prvek, kvuli testovani promenych ..
 $send = $_POST["send"]; 

 global $ip;
 
 if( ( strlen($nod_find) < 1 ) ){ $nod_find="%"; }
 else
 {
   if( !(ereg("^%.*%$",$nod_find)) )
   { $nod_find="%".$nod_find."%"; }
 }
     
 if( ( $update_id > 0 ) ){ $update_status=1; }
 
 if( ( $update_status==1 and !( isset($send) ) ) )
 { 
  //rezim upravy
  try {
    $dotaz_upd = $conn_mysql->query("SELECT * FROM objekty_stb WHERE id_stb = '".intval($update_id)."' ");
    $radku_upd = $dotaz_upd->num_rows;
  } catch (Exception $e) {
    die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
  }
   
  if( $radku_upd == 0 )
  { echo "<div style=\"color: red; \" >Chyba! Požadovaná data nelze načíst! </div>"; }
  else
  {
    while( $data=$dotaz_upd->fetch_array() ):
    
     // primy promenny
     $popis = $data["popis"];	
     $ip=$data["ip_adresa"];
     $id_nodu = $data["id_nodu"];
     $mac = $data["mac_adresa"];
     $popis = $data["popis"];
     $puk = $data["puk"];
     $port_id = $data["sw_port"];  
     $pozn = $data["pozn"];

     $id_tarifu = $data["id_tarifu"];
     
    endwhile;
    
   }
 }
 else
 {
  // rezim pridani, ukladani

  $popis = $_POST["popis"];	
  $ip=$_POST["ip"];
  
  $id_nodu = $_POST["id_nodu"];
  $mac = $_POST["mac"];
  $popis = $_POST["popis"];
  
  $puk = $_POST["puk"];
  $port_id = $_POST["port_id"];
  $pozn = $_POST["pozn"];
  
  $id_tarifu = intval($_POST["id_tarifu"]);
  
 }

 //zde generovani nevyplnenych policek ...
 $stb->generujdata(); 

 //kontrola vlozenych udaju ( kontrolujou se i vygenerovana data ... )
 if( (strlen($ip) > 0) ){ $stb->checkip($ip); }

 if( (strlen($mac) > 0) ){ $stb->checkmac($mac); }

 if( (strlen($puk) > 0) ){ $stb->checkcislo($puk); }

 if( (strlen($id_nodu) > 0) ){ $stb->checkcislo($id_nodu); }

 if( (strlen($port_id) > 0) ){ $stb->checkcislo($port_id); }
													    
 // jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
 if( ( ($ip != "") and ($mac != "") and ( $id_nodu > 0 ) and ($id_tarifu > 0) ) ):

 // check duplicit v modu pridani ...
 if ( ( $update_status!=1 ) )
 {

   //zjisti jestli neni duplicitni dns, ip adresa, mac ...
   $MSQ_POPIS = $conn_mysql->query("SELECT * FROM objekty_stb WHERE popis LIKE '$popis' ");
   $MSQ_IP = $conn_mysql->query("SELECT * FROM objekty_stb WHERE ip_adresa LIKE '$ip' ");
   $MSQ_MAC = $conn_mysql->query("SELECT * FROM objekty_stb WHERE mac_adresa LIKE '$mac' ");
    
   if( $MSQ_POPIS->num_rows > 0 )
   { 
    $error .= "<div style=\"color: #CC0066; \" ><h4>Popis ( ".$popis." ) již existuje!!!</h4></div>"; 
    $fail = "true"; 
   }
   if( $MSQ_IP->mysql_num_rows > 0 )
   { 
    $error .= "<div style=\"color: #CC0066; \" ><h4>IP adresa ( ".$ip." ) již existuje!!!</h4></div>"; 
    $fail = "true"; 
   }
   if( $MSQ_MAC->num_rows > 0 )
   { 
    $error .= "<div style=\"color: #CC0066; \" ><h4>MAC adresa ( ".$mac." ) již existuje!!!</h4></div>"; 
    $fail = "true"; 
   }
 
 }

 // check duplicit v modu uprava
 if( ( $update_status==1 and (isset($odeslano)) ) )
 {

   //zjisti jestli neni duplicitni dns, ip
   $MSQ_POPIS = $conn_mysql->query("SELECT * FROM objekty_stb WHERE ( popis LIKE '$popis' AND id_stb != '$update_id' ) ");
   $MSQ_IP = $conn_mysql->query("SELECT * FROM objekty_stb WHERE ( ip_adresa LIKE '$ip' AND id_stb != '$update_id' )");
   $MSQ_MAC = $conn_mysql->query("SELECT * FROM objekty_stb WHERE ( mac_adresa LIKE '$mac' AND id_stb != '$update_id' ) ");
    
   if ($MSQ_POPIS->num_rows > 0){ $error .= "<h4>Popis ( ".$popis." ) již existuje!!!</h4>"; $fail = "true"; }
   if ($MSQ_IP->num_rows > 0){ $error .= "<h4>IP adresa ( ".$ip." ) již existuje!!!</h4>"; $fail = "true"; }
   if ($MSQ_MAC->num_rows > 0){ $error .= "<h4>MAC adresa ( ".$mac." ) již existuje!!!</h4>"; $fail = "true"; }
 
 }

 // echo "debug: id_fakturacni_skupiny: ".$pozastavene_fakturace_id." id_cloveka: $id_cloveka ,dov_net-puvodni: $dov_net_puvodni , povolen inet: $dov_net";

 //checkem jestli se macklo na tlacitko "OK" :)
 if ( ereg("OK",$odeslano) ) { echo ""; }
 else
 { 
   $fail="true"; 
   $error .= "<div class=\"objekty-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>";
 }

 //ulozeni
 if( !( isset($fail) ) ) 
 { 
  // priprava / konverze promennych pred ulozenim ...
     
  if( $update_status =="1" )
  {
    
    if( !( check_level($level,137) ) ) 
    {
     echo "<br><div style=\"color: red; font-size: 18px; \" >Objekty nelze upravovat, není dostatečné oprávnění. </div><br>";
     exit;
    }
    else
    {
     // rezim upravy
    
     //prvne stavajici data docasne ulozime 
     $pole2 = "<b>akce: uprava stb objektu; </b><br>";
    	 
     $vysl4 = $conn_mysql->query("SELECT * FROM objekty_stb WHERE id_stb = '".intval($update_id)."' ");

     if( ( $vysl4->num_rows <> 1 ) )
     { 
       echo "<div style=\"color: red; padding-top: 5px; padding-bottom: 5px; \" >";
       echo "Chyba! Nelze zjistit puvodni data pro ulozeni do archivu </div>"; 
     }
     else  
     { 
       while ($data4=$vysl4->fetch_array() ):
	
        $pole_puvodni_data["id_stb"]=$data4["id_stb"];		
        
        $pole_puvodni_data["mac_adresa"] = $data4["mac_adresa"];		
        $pole_puvodni_data["ip_adresa"] = $data4["ip_adresa"];
        $pole_puvodni_data["puk"] = $data4["puk"];		
        $pole_puvodni_data["popis"] = $data4["popis"];		
        $pole_puvodni_data["id_nodu"] = $data4["id_nodu"];		
        $pole_puvodni_data["sw_port"] = $data4["sw_port"];
        $pole_puvodni_data["pozn"] = $data4["pozn"];		
        $pole_puvodni_data["upravil_kdo"] = $data4["upravil_kdo"];		
        
        $pole_puvodni_data["id_tarifu"] = $data4["id_tarifu"];		
	
       endwhile;
     } // konec else if radku <> 1
  
      //pridavani do pole pro porovnavani z archivu zmen...
      $obj_upd["mac_adresa"] = $mac;		
      $obj_upd["ip_adresa"] = $ip;
      $obj_upd["puk"] = $puk;		
      $obj_upd["popis"] = $popis;		
      $obj_upd["id_nodu"] = $id_nodu;		
      $obj_upd["sw_port"] = $port_id;
      $obj_upd["pozn"] = $pozn;		
      $obj_upd["upravil_kdo"] = $nick;		

      $obj_upd["id_tarifu"] = $id_tarifu;		

      $res = $conn_mysql->query("UPDATE objekty_stb SET mac_adresa = '$mac', ip_adresa = '$ip',
    			    puk = '$puk', popis = '$popis', id_nodu = '$id_nodu', sw_port = '$port_id',
			    pozn = '$pozn', upravil_kdo = '$nick', id_tarifu = '$id_tarifu' ".
			    " WHERE id_stb = '".intval($update_id)."' Limit 1 ");

     } // konec else jestli je opravneni
    
     if($res){ echo "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3>\n"; }
     else{ echo "<br><H3><div style=\"color: red; \" >Chyba! Data v databázi nelze změnit.</div></h3>\n"; }
	     
     //ted vlozime do archivu zmen
     require("objekty-stb-add-inc-archiv.php");

     $updated="true";
    
    }
    else
    {
     // rezim pridani
     
     $res = $conn_mysql->query("INSERT INTO objekty_stb 
    			    (mac_adresa, ip_adresa, puk, popis, id_nodu, sw_port, pozn, vlozil_kdo, id_tarifu) 
    			 VALUES ('$mac','$ip','$puk','$popis','$id_nodu','$port_id','$pozn','$nick', '$id_tarifu') ");

     $id_stb = $conn_mysql->insert_id;
     
     if( $res )
     { echo "<br><H3><div style=\"color: green;\" >Data úspěšně uloženy do databáze.</div></H3>\n"; } 
     else
     { 
       echo "<br><H3><div style=\"color: red;\" >Chyba! Data do databáze nelze uložit. </div></H3>\n"; 

       $link = $MC;
       echo mysql_errno($link) . ": " . mysql_error($link) . "\n";     
       echo "res: $res \n";
       
       /*
       echo "INSERT INTO objekty_stb
    			     (mac_adresa, ip_adresa, puk, popis, id_nodu, sw_port, pozn)
			 VALUES ('$mac', '$ip', '$puk', '$popis', '$id_nodu', '$port_id', '$pozn')
			";
        */
     }	
	
     // pridame to do archivu zmen
     $pole="<b> akce: pridani stb objektu ; </b><br>";
     
     $pole .= "[id_stb]=> ".$id_stb.", ";
     $pole .= "[mac_adresa]=> ".$mac.", [ip_adresa]=> ".$ip.", [puk]=> ".$puk.", [popis]=> ".$popis;
     $pole .= ", [id_nodu]=> ".$id_nodu.", [sw_port]=> ".$port_id." [pozn]=> ".$pozn.", [id_tarifu]=> ".$id_tarifu;
          
     if( $res == 1 ){ $vysledek_write="1"; }
    
     $add = $conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) ".
    		      "VALUES ('".$conn_mysql->real_escape_string($pole)."',".
    		      "'".$conn_mysql->real_escape_string($nick)."',".
    		      "'".$conn_mysql->real_escape_string($vysledek_write)."')");
     
     $writed = "true"; 

     //automaticke ovezovani

     // Aglobal::work_handler("4"); //rh-fiber - radius
     // Aglobal::work_handler("7"); //trinity - sw.h3c.vlan.set.pl update                                 
     // Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
                
     //pridani do IPTV portálu
     
     //http://app01.cho01.iptv.grapesc.cz:9080/admin/admin/provisioning/stb-edit.html
     
     echo "<div style=\"color: #ff4500; font-weight: bold; font-size: 18px; padding-top: 10px;\" >".
                     "<b>Pozor!</b> Settopbox je třeba také přidat do IPTV portálu. ".
                     " (<a href=\"admin-login-iptv.php\" target=\"_new\" >aktivace funkcí IPTV portálu (přihlašení)</a>)".
          "</div>";
     
     
     
     $p_link = "http://app01.cho01.iptv.grapesc.cz:9080/admin/admin/provisioning/stb-edit.html";
                      
     echo "<div style=\"padding-top: 5px; font-weight: bold; font-size: 18px; \">".
    	    "<a href=\"objekty-stb-add-portal.php?id_stb=".intval($id_stb)."\" target=\"_new\" >".
    	    "Přidání do IPTV portálu zde</a>".
    	  "</div>";
    
     // konec else - rezim pridani
    }

}else {} // konec else ( !(isset(fail) ), muji tu musi bejt, pac jinak nefunguje nadrazeny if-elseif

elseif ( isset($send) ): 
$error = "<h4>Chybí povinné údaje !!! (aktuálně jsou povinné: IP adresa, přípojný bod, MAC adresa, tarif) </H4>"; 
endif; 

if ($update_status==1)
{ echo '<h3 align="center">Úprava STB objektu</h3>'; } 
else 
{ echo '<h3 align="center">Přidání nového STB objektu</h3>'; }

// jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
if ( (isset($error)) or (!isset($send)) ): 
echo $error; 

echo $info;

// vlozeni vlastniho formu
require("objekty-stb-add-inc-form.php");

elseif ( ( isset($writed) or isset($updated) ) ): ?> 

<table border="0" width="50%" >
    <tr>
     <td align="right">Zpět na objekty </td>
     <td>
      <form action="objekty-stb.php" method="GET" >
       <!--
       <input type="hidden" <?php echo "value=\"".$dns."\""; ?> name="dns_find" >
       -->
      <input type="submit" value="ZDE" name="odeslat" >
      </form>
     </td>
          
     <td align="right">Restart (iptables) </td>
     <td>
     
      <form action="work.php" method="POST" >
        <!--
        <input type="hidden" name="akce" value="true" >
        <input type="hidden" name="iptables" value="1" >
        -->
       <input type="submit" value="ZDE" name="odeslat" >
      </form> 
     </td>
    </tr>
</table>

<br>
STB Objekt byl přidán/upraven, zadané údaje:<br><br>
<b>Popis objektu</b>: <?php echo $popis; ?><br>
<b>IP adresa</b>: <?php echo $ip; ?><br>
<b>MAC adresa</b>: <?php echo $mac; ?><br><br>

<b>Puk</b>: <?php echo $puk; ?><br>
<b>Číslo portu switche</b>: <?php echo $port_id; ?><br>

<b>Přípojný bod</b>:
<?php
    $vysledek3=$conn_mysql->query("select jmeno, id from nod_list WHERE id='".intval($id_nodu)."' ");
    $radku3=$vysledek3->num_rows;
    
    if($radku3==0) echo " Nelze zjistit ";
    else 
    {
        while( $zaznam3=$vysledek3->fetch_array() )
	      { echo $zaznam3["jmeno"]." (id: ".$zaznam3["id"].") ".''; }
    }

echo "<br><br>";

echo "<b>Poznámka</b>:".htmlspecialchars($pozn)."<br>";

$ms_tarif = $conn_mysql->query("SELECT jmeno_tarifu FROM tarify_iptv WHERE id_tarifu = '".intval($id_tarifu)."'");

$ms_tarif->data_seek(0);
$ms_tarif_r = $ms_tarif->fetch_row();

echo "<b>Tarif</b>: ".$ms_tarif_r[0]."<br><br>";

endif; 

echo "<br><br>";

?> 

   </td>
  </tr>
  
 </table>

</body> 
</html>

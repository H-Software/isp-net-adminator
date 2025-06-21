<?php

//skript co se pousti kazdou hodinu a 
// v urcitou dobu podle db lidem co maj pocet dni sikany nula zakaze net

 require("/var/www/html/htdocs.ssl/adminator2/include/config.php");

//  require_once("/var/www/html/htdocs.ssl/adminator2/include/class.php");

//prvne detekce casu
$datum = strftime("%H ", time());
// echo " \n ".$datum." \n ";
$time = strftime("%d/%m/%Y %H:%M:%S", time());

// prvne zjistime jestli je funkce zapnuta a pak kdy se ma vykonavat
    $vysl_cas=$conn_mysql->query("select * from automatika WHERE vec LIKE 'sikana_net_n' ");
    $radku_cas = $vysl_cas->num_rows();
     
    if ($radku_cas==0) { echo "chyba - nelze zjistit stav"; }
    else
    {
      while ($data_cas=mysql_fetch_array($vysl_cas) ):
		      
      $cas=$data_cas["cas_hodina"];
      $zapnuto=$data_cas["zapnuto"];
    
     if ( $zapnuto == 2 ) { echo "\n funkce zapnuta \n"; } 
     elseif ( $zapnuto==1 ){ echo "\n funkce vypnuta \n"; exit; }
     else { echo "\n stav funkce nelze zjistit \n"; exit; }
    
    echo "aktualni cas: ".$time." \n cas z db: ".$cas." , aktualni cas: ".$datum." \n";
    
    $cas=$cas+0;
    $datum=$datum+0;
    
     if ( !($cas==$datum) ) 
     { echo "nic nedelam - este neni cas odpoctu \n\n";  exit; }

     endwhile;
	
    }

    // ted vyberem lidi co maj sikanu a nula dni 
        	    
    $dotaz=pg_query("SELECT * FROM objekty WHERE sikana_status='a' AND sikana_cas='0' ");
    $radku=pg_num_rows($dotaz);
       
    if ($radku==0) echo "Žádné objekty nemaji sikanu a 0 dni. \n\n";
    else
     {
     while (  $data=pg_fetch_array($dotaz) ):
     
    $id=$data["id_komplu"];
    $sikana_cas=$data["sikana_cas"]; 
    $sikana_cas=$sikana_cas+0;
    
    $dov_net=$data["dov_net"];
    
    echo "ip: ".$data["ip"]." ,dns: ".$data["dns_jmeno"]." ,puv. pocet dni: ".$sikana_cas." dov_net: ".$dov_net." \n";
    
    if($dov_net == "n" ){ echo " \n nelze zakazat, net uz je zakazan \n"; }
    else
    {
    
     $obj_upd = array( "dov_net" => "n" , "sikana_status" => "n" );
     $obj_id = array( "id_komplu" => $id );
     
     
     $res = pg_update($db_ok2, 'objekty', $obj_upd, $obj_id);
										 
     if($res){ echo " stav: OK \n"; } else{ echo " stav: error \n"; }

     // ted to ulozime do archivu zmen 
	$pole2 .= "<b>akce: zakazani netu z duvodu sikany;</b><br>";
	
	$pole2 .= "[id_komplu]=> ".$id." ";
	     
	foreach ($obj_upd as $key => $val) { 
	    $pole2 .= " <b>[".$key."]</b> => ".$val;
	}
		 
	$pole2 .= ", rs: ".$res;
	      
	 $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce, provedeno_kym, vysledek) 
			    VALUES ('".$conn_mysql->real_escape_string($pole2)."', 'system', '".intval($res)."')");
	
	$pole2 = "";
				 
    } // konec else sikana_cas <= 0
    
    endwhile; // while pg_fetch_array

    echo "\n ";
    
    /*
    //ted je treba ovezit
    system("sudo /root/bin/erik.remote.exec.sh \"/etc/init.d/iptables restart\" ",$err2);
    
     if ( !( $err2) ){ echo "<span class=\"work-ok\">erik-iptables-restart ok </span>\n"; } 
     else { echo "<span class=\"work-error\">erik-iptables-restart error </span>\n"; }
     
     $pole3 .= "akce: restart po skriptu sikana-net-n, vysledek restartu: ".$err2;
    // zaverecne ulozeni do archivu
    
    $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce) VALUES ('$pole3')");
    */
    
    //novej zpusob osvezovani, pridat do fronty
    // Aglobal::work_handler("1"); //reinhard-3 (ros) - restrictions (net-n/sikana)
    // Aglobal::work_handler("2"); //reinhard-wifi (ros) - restrictions (net-n/sikana)
    // Aglobal::work_handler("3"); //reinhard-fiber (linux) - iptables (net-n/sikana atd)
    
  } //konec else od pg
  
?>
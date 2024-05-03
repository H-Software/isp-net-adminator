<?php

//skript co se pousti kazdou hodinu a 
// v urcitou dobu podle db dekrementuje pocet dni sikany

 require("/var/www/html/htdocs.ssl/adminator2/include/config.php");
  
//prvne detekce casu
 $datum = strftime("%H ", time());
// echo " \n ".$datum." \n ";
 $time = strftime("%d/%m/%Y %H:%M:%S", time());

// prvne zjistime jestli je funkce zapnuta a pak kdy se ma vykonavat
 $vysl_cas=$conn_mysql->query("select * from automatika WHERE vec LIKE 'sikana_odpocet' " );
 $radku_cas = $vysl_cas->num_rows();
     
 if($radku_cas==0) { 
    echo "chyba - nelze zjistit stav"; 
 }
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

 // ted vyberem lidi co maj sikanu a postupne budem cas dekrementovat
    	    
 $dotaz=pg_query("SELECT * FROM objekty WHERE sikana_status='a' ");
 $radku=pg_num_rows($dotaz);
       
 if ($radku==0) echo "Žádné objekty nemaji sikanu. ";
 else
 {
    while (  $data=pg_fetch_array($dotaz) ):
     
      $id=$data["id_komplu"];
      $sikana_cas=$data["sikana_cas"]; 
      $sikana_cas=$sikana_cas+0;
    
      echo "ip: ".$data["ip"]." ,dns: ".$data["dns_jmeno"]." ,puv. pocet dni: ".$sikana_cas."  \n";
    
      if( $sikana_cas <= 0 ){ echo " \n nelze snizit, counter je na nule \n"; }
      else
      {
    
        $sikana_cas--;
    
        $obj_upd = array( "sikana_cas" => $sikana_cas );
        $obj_id = array( "id_komplu" => $id );
     
	$res = pg_update($db_ok2, 'objekty', $obj_upd, $obj_id);
										 
	if($res){ 
	    echo " stav: OK \n"; 
	} 
	else { 
	    echo " stav: error \n"; 
	}

    
      } // konec else sikana_cas <= 0
    
    endwhile; // while pg_fetch_array

    echo "\n ";
 
 } //konec else od pg
     
?>

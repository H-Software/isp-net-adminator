<?php

//skript co se pousti kazdou hodinu / resp. kazdej den a 
// v urcitou dobu podle db pusti skript co skontroluje neuhr. faktury vuci splatnosti vlastnika

 require("/var/www/html/htdocs.ssl/adminator2/include/config.php");
 
//prvne detekce casu
$datum = strftime("%H ", time());
// echo " \n ".$datum." \n ";
$time = strftime("%d/%m/%Y %H:%M:%S", time());

// prvne zjistime jestli je funkce zapnuta a pak kdy se ma vykonavat
    $vysl_cas=$conn_mysql->query("select * from automatika WHERE vec LIKE 'kontrola_fn_vlastnik' " );
    $radku_cas = $vysl_cas->num_rows();
     
    if ($radku_cas==0) { echo "chyba - nelze zjistit stav"; }
    else
    {
      while ($data_cas=mysql_fetch_array($vysl_cas) ):
		      
      $cas=$data_cas["cas_hodina"];
      $zapnuto=$data_cas["zapnuto"];
    
     if( $zapnuto == 2 ) { echo "\n funkce zapnuta \n"; } 
     elseif( $zapnuto==1 ){ echo "\n funkce vypnuta \n"; exit; }
     else{ echo "\n stav funkce nelze zjistit \n"; exit; }
    
     echo "aktualni cas: ".$time." \n cas z db: ".$cas." , aktualni cas: ".$datum." \n";
    
     $cas=$cas+0;
     $datum=$datum+0;
    
     if( !($cas==$datum) ) 
     { echo "nic nedelam - este neni cas odpoctu \n\n";  exit; }

     endwhile;
	
   } // konec else if radku_cas == 0

    // ted spustime skript co chceme
    
    $typ=$_GET["typ"];
    $odeslano=$_GET["odeslano"];
    
    //$no_login=$_GET["no_login"];
    
    $_GET["no_login"] = "yes";
    
    require("/var/www/html/htdocs.ssl/adminator2/faktury/fn-aut-check-splatnost.php");    
     
    //$vysl = system("wget https://trinity.simelon.net/adminator2/faktury/fn-aut-check-splatnost.php?typ=1&odeslano=OK&no_login=yes --no-check-certificate");
    
    // echo $vysl;
    
?>

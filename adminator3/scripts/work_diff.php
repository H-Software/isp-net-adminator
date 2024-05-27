<?php

//skript co se pousti v urcitych intervalech a provadi pozadavky na restart z adminatoru

error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);

require_once __DIR__ . "/../include/main.function.shared.php";

require_once __DIR__ . "/../include/config.php";

$ag = new Aglobal();      
$ag->conn_mysql = $conn_mysql;
$ag->conn_pgsql = $db_ok2;

$html_tags = 1;

echo "work-diff.php start [".strftime("%d/%m/%Y %H:%M:%S", time())."] \n";
$output_main .= "work-diff.php start [".strftime("%d/%m/%Y %H:%M:%S", time())."] \n";
       
    $sql = "SELECT id, number_request FROM workitems ORDER BY id";
    
    $sql = "SELECT workitems.id, workitems_names.name, workitems.number_request, workitems_names.priority ".
	   " FROM workitems, workitems_names ".
	   " WHERE workitems.number_request = workitems_names.id ".
	   " ORDER BY priority ";
    
    $rs = $conn_mysql->query($sql);
    $num_rows = $rs->num_rows;
    
    if( $num_rows ==0 ) 
    { 
		echo " INFO: no requests on the system \n"; 
		$output_main .= " INFO: no requests on the system \n";
    }
    else
    {
      while($data = $rs->fetch_array() )
      {
        $id = $data["id"];
        $number_request = $data["number_request"];

        execute_action($number_request, $id);
	
      } //end of while
	
   } // end of else if num_rows == 0


echo "work-diff.php stop [".strftime("%d/%m/%Y %H:%M:%S", time())."] \n";
$output_main .= "work-diff.php stop [".strftime("%d/%m/%Y %H:%M:%S", time())."] \n";

if( preg_match("/.*<span.*>.*/", $output_main) )
{ $soubor = fopen("/var/www/html/htdocs.ssl/reinhard.remote.log", "w"); }
else
{ 
    $output_main = "- - - - - - - - - - - - - -\n".$output_main;
    $soubor = fopen("/var/www/html/htdocs.ssl/reinhard.remote.log", "a");
}
 
fwrite($soubor, $output_main); 
fclose($soubor);

//vlozit vysledek do DB

if( (strlen($output_main) > 150) )
{
  $set = array();
  $set["akce"] = "'" . $conn_mysql->real_escape_string($output_main) . "'";
  //$set["provedeno_kym"] = "'" . $conn_mysql->real_escape_string(\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email) . "'";

  // a další spolu s případným ošetřením hodnot
  $rs_archiv = $conn_mysql->query("INSERT INTO archiv_zmen_work (" . implode(", ", array_keys($set)) . ") VALUES (" . implode(", ", $set) . ")");
}

//
// zde uz jen funkce
//

function execute_request($cmd, $mess_ok, $mess_er) 
{
    global $html_tags, $output_main;
    
    exec($cmd, $output, $rs);
    //system($cmd, $rs);

    //print_r($output);
    $output_main .= "\n".implode("\n ", $output)."\n";
    	    
    if($rs == "0")
    { 		
		if($html_tags == 1)
		{ $hlaska = "  <span class=\"work-ok\">".$mess_ok." (message: ".$rs.")</span>\n"; }
		else
		{ $hlaska = "  ".$mess_ok." (message: ".$rs.")\n"; }

		echo $hlaska;
		$output_main .= $hlaska;
	
    }
    else
    { 
		if($html_tags == 1)
		{ $hlaska = "  <span class=\"work-error\">".$mess_er." (message: ".$rs.")</span>\n"; }
		else
		{ $hlaska = "  ".$mess_er." (message: ".$rs.")\n"; }

		echo $hlaska; 
		$output_main .= $hlaska;
    }
} //end of function execute_request

function execute_action($number_request, $id)
{
	global $output_main, $ag, $conn_mysql;
    
	if($number_request == 1)
	{ //reinhard-3 - restriction (net-n/sikana)

	    $cmd = "sudo /root/bin/trinity.local.exec2.sh \"php /var/www/html/htdocs.ssl/adminator2/mk_control/mk_rh_restriction.php 10.128.0.3\" ";

		$mess_ok = "reinhard-3-restriction ok ";
		$mess_er = "reinhard-3-restriction error ";
	    		
	    execute_request($cmd, $mess_ok, $mess_er);
	
	    $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");
	}
	elseif( $number_request == 2)
	{ //reinhard-wifi (1) - restrictions (net-n/sikana)
	
	    //$cmd = "sudo /root/bin/reinhard-wifi.remote.exec2.sh \"/etc/init.d/iptables restart\" ";
	    $cmd = "sudo /root/bin/trinity.local.exec2.sh \"php /var/www/html/htdocs.ssl/adminator2/mk_control/mk_rh_restriction.php 10.128.0.2\" ";
	    
	    $mess_ok = "reinhard-wifi-iptables-restart ok ";
	    $mess_er = "reinhard-wifi-iptables-restart error ";
				   
	    execute_request($cmd, $mess_ok, $mess_er);
	
	    $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");
	}
	elseif($number_request == 3)
	{ //reinhard-fiber (2) - iptables (net-n/sikana)
	
	   $cmd = "sudo /root/bin/reinhard-fiber.remote.exec2.sh \"/etc/init.d/iptables-simelon restart\" ";
	       
	   $mess_ok = "reinhard-fiber.iptables ok ";
	   $mess_er = "reinhard-fiber.iptables error "; 

	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");
	}
	elseif($number_request == 4)
	{ //reinhard-fiber - radius
	    $cmd = "sudo /root/bin/reinhard-fiber.remote.exec2.sh \"/root/bin/radius.restart.sh\"";
	
	    $mess_ok = "reinhard-fiber.radius ok ";
	    $mess_er = "reinhard-fiber.radius error ";    
	
	    execute_request($cmd, $mess_ok, $mess_er);
	
	    $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");
	}
	elseif($number_request == 5)
	{
	   $cmd = "sudo /root/bin/reinhard-fiber.remote.exec2.sh \"/etc/init.d/shaper restart\" ";
	   
	   $mess_ok = "reinhard-fiber.shaper ok ";
	   $mess_er = "reinhard-fiber.shaper error ";

	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");
	}
	elseif($number_request == 6)
	{
	   $cmd = "sudo /root/bin/trinity.local.exec2.sh \"/root/bin/mikrotik.dhcp.leases.erase.sh\" ";
	   
	   $mess_ok = "(trinity) mikrotik.dhcp.leases.erase ok ";
	   $mess_er = "(trinity) mikrotik.dhcp.leases.erase error ";
	   
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");
	}
	elseif($number_request == 7)
	{
	   $cmd = "sudo /root/bin/trinity.local.exec2.sh \"/root/bin/scripts_fiber/sw.h3c.vlan.set.pl update\" ";
	   
	   $mess_ok = "trinity.sw.h3c.vlan.set ok ";
	   $mess_er = "trinity.sw.h3c.vlan.set error ";

	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 8)
	{ //nic
	
	}
	elseif($number_request == 9)
	{
	   $cmd = "sudo /root/bin/erik.remote.exec.sh \"/root/bin/dns.restart.sh\" ";
	   
	   $mess_ok = "erik-dns.restart ok ";
	   $mess_er = "erik-dns.restart-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 10)
	{
	   $cmd = "sudo /root/bin/trinity.local.exec2.sh \"/root/bin/dns.restart.sh\" ";
	   
	   $mess_ok = "trinity-dns-restart ok ";
	   $mess_er = "trinity-dns-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 11)
	{
	   $cmd = "sudo /root/bin/artemis.remote.exec2.sh \"/root/bin/dns.restart.sh\" ";
	   
	   $mess_ok = "artemis-dns-server-restart ok ";
	   $mess_er = "artemis-dns-server-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 12)
	{
	   $cmd = "sudo /root/bin/c.ns.remote.exec2.sh \"/root/bin/dns.restart.sh\" ";
	   
	   $mess_ok = "c.ns.simelon.net-dns-server-restart ok ";
	   $mess_er = "c.ns.simelon.net-dns-server-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 13)
	{ // reinhard-wifi (ros) - shaper (client's tariffs)
	
	   $cmd = "sudo /root/bin/trinity.local.exec2.sh \"php /var/www/html/htdocs.ssl/adminator2/mk_control/mk_qos_handler.php 10.128.0.2\" ";
	   
	   $mess_ok = "reinhard-wifi-shaper-restart ok ";
	   $mess_er = "reinhard-wifi-shaper-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 14)
	{
	   $cmd = "sudo /root/bin/trinity.local.exec2.sh \"/root/bin/scripts_wifi_network/rb.filter_v2.pl\" ";
	   
	   //obsolete
	   //$cmd = "sudo /root/bin/trinity.local.exec2.sh \"/root/bin/scripts_wifi_network/obsolete/rb.filter_no_fork.pl\" ";
	   
	   $mess_ok = "trinity-filtrace-IP-on-Mtik's-restart ok ";
	   $mess_er = "trinity-filtrace-IP-on-Mtik's-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 15)
	{ //trinity - Monitoring I - Footer-restart (alarms)
	   
	   $cmd = "sudo /root/bin/monitoring.remote.exec2.sh \"/var/www/cgi-bin/mon1-footer.pl\" ";
 
	   $mess_ok = "monitoring-I-Footer-restart ok ";
	   $mess_er = "monitoring-I-Footer-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 16)
	{
	   $cmd = "sudo /root/bin/trinity.local.exec2.sh \"/var/www/cgi-bin/cgi-mon/footer_php.pl\" ";
	   
	   $mess_ok = "trinity-monitoring-I-Footer-PHP-restart ok ";
	   $mess_er = "trinity-monitoring-I-Footer-PHP-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 17)
	{
	   $cmd = "sudo /root/bin/trinity.local.exec2.sh \"/var/www/cgi-bin/cgi-mon/footer_cat.pl\" ";
	   
	   $mess_ok = "trinity-monitoring-I-Footer-cat-restart ok ";
	   $mess_er = "trinity-monitoring-I-Footer-cat-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 18)
	{
	   $cmd = "sudo /root/bin/monitoring.remote.exec2.sh \"/var/www/cgi-bin/mon2-feeder.pl\" ";
	   
	   $mess_ok = "monitoring - Monitoring II - Feeder-restart ok ";
	   $mess_er = "monitoring - Monitoring II - Feeder-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 19)
	{
	   $output_main .= $ag->synchro_router_list();
	     
	   $mess_ok = "trinity - adminator - synchro_router_list - restart ";

	   $hlaska = "  <span class=\"work-ok\">".$mess_ok." (message: ".$rs.")</span>\n";
	   
	   echo $hlaska;
	   $output_main .= $hlaska;
	   
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");
	}
	elseif( $number_request == 20 )
	{
	   $cmd = "sudo /root/bin/trinity.local.exec2.sh \"php /var/www/html/htdocs.ssl/adminator2/mk_control/mk_qos_handler.php 10.128.0.3\" ";
	   
	   $mess_ok = "reinhard-3 (ros) - shaper (client's tariffs) - restart ok ";
	   $mess_er = "reinhard-3 (ros) - shaper (client's tariffs) - restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 21)
	{
	   $cmd = "sudo /root/bin/artemis.remote.exec2.sh \"/root/bin/radius.restart.sh\" ";
	   
	   $mess_ok = "artemis-radius-restart ok ";
	   $mess_er = "artemis-radius-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 22)
	{
	   $cmd = "sudo /root/bin/monitoring.remote.exec2.sh \"/var/www/cgi-bin/mon2-checker.pl\" ";
	   
	   $mess_ok = "monitoring - Monitoring II - Feeder-restart ok ";
	   $mess_er = "monitoring - Monitoring II - Feeder-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 23)
	{
	   //$cmd = "";
	   $cmd = "sudo /root/bin/trinity.local.exec2.sh \"php /var/www/html/htdocs.ssl/adminator2/mk_control/mk_qos_handler.php 10.128.0.15\" ";
	   
	   $mess_ok = "reinhard-5-shaper-restart ok ";
	   $mess_er = "reinhard-5-shaper-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == 24)
	{
	    $cmd = "sudo /root/bin/trinity.local.exec2.sh \"php /var/www/html/htdocs.ssl/adminator2/mk_control/mk_rh_restriction.php 10.128.0.15\" ";
	    
	    $mess_ok = "reinhard-5-iptables-restart ok ";
	    $mess_er = "reinhard-5-iptables-restart error ";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	elseif($number_request == "templ")
	{
	   $cmd = "";
	   
	   $mess_ok = "";
	   $mess_er = "";
	
	   execute_request($cmd, $mess_ok, $mess_er);
	
	   $rs_delete = $conn_mysql->query("DELETE FROM workitems WHERE id = '$id' LIMIT 1");	   
	}
	else
	{ 
	    echo " ERROR: Not found action to request No.".$number_request."\n"; 
	    $output_main .= " ERROR: Not found action to request No.".$number_request."\n";
	}	


} //end of function execute_action

<?php

require("include/main.function.shared.php");
require_once("include/config.php");
require_once("include/check_login.php");
require_once("include/check_level.php");


$ag = new Aglobal();
$ag->conn_mysql = $conn_mysql;
$ag->conn_pgsql = $db_ok2;

echo "<div style=\"padding-bottom: 10px; padding-top: 10px; font-size: 18px; \">Přidání/úprava routeru </div>";

if(($odeslat == "OK") and ($error != "1")) {
    //proces ukladani ..

    //vypsat co se vlozilo
    // removed
    
    if($update_id > 0) {

        $pole = "<b>akce: uprava routeru;</b><br>";

        // prvne zjistime puvodni hodnoty
        $dotaz_top = $conn_mysql->query("SELECT nazev, ip_adresa, parent_router, mac, monitoring, 
					monitoring_cat, alarm, filtrace, id_nodu, poznamka 
				FROM router_list WHERE id = '".intval($update_id)."' ");

        $dotaz_top_radku = $dotaz_top->num_rows();

        if($dotaz_top_radku < 1) {
            echo "<span style=\"color: red; font-size: 16px; font-weight: bold;\">
          <p> Chyba! Nelze načíst zdrojové hodnoty pro úpravu. </p></span>";
        } else {
            while($data_top = mysql_fetch_array($dotaz_top)):

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

        if(strlen($mac) <= 0) {
            $mac = "00:00:00:00:00:00";
        }

        $uprava = $conn_mysql->query("UPDATE router_list SET nazev='$nazev', ip_adresa='$ip_adresa', parent_router='$parent_router',
	            		mac='$mac', monitoring='$monitoring', monitoring_cat='$monitoring_cat', alarm='$alarm',
				filtrace='$filtrace', id_nodu='$selected_nod', poznamka = '$poznamka' WHERE id=".intval($update_id)." Limit 1 ");

        if($uprava) {
            echo "<br><span style=\"color: green; font-size: 18px; \">Záznam úspěšně upraven.</span><br><br>";
        } else {
            echo "<div style=\"color: red; font-weight: bold; font-size: 16px; \">Záznam nelze upravit v databázi. </div>";
        }

        //ulozeni do archivu zmen
        require("topology-router-add-inc-archiv-zmen.php");

        //automatické restarty
        // if( ereg(".*změna.*Alarmu.*z.*", $pole3) )
        // {
        //   //kvuli alarmu
        //   Aglobal::work_handler("15"); 		//trinity - Monitoring I - Footer-restart
        //       }

        // if( ereg(".*změna.*Monitorování.*", $pole3) or ereg(".*změna.*Monitoring kategorie.*", $pole3) )
        // {
        //   //kvuli monitoringu - feeder asi nepovinnej
        //   Aglobal::work_handler("18"); 		//monitoring - Monitoring II - Feeder-restart
        //   Aglobal::work_handler("22"); //monitoring - Monitoring II - checker-restart
        //       }

        // if( ereg(".*změna.*Nadřazený router.*", $pole3) )
        // {
        //      Aglobal::work_handler("1");        //reinhard-3 (ros) - restrictions (net-n/sikana)
        //      Aglobal::work_handler("20");       //reinhard-3 (ros) - shaper (client's tariffs)

        //      Aglobal::work_handler("24");       //reinhard-5 (ros) - restrictions (net-n/sikana)
        //      Aglobal::work_handler("23");       //reinhard-5 (ros) - shaper (client's tariffs)

        //      Aglobal::work_handler("13");       //reinhard-wifi (ros) - shaper (client's tariffs)
        //      Aglobal::work_handler("2");        //reinhard-wifi (ros) - restrictions (net-n/sikana)

        //      Aglobal::work_handler("14");       //(trinity) filtrace-IP-on-Mtik's-restart

        // }

        // if( ereg(".*změna.*Připojného bodu.*", $pole3) )
        // {
        //      Aglobal::work_handler("14");	//(trinity) filtrace-IP-on-Mtik's-restart
        // }

        // if( ereg(".*změna.*Filtrace.*", $pole3) )
        // {
        //      Aglobal::work_handler("14");	//(trinity) filtrace-IP-on-Mtik's-restart
        // }

        // if( ereg(".*změna.*", $pole3) )
        // {
        //   //radsi vzdy (resp. zatim)
        //   Aglobal::work_handler("19"); 		//trinity - adminator - synchro_router_list
        //       }

    } else {
        // rezim pridani
        $poznamka = addslashes($poznamka);

        if(strlen($mac) <= 0) {
            $mac = "00:00:00:00:00:00";
        }

        $add = $conn_mysql->query("INSERT INTO router_list (nazev,ip_adresa, parent_router,mac, monitoring, alarm, monitoring_cat, filtrace, id_nodu, poznamka) 
					    VALUES ('$nazev','$ip_adresa','$parent_router','$mac','$monitoring','$alarm','$monitoring_cat', '$filtrace', '$selected_nod', '$poznamka' ) ");

        if($add) {
            echo "<br><div style=\"color: green; font-size: 18px; \">Záznam úspěšně vložen.</div><br>";
        } else {
            echo "<div style=\"color: red; font-weight: bold; font-size: 16px; \">Záznam nelze vložit do databáze. </div>";
            echo "<div style=\"\">".$conn_mysql->error()."</div>";
        }

        // pridame to do archivu zmen
        $pole = "<b>akce: pridani routeru;</b><br>";
        $pole .= " nazev: ".$nazev.", ip adresa: ".$ip_adresa.", monitoring: ".$monitoring.", monitoring_cat: ".$monitoring_cat;
        $pole .= " alarm: ".$alarm.", parent_router: ".$parent_router.", mac: ".$mac.", filtrace: ".$filtrace.", id_nodu: ".$selected_nod;

        if($add == 1) {
            $vysledek_write = 1;
        }
        $add = $conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write') ");

        // Aglobal::work_handler("13"); //reinhard-wifi (ros) - shaper (client's tariffs)
        // Aglobal::work_handler("20"); //reinhard-3 (ros) - shaper (client's tariffs)
        // Aglobal::work_handler("23"); //reinhard-5 (ros) - shaper (client's tariffs)

        // Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart

        // //automatické restarty
        // if($alarm == 1) {
        //     //kvuli alarmu
        //     Aglobal::work_handler("15"); //trinity - Monitoring I - Footer-restart
        // }

        // if($monitoring == 1) {
        //     //kvuli monitoringu
        //     Aglobal::work_handler("18"); //monitoring - Monitoring II - Feeder-restart
        //     Aglobal::work_handler("22"); //monitoring - Monitoring II - checker-restart
        // }

        // //radsi vzdy (resp. zatim)
        // Aglobal::work_handler("19"); //trinity - adminator - synchro_router_list

    } //konec if/else update_id > 0

} // konec odeslat == OK
else {
    //nechceme ukladat, tj. zobrazit form

    //pokud update, tak zjistit predchozi hodnoty
    if($update_id > 0 and ($odeslat != "OK")) {
        // nacteni promennych, pokud se nedna o upravu a neodeslal sem form

        // removed
    }

    //zobrazime formular
    // removed
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
    if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $ip_addr)) {
        //now all the intger values are separated
        $parts = explode(".", $ip_addr);
        //now we need to check each part can range from 0-255
        foreach($parts as $ip_parts) {
            if(intval($ip_parts) > 255 || intval($ip_parts) < 0) {

                return false;
            } //if number is not within range of 0-255
        }

        return true;
    } else {
        return false;
    } //if format of ip address doesn't matches
}

function checkcislo($cislo)
{
    $rra_check = ereg('^([[:digit:]]+)$', $cislo);

    if (!($rra_check)) {
        global $fail;
        $fail = "true";
        global $error;
        $error .= "<H4>Zadaný číselný údaje ( ".$cislo." ) není ve  správném formátu !!! </H4>";
    }

} //konec funkce check cislo

function checkdns($dns)
{
    $dns_check = ereg('^([[:alnum:]]|\.|-)+$', $dns);
    if (!($dns_check)) {
        global $fail;
        $fail = "true";
        global $error;
        $error .= "<div class=\"objekty-add-fail-dns\"><H4>DNS záznam ( ".$dns." ) není ve správnem formátu !!! </H4></div>";
    }

} // konec funkce check rra


?>

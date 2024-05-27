<?php

use RouterOS\Config;
use RouterOS\Client;
use RouterOS\Query;

class Aglobal
{
    public \mysqli|\PDO $conn_mysql;

    public \PgSql\Connection|\PDO|null $conn_pgsql;

    public function test_snmp_function()
    {

        $ret_array = array();

        $ret_array[0] = true;

        if(!(function_exists('snmpget'))) {

            $ret_array[0] = false;
            $ret_array[1] = "Chyba! Neexistuje funkce \"snmpget\"!";

        }

        if(!(function_exists('snmpwalk'))) {

            $ret_array[0] = false;
            $ret_array[1] = "Chyba! Neexistuje funkce \"snmpwalk\"!";

        }

        return $ret_array;

    } //konec funkce test_snmp_function


    public function test_router_for_monitoring($router_id)
    {

        $ret_array = array();

        //default hodnoty, ktere se pripadne prepisou..
        //        $ret_array[0] = true;
        //    $ret_array[1] = "Všechny testy v pořádku! \n";

        $router_id = intval($router_id);

        $rs_q = $this->conn_mysql->query("SELECT ip_adresa, id FROM router_list WHERE id = '".$router_id."'");
        $rs_q_num = $rs_q->num_rows;

        if($rs_q_num <> 1) {

            $ret_array[0] = false;
            $ret_array[1] .= "Chyba! Nelze najít router dle předaných parametrů (id: ".$router_id.") \n";

            return $ret_array;

        }

        // $router_ip = mysql_result($rs_q, 0, 0);
        $rs_q->data_seek(0);
        list($router_ip) = $rs_q->fetch_row();

        $rs_login = $this->conn_mysql->query("SELECT value FROM settings WHERE name IN ('routeros_api_login_name', 'routeros_api_login_password') ");

        // $login_name = mysql_result($rs_login, 0, 0);
        // $login_pass = mysql_result($rs_login, 1, 0);
        $rs_login->data_seek(0);
        list($login_name) = $rs_login->fetch_row();
        $rs_login->data_seek(1);
        list($login_pass) = $rs_login->fetch_row();

        //
        // test pingu
        //

        exec("scripts/ping.sh ".$router_ip, $ping_output, $ping_ret);

        if(!($ping_output[0] > 0)) {
            //  NENI ODEZVA NA PING

            $ret_array[0] = false;
            $ret_array[1] = "Chyba! Router neodpovídá na odezvu Ping (id: ".$router_id.", ping: ".$ping_output[0].")";

            return $ret_array;
        }

        //
        // test API
        //
        // $API = new RouterOS();

        // //pokus o spojeni krz API
        // $conn = $API->connect($router_ip, $login_name, $login_pass);

        // if($conn == false) {

        //     $ret_array[0] = false;
        //     $ret_array[1] .= "Chyba! Nelze se spojit s routerem krz API. (ROS_API say: couldn't connect to router) \n";

        //     return $ret_array;

        // }

        // $conn = RouterOS::connect($ip, $login_user, $login_pass) or die("couldn't connect to router\n");

        $rosConfig = new Config([
            'host' => $router_ip,
            'user' => $login_name,
            'pass' => $login_pass,
            'port' => 18728,
        ]);

        try {
            $rosClient = new Client($rosConfig);
        } catch (Exception $exception) {
            $ret_array[0] = false;
            $ret_array[1] .= "Chyba! Nelze se spojit s routerem krz API. (ROS_API say: couldn't connect to router) \n";

            return $ret_array;
        }

        //
        // test SNMP
        //

        //test zda máme k dispozici SNMP funkce v PHP

        $rs_snmp_f = $this->test_snmp_function();

        if($rs_snmp_f[0] === false) {

            $ret_array[0] = false;
            $ret_array[1] .= "Chyba! ".$rs_snmp_f[1]."\n";

            return $ret_array;
        }

        $rs_snmp = snmpget($router_ip, "public", ".1.3.6.1.2.1.25.3.3.1.2.1", 300000);

        if($rs_snmp === false) {

            $ret_array[0] = false;
            $ret_array[1] .= "Chyba! Router korektne neodpovídá na SNMP GET dotaz. (".$rs_snmp.") \n";

            return $ret_array;
        }

        //debug result
        /*
        $ret_array[0] = false;
        $ret_array[1] = " generic error, (router_id: ".$router_id.", router_id: ".$router_ip." ";

        $ret_array[1] .=  " INFO: Ping: Average: ".$ping_avg."ms, Packetloss: ".$ping_packetloss."% ";

        //    $ret_array[1] .=  "\n INFO: SNMP GET load: ".$rs_snmp." \n";

        //    $ret_array[1] .= " login_name: ".$login_name.", login_pass: ".$login_pass."";
        $ret_array[1] .= ")";
        */
        //end of debug result

        $ret_array[0] = true;
        $ret_array[1] = "Všechny testy v pořádku! \n";

        //final return...
        return $ret_array;

    } //end of function test_router_for_monitoring

} //konec tridy Aglobal

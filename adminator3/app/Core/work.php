<?php

namespace App\Core;

use Psr\Container\ContainerInterface;
use SebastianBergmann\Type\VoidType;
use Illuminate\Support\Facades\Redis;
use HyssaDev\HibikenAsynqClient\Client;

class work
{
    // DI
    protected \Monolog\Logger $logger;

    protected \mysqli|\PDO $conn_mysql;

    protected \PgSql\Connection|\PDO|null $conn_pgsql;

    protected $sentinel;

    protected Redis $redis;

    // protected $container;

    protected $loggedUserEmail;

    public function __construct(ContainerInterface $container)
    {
        // $this->container = $container;
        $this->logger = $container->get('logger');
        $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');

        $this->sentinel = $container->get('sentinel');

        // needed for activating facade
        $this->redis = $container->get('redis');

        $this->loggedUserEmail = $this->sentinel->getUser()->email;

        $this->logger->info(message: __CLASS__ . "\\" . __FUNCTION__ . " called");
    }

    public function taskEnqueue(int $item_id): bool|int
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $asynq_client = new Client($this->redis);
        $res = $asynq_client->Enqueue([
            'typename' => "adminator3:workitem:$item_id",
            'payload' => [
                'item_id' => $item_id,
            ],
            'opts' => [
                'timeout' => 0, // TODO: change to 24h
            ]
        ], [
            'queue' => "adminator3:workitem",
            'group' => $item_id,
        ]);

        return $res;
    }

    public function work_handler($item_id): array
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        // prep vars

        //item_id - cislo ktery odpovida vzdy nejaky akci :)
        //seznam cisel a akcí
        // 1 - osvezeni net-n/sikany na reinhard-3
        // zbytek viz databáze

        $item_id = intval($item_id);

        $output = "";

        // load workitem's name from database
        $rs_item_name = $this->conn_mysql->query("SELECT name FROM workitems_names WHERE id = '$item_id' ");

        $rs_item_name->data_seek(0);
        list($item_name) = $rs_item_name->fetch_row();

        if (is_null($item_name)) {
            //TODO: add warning over bootstrap.JS
            $this->logger->warning(message: __CLASS__ . "\\" . __FUNCTION__ . ": parsing item_name failed (item_id $item_id)");
        } else {
            $this->logger->info(message: __CLASS__ . "\\" . __FUNCTION__ . ": parsed item_name: " . var_export($item_name, true));
        }

        // asynqClient part
        $rs_queue = $this->taskEnqueue($item_id);
        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": rs_queue: " . var_export($rs_queue, true));

        if ($rs_queue) {
            $rs_write = 1;
        } else {
            $rs_write = 0;
        }

        // save it into Archive of changes/Archiv Zmen
        $akce_az = "<b>akce:</b> požadavek na restart;<br>[<b>item_id</b>] => ".$item_id;
        $akce_az .= ", [<b>item_name</b>] => ".$item_name;

        $sql_az = "INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
            "('".$this->conn_mysql->real_escape_string($akce_az)."','" .$this->loggedUserEmail . "','".$rs_write."')";

        $add_az = $this->conn_mysql->query($sql_az);

        // generate output view
        $output .= "<div style=\"\">Požadavek na restart <b>\"".$item_name."\"</b> (No. ".$item_id.")";

        if ($rs_queue) {
            $output .= "<div> - <span style=\"color: green;\"> úspěšně přidán do fronty</span></div>";
        } else {
            $output .= "<div> - <span style=\"color: red;\"> chyba při přidání požadavku do fronty</span></div>";
        }

        if ($add_az) {
            $output .= "<div> - <span style=\"color: green;\"> úspěšně přidán do archivu změn.</span></div>";
        } else {
            $output .= "<div> - <span style=\"color: red;\"> chyba při přidání požadavku do archivu změn.</span></div>";
            $output .= "</div><div> sql: ".$sql_az."\n";
        }

        $output .= "</div>";


        return array($output);

    } //end of function work_handler

    public function workActionObjektyFiberDiff(string $changes, array $origData, $itemId): array
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $output = "";
        $work_output = [];

        //zmena sikany/NetN
        if (preg_match("/.*změna.*Šikana.*z.*/", $changes)
            or
            preg_match("/.*změna.*Povolen.*Inet.*z.*/", $changes)
        ) {
            $work_output[] = $this->work_handler("3");
        }

        //zmena IP adresy pokud je aktivni Sikana ci NetN
        if ((
            preg_match("/.*změna.*IP.*adresy.*z.*/", $changes)
            and
            (
                ($origData["sikana_status"] == "a")
                or
                ($origData["dov_net"] == "n")
            )
        )
        ) {
            $work_output[] = $this->work_handler("1"); //reinhard-3 (ros) - restrictions (net-n/sikana)
            $work_output[] = $this->work_handler("2"); //reinhard-wifi (ros) - restrictions (net-n/sikana)

            $work_output[] = $this->work_handler("3"); //reinhard-fiber - sikana/net-n

            $work_output[] = $this->work_handler("4"); //reinhard-fiber - radius
            $work_output[] = $this->work_handler("21"); //artemis - radius (tunel. verejky, optika)

            $work_output[] = $this->work_handler("6"); //(reinhard-fiber) - mikrotik.dhcp.leases.erase

            $work_output[] = $this->work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update

        } elseif (preg_match("/.*změna.*IP.*adresy.*z.*/", $changes)) {

            $work_output[] = $this->work_handler("4"); //reinhard-fiber - radius

            $work_output[] = $this->work_handler("6"); //(reinhard-fiber) - mikrotik.dhcp.leases.erase

            $work_output[] = $this->work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update
        }

        if (preg_match("/.*změna.*MAC.*adresy.*/", $changes)) {

            $work_output[] = $this->work_handler("4"); //reinhard-fiber - radius
            $work_output[] = $this->work_handler("21"); //artemis - radius (tunel. verejky, optika)

            $work_output[] = $this->work_handler("6"); //(reinhard-fiber) - mikrotik.dhcp.leases.erase
            $work_output[] = $this->work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update

        }

        // //zmena pripojneho bodu

        // //zmena tarifu

        //zmena cisla portu
        if (preg_match("/.*Číslo sw. portu.*/", $changes)) {
            $work_output[] = $this->work_handler("4"); //reinhard-fiber - radius
            $work_output[] = $this->work_handler("21"); //artemis - radius (tunel. verejky, optika)

            $work_output[] = $this->work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update
        }

        // $output .= var_export($work_output, true);

        foreach ($work_output as $id => $item) {
            $output .= $item[0];
        }

        return array($output);
    }

    public function workActionObjektyFiber(string $changes, int $itemId): array
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $output = "";
        $work_output = [];

        $work_output[] = $this->work_handler("3"); //rh-fiber - iptables
        $work_output[] = $this->work_handler("4"); //rh-fiber - radius
        $work_output[] = $this->work_handler("5"); //rh-fiber - shaper
        $work_output[] = $this->work_handler("6"); //reinhard-fiber - mikrotik.dhcp.leases.erase
        $work_output[] = $this->work_handler("7"); //trinity - sw.h3c.vlan.set.pl update

        $work_output[] = $this->work_handler("21"); //artemis - radius (tunel. verejky, optika)

        // $output .= var_export($work_output, true);

        foreach ($work_output as $id => $item) {
            $output .= $item[0];
        }

        return array($output);
    }

    public function workActionObjektyWifiDiff(string $changes, array $origData, $itemId)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $output = "";
        $work_output = [];

        // //zjistit, krz kterého reinharda jde objekt
        $reinhard_id = adminator::find_reinhard($itemId, $this->conn_mysql, $this->conn_pgsql);

        // "zmena sikany" or "zmena NetN"
        if (preg_match("/.*změna.*Šikana.*z.*/", $changes)
            or
            preg_match("/.*změna.*Povolen.*Inet.*z.*/", $changes)
        ) {
            if ($reinhard_id == 177) {
                $work_output[] = $this->work_handler("1");
            } elseif ($reinhard_id == 1) {
                $work_output[] = $this->work_handler("2");
            } elseif ($reinhard_id == 236) {
                $work_output[] = $this->work_handler("24");
            } else {
                //nenalezet pozadovany reinhard, takze osvezime vsechny

                $work_output[] = $this->work_handler("1");
                $work_output[] = $this->work_handler("2");
                $work_output[] = $this->work_handler("24");
            }
        }

        //zmena IP adresy
        if (preg_match("/.*změna.*IP.*adresy.*z.*/", $changes)) {
            //pokud: zmena IP adresy bez aktivovaného omezení, tak staci items nize:
            $work_output[] = $this->work_handler("5");
            $work_output[] = $this->work_handler("13");
            $work_output[] = $this->work_handler("20");
            $work_output[] = $this->work_handler("23");

            $work_output[] = $this->work_handler("14");

            // pokud je aktivni omezeni -> radsi vynutit restart net-n/sikany u vseho, resp i zbytku
            if (($origData["sikana_status"] == "a")
                or
                ($origData["dov_net"] == "n")) {

                $work_output[] = $this->work_handler("1");
                $work_output[] = $this->work_handler("2");
                $work_output[] = $this->work_handler("3");
                $work_output[] = $this->work_handler("24");
            }
        }

        //zmena linky -- shaper / filtrace
        if (preg_match("/.*změna.*pole.*id_tarifu.*/", $changes)
            or
            preg_match("/.*změna.*Tarifu.*/", $changes)
            or
            preg_match("/.*změna.*pole.*client_ap_ip.*/", $changes)
        ) {
            if (preg_match("/.*změna.*pole.*client_ap_ip.*/", $changes)) {
                $work_output[] = $this->work_handler("14");
            }

            if ($reinhard_id == 177) {
                $work_output[] = $this->work_handler("20");
            } elseif ($reinhard_id == 1) {
                $work_output[] = $this->work_handler("13");
            } elseif ($reinhard_id == 236) {
                $work_output[] = $this->work_handler("23");
            } else {
                $work_output[] = $this->work_handler("13");
                $work_output[] = $this->work_handler("20");
                $work_output[] = $this->work_handler("23");
            }

            // filtrace asi neni treba
            // $work_output[] = $this->work_handler("14");
        }

        //zmena tunneling_ip ci tunel záznamů
        // --> radius artemis
        // zde dodelat zmenu IP adresy, pokud tunelovana verejka
        if (
            preg_match("/.*změna.*pole.*tunnelling_ip.*/", $changes)
            or
            preg_match("/.*změna.*pole.*tunnel_user.*/", $changes)
            or
            preg_match("/.*změna.*pole.*tunnel_pass.*/", $changes)
        ) {
            $work_output[] = $this->work_handler("21");
        }

        // //zmena MAC adresy .. zatim se nepouziva u wifi

        //zmena DNS záznamu, asi jen u veřejných IP adresa
        // --> restart DNS auth. serveru
        if (preg_match("/.*změna.*pole.*dns_jmeno.*/", $changes)) {
            $work_output[] = $this->work_handler("9");
            $work_output[] = $this->work_handler("10");
            $work_output[] = $this->work_handler("11");
            $work_output[] = $this->work_handler("12");
        }

        // $output .= var_export($work_output, true);

        foreach ($work_output as $id => $item) {
            $output .= $item[0];
        }

        return array($output);
    }

    public function workActionObjektyWifi(string $changes, int $itemId, array $args): array
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $output = "";
        $work_output = [];

        $reinhard_id = adminator::find_reinhard($itemId, $this->conn_mysql, $this->conn_pgsql);

        if ($args['form_typ_ip'] == 4) {
            //L2TP verejka
            $work_output[] = $this->work_handler("21");
        }

        $work_output[] = $this->work_handler("14");

        //zde dodat if zda-li je NetN ci SikanaA
        if ((preg_match("/.*<b>\[dov_net\]<\/b> => n.*/", $changes) == 1)
                or (preg_match("/.*<b>\[sikana_status\]<\/b> => a.*/", $changes) == 1)) {

            if ($reinhard_id == 177) {
                $work_output[] = $this->work_handler("1");
            } elseif ($reinhard_id == 1) {
                $work_output[] = $this->work_handler("2");
            } elseif ($reinhard_id == 236) {
                $work_output[] = $this->work_handler("24");
            } else {
                //nenalezet pozadovany reinhard, takze osvezime vsechny

                $work_output[] = $this->work_handler("1");
                $work_output[] = $this->work_handler("2");
                $work_output[] = $this->work_handler("24");

            } //end of else - if reinhard_id
        }

        if ($reinhard_id == 177) {
            $work_output[] = $this->work_handler("20");
        } elseif ($reinhard_id == 1) {
            $work_output[] = $this->work_handler("13");
        } elseif ($reinhard_id == 236) {
            $work_output[] = $this->work_handler("23");
        } else {
            $work_output[] = $this->work_handler("13");
            $work_output[] = $this->work_handler("20");
            $work_output[] = $this->work_handler("23");
        }

        // $output .= var_export($work_output, true);

        foreach ($work_output as $id => $item) {
            $output .= $item[0];
        }

        return array($output);
    }

    public function workActionTopologyRouterAdd(): void
    {
        // TODO: enable actions for topology/router-add

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
    }

    public function workActionTopologyRouterDiff(): void
    {
        // TODO: enable actions for topology/router-update
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
    }

}

<?php

namespace App\Core;

use Psr\Container\ContainerInterface;
use SebastianBergmann\Type\VoidType;
use Illuminate\Support\Facades\Redis;
use HyssaDev\HibikenAsynqClient\Client;
use Exception;

class work
{
    // DI
    protected \Monolog\Logger $logger;

    protected \mysqli|\PDO $conn_mysql;

    protected \PgSql\Connection|\PDO|null $conn_pgsql;

    public \PDO|null $pdoMysql;

    protected $sentinel;

    protected Redis $redis;

    // protected $container;

    protected $loggedUserEmail;

    /**
     * {@inheritdoc}
     */
    public array $p_bs_alerts = [];

    public function __construct(ContainerInterface $container)
    {
        // $this->container = $container;
        $this->logger = $container->get('logger');
        $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');
        $this->pdoMysql = $container->get('pdoMysql');

        $this->sentinel = $container->get('sentinel');

        // needed for activating facade
        $this->redis = $container->get('redis');

        $this->loggedUserEmail = $this->sentinel->getUser()->email;

        $this->logger->info(message: __CLASS__ . "\\" . __FUNCTION__ . " called");
    }

    public function callPdoQueryAndFetch($query): array
    {
        $rs_error = null;
        try {
            $rs = $this->pdoMysql->query($query);
        } catch (Exception $e) {
            $rs_error = $e->getMessage();
        }

        if (is_object($rs)) {
            $rs_data = $rs->fetchAll();

        } else {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": PDO result is not object");
            $rs_data = [];
        }

        return [$rs_data, $rs_error];
    }

    public function getAllItems(): array
    {
        $q = "SELECT id, name FROM workitems_names ORDER BY id";
        list($data_rs, $dotaz_error) = $this->callPdoQueryAndFetch($q);

        if ($dotaz_error != null) {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": Caught Exception: " . var_export($dotaz_error, true));
            $this->p_bs_alerts["Nelze načíst data pro výpis akcí pro manuální restart. <br>(SQL error: $dotaz_error)"] = "danger";

            return [false, []];

        } elseif (count($data_rs) < 1) {
            $this->p_bs_alerts["Žádné data pro výpis akcí pro manuální restart."] = "warning";

            return [true, []];
        } else {
            foreach ($data_rs as $key => $val) {
                $itemsList[] = ["id" => $val["id"], "name" => $val["name"]];
            }
            return [true, $itemsList];
        }
    }

    public function getItemName(int $id): string|null
    {
        $rs_item_name = $this->conn_mysql->query("SELECT name FROM workitems_names WHERE id = '$id' ");

        $rs_item_name->data_seek(0);
        list($item_name) = $rs_item_name->fetch_row();

        return $item_name;
    }

    public function taskEnqueue(int $item_id): bool|int
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        try {
            $asynq_client = new Client($this->redis);
            $res = $asynq_client->Enqueue([
                'typename' => "adminator3:workitem:basic",
                'payload' => [
                    'item_id' => $item_id,
                    'createdAt' => time(),
                    'createdBy' => $this->loggedUserEmail,
                ],
                'opts' => [
                    'timeout' => 86400,
                ]
            ], [
                'queue' => "adminator3:workitem",
                'group' => $item_id,
            ]);
        } catch (\RedisException $ex) {
            $m = $ex->getMessage();
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": Redis error: $m");
            return false;
        }

        return $res;
    }

    public function taskGroupList(): array
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        try {
            $allGroups = $this->redis::sinter("asynq:{adminator3:workitem}:groups");
        } catch (\RedisException $ex) {
            $m = $ex->getMessage();

            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": Redis error: $m");
            $this->p_bs_alerts["Nelze načíst data pro výpis tasks groups. <br>(Redis error: $m)"] = "danger";

            return [false, "Redis error: $m"];
        }

        if (empty($allGroups)) {
            $this->p_bs_alerts["Queue: Data pro výpis tasks groups nenalezeny."] = "warning";
            return [false, "Redis: empty results: no data in groups"];
        }

        for ($i = 0; $i < count($allGroups); $i++) {
            $groupId = $allGroups[$i];

            $groupCount = $this->redis::zcount("asynq:{adminator3:workitem}:g:$groupId", "-inf", "+inf");
            if ($groupCount == 0) {
                $this->p_bs_alerts["Queue: Tasks group $groupId neobsahuje žádné úkoly."] = "warning";
            }

            // N.B. smarty is not displaying NULL values, so we don't care about return value of getItemName()
            $r[$groupId] = ["count" => $groupCount, "name" => $this->getItemName($groupId)];
        }

        if (empty($r)) {
            $this->p_bs_alerts["Queue: Tasks groups neobsahují žádné úkoly."] = "warning";
            return [false, "Redis: empty results: no tasks in any group"];
        }

        return [true, $r];
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
        $item_name = $this->getItemName($item_id);

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
    }

    public function workActionTopologyNodeDiff(): void
    {
        // TODO: fix automatic restarts
        // if(ereg(".*Routeru, kde se provádí filtrace.*", $pole3)) {
        //     Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart
        // }

        // if(ereg(".*<b>Routeru</b>.*", $pole3)) {
        //     Aglobal::work_handler("1");	//reinhard-3 (ros) - restrictions (net-n/sikana)
        //     Aglobal::work_handler("20"); 	//reinhard-3 (ros) - shaper (client's tariffs)

        //     Aglobal::work_handler("24");	//reinhard-5 (ros) - restrictions (net-n/sikana)
        //     Aglobal::work_handler("23");	//reinhard-5 (ros) - shaper (client's tariffs)

        //     Aglobal::work_handler("13");	//reinhard-wifi (ros) - shaper (client's tariffs)
        //     Aglobal::work_handler("2");	//reinhard-wifi (ros) - restrictions (net-n/sikana)

        //     Aglobal::work_handler("14"); 	//(trinity) filtrace-IP-on-Mtik's-restart

        // }

        // if(ereg(".*vlan_id.*", $pole3)) {
        //     Aglobal::work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update

        //     Aglobal::work_handler("4"); //reinhard-fiber - radius
        //     Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
        // }

        // if(ereg(".*změna.*koncového.*zařízení.*", $pole3)) {
        //     Aglobal::work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update

        //     Aglobal::work_handler("4"); //reinhard-fiber - radius
        //     Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
        // }
    }

    public function workActionTopologyNodeAdd(): void
    {
        // TODO: add work items
    }

}

<?php

namespace App\Core;

use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager as DB;
use Exception;

class objekt extends adminator
{
    public \mysqli|\PDO $conn_mysql;

    public \PgSql\Connection|\PDO|null $conn_pgsql;

    public \PDO|null $pdoMysql;

    public \PDO|null $pdo;

    public \Monolog\Logger $logger;

    protected $container;

    // protected $validator;

    protected $sentinel;

    public $adminator; // handler for instance of adminator class

    public $request;

    protected $work;

    protected $loggedUserEmail;

    // public ?string $userIdentityUsername = null;

    public $dns_find;

    public $ip_find;

    public $mod_vypisu;

    public $es;

    public $razeni;

    public $list;

    public $dotaz_source;

    public $listErrors;

    public $csrf_html;

    public $allowedUnassignFromVlastnik = false;

    public $listAllowedActionUpdate = false;

    public $listAllowedActionErase = false;

    public $listAllowedActionGarant = false;

    public $nod_find;

    public $sql_nod;

    public ?int $update_id;

    public $odeslano;

    public $send;

    public $mod_objektu;

    public $form_ip_error;

    public $form_selected_nod;

    public $form_dns;

    public $form_mac;

    public $form_ip;

    public $form_typ_ip;

    public $form_typ;

    public $form_id_tarifu;

    public $form_client_ap_ip;

    public $form_pozn;

    public $form_dov_net;

    public $form_sikana_status;

    public $form_sikana_cas;

    public $form_sikana_text;

    public $form_port_id;

    public $form_another_vlan_id;

    public $origDataArray;

    public $updatedDataArray;

    public $addedDataArray;

    private int $insertedId;

    private $action_info;

    private $action_error; // indication/control variable for non-fatal error

    private $action_fail; // indication/control variable for blocker

    /**
     * {@inheritdoc}
     */
    public $p_bs_alerts = array();

    public function __construct(ContainerInterface $container, $usePDO = false)
    {
        $this->container = $container;
        // $this->validator = $container->get('validator');
        $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');
        $this->pdoMysql = $container->get('pdoMysql');

        if($usePDO == true) {
            $this->pdo = $container->get('pdoPgsql');
        } else {
            $this->pdo = null;
        }

        $this->logger = $container->get('logger');
        $this->sentinel = $container->get('sentinel');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->loggedUserEmail = $this->sentinel->getUser()->email;

        $this->work = new \App\Core\work($this->container);
    }

    /**
    * call PDO query & fetchAll and catch errors
    *
    * @return Array <array, string>
    */
    public function callPdoQueryAndFetch(string $query, string $handler = 'pdo'): array
    {
        $rs_data = [];
        $rs_error = null;
        $rs = null;

        if($this->$handler instanceof \PDO) {
            $driver_name = $this->$handler->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": driver_name: " . var_export($driver_name, true));
        } else {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": handler is not PDO type");
            return [$rs_data, "database handler is not PDO type (" . var_export($handler, true) . ")"];
        }

        try {
            $rs = $this->$handler->query($query);
        } catch (Exception $e) {
            $rs_error = $e->getMessage();
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": Caught exception: " . var_export($rs_error, true));
            return [$rs_data, $rs_error];
        }

        if(is_object($rs)) {
            $rs_data = $rs->fetchAll();
        } else {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": PDO result is not object");
            $rs_error = "ERROR: PDO result is not object";
        }

        return [$rs_data, $rs_error];
    }

    public function listGetOrderItems()
    {
        $output = "";

        $output .= "\n<tr>\n";
        $output .= '<td colspan="1">';

        $output .= "\n\n <input type=\"radio\" ";
        if (($this->razeni == 1)) {
            $output .= " checked ";
        }
        $output .= "name=\"razeni\" value=\"1\" onClick=\"form1.submit();\" > ";
        $output .= "<img src=\"img2/ses.png\" alt=\"ses\" width=\"15px\" height=\"10px\" >";

        $output .= " | <input type=\"radio\" ";
        if (($this->razeni == 2)) {
            $output .= " checked ";
        }
        $output .= " name=\"razeni\" value=\"2\" onClick=\"form1.submit();\"> \n";
        $output .= "<img src=\"img2/vzes.png\" alt=\"vzes\" width=\"15px\" height=\"10px\" >";

        $output .= '</td>
                    <td colspan="3">';

        $output .= "<input type=\"radio\" ";
        if (($this->razeni == 3)) {
            $output .= " checked ";
        }
        $output .= "name=\"razeni\" value=\"3\" onClick=\"form1.submit();\" > ";
        $output .= "<img src=\"img2/ses.png\" alt=\"ses\" width=\"15px\" height=\"10px\" >";

        $output .= " | <input type=\"radio\" ";
        if (($this->razeni == 4)) {
            $output .= " checked ";
        }
        $output .= " name=\"razeni\" value=\"4\" onClick=\"form1.submit();\"> \n";
        $output .= "<img src=\"img2/vzes.png\" alt=\"vzes\" width=\"15px\" height=\"10px\" >";

        $output .= '</td>
                    <td>';

        $output .= "<input type=\"radio\" ";
        if (($this->razeni == 9)) {
            $output .= " checked ";
        }
        $output .= "name=\"razeni\" value=\"9\" onClick=\"form1.submit();\" > ";
        $output .= "<img src=\"img2/ses.png\" alt=\"ses\" width=\"15px\" height=\"10px\" >";

        $output .= " | <input type=\"radio\" ";
        if (($this->razeni == 10)) {
            $output .= " checked ";
        }
        $output .= " name=\"razeni\" value=\"10\" onClick=\"form1.submit();\"> \n";
        $output .= "<img src=\"img2/vzes.png\" alt=\"vzes\" width=\"15px\" height=\"10px\" >";

        $output .= '</td>
        <td></td>';

        // $output .= "<td><b>client ap </b></td>";

        // $output .= '
        //     <td align="center" ><b>upravit</b></td>
        //     <td align="center" ><b>smazat</b></td>
        //     <td><b>třída </b></td>
        // <td><b>Aktivní</b></td>
        // <td><b>Test obj.</b></td>
        // <td><b>Linka </b></td>
        // <td><b>Omezení </b></td>';

        $output .= "\n</tr>\n";

        return array($output);
    }

    private function listPrepareVars()
    {
        foreach ($this->request->getQueryParams() as $i => $v) {
            if(preg_match('/^(mod_vypisu|es|razeni|list)$/', $i) and strlen($v) > 0) {
                $$i = $this->request->getQueryParams()[$i];
            }
        }

        if(isset($mod_vypisu)) {
            if(!(preg_match('/^([[:digit:]])+$/', $mod_vypisu))) {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Chyba! Nesouhlasi vstupni data. (mod vypisu) </div>";
            }
            $this->mod_vypisu = $mod_vypisu;
        }

        if((strlen($this->dns_find) > 0)) {
            if(!(preg_match('/^([[:alnum:]]|_|-|\.|\%)+$/', $this->dns_find))) {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v poli \"Hledání dle dns\". (Povolené: a-z,A-Z,0-9,-, _,. )</div>";
            }
        }

        if((strlen($this->ip_find) > 0)) {
            if(!(preg_match('/^([[:digit:]]|\.|/)+$/', $this->ip_find))) {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v poli \"Hledání dle ip adresy\". (Povolené: a-z,A-Z,0-9,-, _,. )</div>";
            }
        }

        if(isset($es)) {
            if(!(preg_match('/^([[:digit:]])+$/', $es))) {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v poli \"Sekundární hledání\". </div>";
            }
            $this->es = $es;
        }

        if(isset($razeni)) {
            if(!(preg_match('/^([[:digit:]])+$/', $razeni))) {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v promenné \"razeni\". </div>";
            }
            $this->razeni = $razeni;
        }

        if(isset($list)) {
            if(!(preg_match('/^([[:digit:]])+$/', $list))) {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v promenné \"list\". </div>";
            }
            $this->list = $list;
        }

        if(strlen($this->listErrors) > 0) {
            return false;
        }

        return true;
    }

    private function listGenerateSql()
    {
        // detect mode
        //
        if ((strlen($this->dns_find) > 0)) {
            $co = 1;
            $sql = $this->dns_find;
        }

        if ((strlen($this->ip_find) > 0)) {
            $co = 2;
            $sql = $this->ip_find;
        }

        list($se, $order) = $this->select($this->es, $this->razeni);

        $tarif_sql = "";

        if($this->mod_vypisu == 1) {
            $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '0' ");

            $i = 0;

            while($data_f = $dotaz_f->fetch_array()) {
                if($i == 0) {
                    $tarif_sql .= "AND ( ";
                }
                if($i > 0) {
                    $tarif_sql .= " OR ";
                }

                $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]."";

                $i++;
            }

            if($i > 0) {
                $tarif_sql .= " ) ";
            }

        } elseif($this->mod_vypisu == 2) {
            $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '1' ");

            $i = 0;

            while($data_f = $dotaz_f->fetch_array()) {
                if($i == 0) {
                    $tarif_sql .= "AND ( ";
                }
                if($i > 0) {
                    $tarif_sql .= " OR ";
                }

                $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";

                $i++;
            }

            if($i > 0) {
                $tarif_sql .= " ) ";
            }

        }
        // $output .= "dotaz_tarif: ".$tarif_sql." /";

        if($co == 1) {
            $sql = "%".$sql."%";

            $dotaz_source = "SELECT * FROM objekty WHERE dns_jmeno LIKE '$sql' ".$se.$tarif_sql.$order;
        } elseif($co == 2) {
            $dotaz_source = "SELECT * FROM objekty WHERE ip <<= '$sql' ".$se.$tarif_sql.$order;
        } elseif($co == 3) {
            $dotaz_source = "SELECT * FROM objekty WHERE id_cloveka=".$id;
        } else {
            // $output .= "";
            return false;
        }

        $this->dotaz_source = $dotaz_source;

        return true;
    }

    /**
    * list body content
    *
    * @return Array <string, string, string> - html output, error message(s), export link
    */
    public function listGetBodyContent(): array
    {
        $output = "";
        $error = "";
        $exportLink = "";

        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": current identity: ".var_export($this->userIdentityUsername, true));

        // checking levels for update/erase/..
        if ($this->adminator->checkLevel(29) === true) {
            $this->listAllowedActionUpdate = true;
        }
        if ($this->adminator->checkLevel(33) === true) {
            $this->listAllowedActionErase = true;
        }
        if ($this->adminator->checkLevel(34) === true) {
            $this->listAllowedActionGarant = true;
        }
        if ($this->adminator->checkLevel(59) === true) {
            $export_povolen = true;
        }

        if ($export_povolen === true) {
            $exportLink = $this->export_vypis_odkaz();
        }

        // prepare vars
        //
        $prepVarsRs = $this->listPrepareVars();
        if($prepVarsRs === false) {
            return array("", $this->listErrors, $exportLink);
        }

        // detect mode (again)
        //
        if ((strlen($this->dns_find) > 0)) {
            $co = 1;
            $sql = $this->dns_find;
        }

        if ((strlen($this->ip_find) > 0)) {
            $co = 2;
            $sql = $this->ip_find;
        }

        $output .= $this->vypis_tab(1);

        $output .= $this->vypis_tab_first_rows($this->mod_vypisu);

        list($output_razeni) = $this->listGetOrderItems();
        $output .= $output_razeni;

        $output .=  "</form>";

        $generateSqlRes = $this->listGenerateSql();
        if($generateSqlRes === false) {
            return array("", '<div class="alert alert-danger" role="alert">Chyba! Nepodarilo se vygenerovat SQL dotaz.</div>', '');
        }
        // paging
        //
        $poradek = "es=".$this->es."&dns_find=".$this->dns_find."&ip_find=".$this->ip_find . "&mod_vypisu=".$this->mod_vypisu;

        foreach ($this->request->getQueryParams() as $i => $v) {
            if(preg_match('/^(odeslano|razeni)$/', $i) and strlen($v) > 0) {
                $poradek .= "&" . $i . "=" . $this->request->getQueryParams()[$i];
            }
        }

        if($this->pdo instanceof \PDO) {
            $dbh = $this->pdo;
        } else {
            $dbh = $this->conn_pgsql;
        }

        $listovani = new \c_listing_objekty($dbh, "/objekty?".$poradek."&menu=1", 30, $this->list, "<center><div class=\"text-listing2\">\n", "</div></center>\n", $this->dotaz_source);
        $listovani->echo = false;

        if(($this->list == "") || ($this->list == "1")) {
            $bude_chybet = 0;
        } //pokud není list zadán nebo je první bude ve výběru sql dotazem chybet 0 záznamů
        else {
            $bude_chybet = (($this->list - 1) * $listovani->interval);
        }   //jinak jich bude chybet podle závislosti na listu a intervalu

        if(intval($listovani->interval) > 0 and intval($bude_chybet) > 0) {
            $this->dotaz_source = $this->dotaz_source . " LIMIT ". intval($listovani->interval)." OFFSET ".intval($bude_chybet)." ";
        }

        // header listing
        $output .= $listovani->listInterval();

        $this->logger->debug(
            "objekt\listGetBodyContent: dump vars: "
                                ."dotaz_source: " . var_export($this->dotaz_source, true)
                                . ", sql: " . var_export($sql, true)
                                . ", co: " . var_export($co, true)
        );

        // get items
        $output .= $this->vypis($sql, $co, 0, $this->dotaz_source);

        // render end of table
        $output .= $this->vypis_tab(2);

        // footer listing
        $output .= $listovani->listInterval();

        // with other "fatal" errors (some of p_bs_alerts),
        // we dont want render "usual" table header, but only error messages (and bootstrap alerts)
        if(strlen($this->listErrors) > 0) {
            return array('', $this->listErrors, '');
        }

        return array($output, $error, $exportLink);
    }

    public function actionPrepareVars()
    {
        $nod_find = $_POST["nod_find"];

        if((strlen($nod_find) < 1)) {
            $nod_find = "%";
        } else {
            // TODO: add validation of nod_find

            if(!(preg_match("/^%.*%$/", $nod_find))) {
                $nod_find = "%".$nod_find."%";
            }
        }

        $this->nod_find = $nod_find;

        // TODO: add validation fo control vars

        $this->update_id = $_POST["update_id"];
        $this->odeslano = $_POST["odeslano"];
        $this->send = $_POST["send"];
    }

    public function actionWifi(): string
    {
        $output = "";

        if (($this->update_id > 0)) {
            $update_status = 1;
        } else {
            $update_status = 0;
        }

        // TODO: add check others reload stuff
        if(($update_status == 1 and !(isset($this->send)))) {
            //rezim upravy
            $dotaz_upd = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE id_komplu='".intval($this->update_id)."' ");
            $radku_upd = pg_num_rows($dotaz_upd);

            if ($radku_upd == 0) {
                $output .= "Chyba! Požadovaná data nelze načíst! ";
            } else {
                while($data = pg_fetch_array($dotaz_upd)) {
                    // primy promenny
                    $this->form_dns = $data["dns_jmeno"];
                    $this->form_ip = $data["ip"];
                    $this->form_mac = $data["mac"];
                    $this->form_typ = $data["typ"];
                    $this->form_pozn = $data["poznamka"];
                    $this->form_selected_nod = $data["id_nodu"];

                    $this->form_sikana_text = $data["sikana_text"];
                    $this->form_client_ap_ip = $data["client_ap_ip"];

                    $this->form_id_tarifu = $data["id_tarifu"];

                    // neprimy :) -> musi se zkonvertovat

                    $dov_net_l = $data["dov_net"];
                    if($dov_net_l == "a") {
                        $this->form_dov_net = 2;
                    } else {
                        $this->form_dov_net = 1;
                    }

                    $verejna_l = $data["verejna"];

                    if($data["tunnelling_ip"] == "1") { //tunelovaná verejka
                        $this->form_typ_ip = "4";

                        $tunnel_user = $data["tunnel_user"];
                        $tunnel_pass = $data["tunnel_pass"];

                    } elseif($verejna_l == "99") {
                        $this->form_typ_ip = "1";
                    } else {
                        $this->form_typ_ip = "2";
                        $vip_rozsah = $verejna_l;
                    }

                    $sikana_status_l = $data["sikana_status"];
                    if(preg_match("/a/", $sikana_status_l)) {
                        $this->form_sikana_status = 2;
                    } else {
                        $this->form_sikana_status = 1;
                    }
                    $sikana_cas_l = $data["sikana_cas"];
                    if(strlen($sikana_cas_l) > 0) {
                        $this->form_sikana_cas = $sikana_cas_l;
                    }

                }

            }

        } else {
            // rezim pridani, ukladani
            $this->form_dns = trim($_POST["dns"]);
            $this->form_ip = $_POST["ip"];
            $this->form_typ = $_POST["typ"];

            $this->form_typ_ip = $_POST["typ_ip"];
            $this->form_dov_net = $_POST["dov_net"];
            $this->form_id_tarifu = $_POST["id_tarifu"];
            $this->form_mac = $_POST["mac"];
            $verejna = $_POST["verejna"];
            $vip_rozsah = $_POST["vip_rozsah"];
            $this->form_pozn = trim($_POST["pozn"]);

            //systémove
            $this->send = $_POST["send"];
            $this->form_selected_nod = $_POST["selected_nod"];

            // dalsi
            $this->form_sikana_status = $_POST["sikana_status"];
            $this->form_sikana_cas = $_POST["sikana_cas"];
            $this->form_sikana_text = $_POST["sikana_text"];

            //$vip_snat_lip = $_POST["vip_snat_lip"];
            $this->form_client_ap_ip = $_POST["client_ap_ip"];

            $tunnel_user = $_POST["tunnel_user"];
            $tunnel_pass = $_POST["tunnel_pass"];
        }

        //co mame: v promeny selected_nod mame id nodu kam se to bude pripojovat
        // co chcete: ip adresu , idealne ze spravnyho rozsahu :)

        $this->generujdata();

        if((strlen($this->form_ip) > 0)) {
            $this->checkip($this->form_ip);
        }

        if((strlen($this->form_dns) > 0)) {
            $this->checkdns($this->form_dns);
        }
        if((strlen($this->form_mac) > 0)) {
            $this->checkmac($this->form_mac);
        }
        if((strlen($this->form_sikana_cas) > 0)) {
            $this->checkcislo($this->form_sikana_cas);
        }
        if((strlen($this->form_selected_nod) > 0)) {
            $this->checkcislo($this->form_selected_nod);
        }

        if((strlen($this->form_client_ap_ip) > 0)) {
            $this->checkip($this->form_client_ap_ip);
        }

        if($this->form_sikana_status == 2) {
            $this->checkSikanaCas($this->form_sikana_cas);

            $this->checkSikanaText($this->form_sikana_text);
        }

        if($this->form_typ_ip == 4) {
            if((strlen($tunnel_user) > 0)) {
                $this->check_l2tp_cr($tunnel_user);
            }
            if((strlen($tunnel_pass) > 0)) {
                $this->check_l2tp_cr($tunnel_pass);
            }
        }

        // checkne se jestli jsou vsechny udaje
        if((($this->form_dns != "") and ($this->form_ip != "")) and ($this->form_selected_nod > 0) and (($this->form_id_tarifu >= 0))) :

            if($update_status != 1 and isset($this->odeslano)) {
                $this->ip_find = $this->form_ip."/32";

                //zjisti jestli neni duplicitni dns, ip
                $MSQ_DNS = pg_query($this->conn_pgsql, "SELECT ip FROM objekty WHERE dns_jmeno = '$this->form_dns' ");
                $MSQ_IP = pg_query($this->conn_pgsql, "SELECT ip FROM objekty WHERE ip <<= '$this->ip_find' ");

                if (pg_num_rows($MSQ_DNS) <> 0) {
                    $this->action_error .= "<h4>Dns záznam ( ".$this->form_dns." ) již existuje!!!</h4>";
                    $this->action_fail = "true";
                }
                if (pg_num_rows($MSQ_IP) <> 0) {
                    $this->action_error .= "<h4>IP adresa ( ".$this->form_ip." ) již existuje!!!</h4>";
                    $this->action_fail = "true";
                }

                //duplicitni tunnel_pass/user
                if($this->form_typ_ip == 4) {
                    $MSQ_TUNNEL_USER = pg_query($this->conn_pgsql, "SELECT tunnel_user FROM objekty WHERE tunnel_user = '$tunnel_user' ");
                    $MSQ_TUNNEL_PASS = pg_query($this->conn_pgsql, "SELECT tunnel_pass FROM objekty WHERE tunnel_pass = '$tunnel_pass' ");

                    if(pg_num_rows($MSQ_TUNNEL_USER) <> 0) {
                        $this->action_error .= "<h4>Login k tunelovacímu serveru (".$tunnel_user.") již existuje!!!</h4>";
                        $this->action_fail = "true";
                    }
                    if(pg_num_rows($MSQ_TUNNEL_PASS) <> 0) {
                        $this->action_error .= "<h4>Heslo k tunelovacímu serveru (".$tunnel_pass.") již existuje!!!</h4>";
                        $this->action_fail = "true";
                    }
                }

            }

            // check v modu uprava
            if (($update_status == 1 and (isset($this->odeslano)))) {
                $this->ip_find = $this->form_ip."/32";

                //zjisti jestli neni duplicitni dns, ip
                $MSQ_DNS2 = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE ( dns_jmeno = '$this->form_dns' AND id_komplu != '".intval($this->update_id)."' ) ");
                $MSQ_IP2 = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE ( ip <<= '$this->ip_find' AND id_komplu != '".intval($this->update_id)."' ) ");

                if(pg_num_rows($MSQ_DNS2) <> 0) {
                    $this->action_error .= "<h4>Dns záznam ( ".$this->form_dns." ) již existuje!!!</h4>";
                    $this->action_fail = "true";
                }
                if(pg_num_rows($MSQ_IP2) <> 0) {
                    $this->action_error .= "<h4>IP adresa ( ".$this->form_ip." ) již existuje!!!</h4>";
                    $this->action_fail = "true";
                }

                //duplicitni tunnel_pass/user
                if($this->form_typ_ip == 4) {
                    $MSQ_TUNNEL_USER = pg_query($this->conn_pgsql, "SELECT tunnel_user FROM objekty WHERE ( tunnel_user = '$tunnel_user' AND id_komplu != '".intval($this->update_id)."' ) ");
                    $MSQ_TUNNEL_PASS = pg_query($this->conn_pgsql, "SELECT tunnel_pass FROM objekty WHERE ( tunnel_pass = '$tunnel_pass' AND id_komplu != '".intval($this->update_id)."' ) ");

                    if(pg_num_rows($MSQ_TUNNEL_USER) > 0) {
                        $this->action_error .= "<h4>Login k tunelovacímu serveru (".$tunnel_user.") již existuje!!!</h4>";
                        $this->action_fail = "true";
                    }
                    if(pg_num_rows($MSQ_TUNNEL_PASS) > 0) {
                        $this->action_error .= "<h4>Heslo k tunelovacímu serveru (".$tunnel_pass.") již existuje!!!</h4>";
                        $this->action_fail = "true";
                    }
                }
            }

            // checknem stav vysilace a filtraci
            $msq_stav_nodu = $this->conn_mysql->query("SELECT stav, router_id FROM nod_list WHERE id= '".intval($this->form_selected_nod)."' ");
            $msq_stav_nodu_radky = $msq_stav_nodu->num_rows;

            while ($data = $msq_stav_nodu->fetch_array()) {
                $stav_nodu = $data["stav"];
                $router_id = $data["router_id"];
            }

            if ($stav_nodu == 2) {
                $this->action_info .= "<div style=\"color: orange; \" ><h4>UPOZORNĚNÍ: Tento přípojný bod je vytížen, mohou se vyskytovat problémy. </h4></div>";
            } elseif ($stav_nodu == 3) {
                $this->action_info .= "<div style=\"color: orange; \"><h4>UPOZORNĚNÍ: Tento přípojný bod je přetížen. </h4></div>";
            } elseif ($stav_nodu == 3) {
                $this->action_fail = "true";
                $this->p_bs_alerts["Tento přípojný bod je přetížen, vyberte prosím jiný."] = "danger";
            }

        // kontrola jestli se muze povolit inet / jestli jsou pozatavené fakturace
        $poz_fakt_clovek = pg_query($this->conn_pgsql, "SELECT id_cloveka, dov_net FROM objekty WHERE id_komplu = '".intval($this->update_id)."' ");
        $poz_fakt_clovek_radku = pg_num_rows($poz_fakt_clovek);

        while ($data_poz_f_clovek = pg_fetch_array($poz_fakt_clovek)) {
            $id_cloveka = $data_poz_f_clovek["id_cloveka"];
            $dov_net_puvodni = $data_poz_f_clovek["dov_net"];
        }

        if ((($id_cloveka > 1) and ($update_status == 1))) {

            $pozastavene_fakt = pg_query($this->conn_pgsql, "SELECT billing_suspend_status FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka)."' ");
            $pozastavene_fakt_radku = pg_num_rows($pozastavene_fakt);

            if ($pozastavene_fakt_radku == 1) {
                while ($data_poz_fakt = pg_fetch_array($pozastavene_fakt)) {
                    $billing_suspend_status = intval($data_poz_fakt["billing_suspend_status"]);
                }
            } else {
                $output .= "Chyba! nelze vybrat vlastníka.";
            }

            // $output .= "debug: id_fakturacni_skupiny: ".$pozastavene_fakturace_id." id_cloveka: $id_cloveka ,dov_net-puvodni: $dov_net_puvodni , povolen inet: $dov_net";

            if($billing_suspend_status == 1) {
                // budeme zli
                // prvne zjisteni predchoziho stavu

                if((($dov_net_puvodni == "n") and ($this->form_dov_net == 2))) {
                    $this->action_fail = "true";
                    $this->action_error .= "<div class=\"objekty-add-mac\" >Klient má pozastavené fakturace. Před povolením internetu je potřeba změnit u vlastníka pole \"Pozastavené fakturace\". </div>";
                }
            }

        } // konec if jestli id_cloveka > 1 and update == 1

        //checkem jestli se macklo na tlacitko "OK" :)
        if(!preg_match("/^OK$/", $this->odeslano) and !isset($this->action_fail)) {
            $this->action_fail = "true";
            $this->action_error .= "<div class=\"objekty-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>";
        }

        //ulozeni
        if (!(isset($this->action_fail))) {
            // priprava promennych

            if ($this->form_dov_net == 2) {
                $dov_net_w = "a";
            } else {
                $dov_net_w = "n";
            }

            if ($this->form_typ == 3) {
                $dov_net_w = "a";
            }

            if ($this->form_typ_ip == 1) {
                $verejna_w = "99";
            } elseif($this->form_typ_ip == 3) {
                $verejna_w = $vip_rozsah;
                //$vip_snat="1";
            } elseif($this->form_typ_ip == 4) {
                //tunelovane ip adresy
                $tunnelling_ip = 1; //flag pro selekci tunelovanych ip
                $verejna_w = $vip_rozsah; //flag ze je jedna o verejnou (asi jen pro DNS)

                $tunnel_user_w = $tunnel_user;
                $tunnel_pass_w = $tunnel_pass;

            } else {
                //obyc verejka
                $verejna_w = $vip_rozsah;
                $tunnelling_ip = "0";
            }

            if($this->form_sikana_status == "2") {
                $sikana_status_w = 'a';
            } else {
                $sikana_status_w = 'n';
            }

            $this->form_sikana_cas = intval($this->form_sikana_cas);

            if($update_status == 1) {
                // rezim upravy

                if ($this->adminator->checkLevel(29) === false) {
                    $output .= "<br><div style=\"color: red; font-size: 18px; \" >Objekty nelze upravovat, není dostatečné oprávnění. </div><br>";
                    return $output;
                }

                //prvne stavajici data docasne ulozime
                $sql_rows = "id_komplu, dns_jmeno, ip, mac, client_ap_ip, dov_net, id_tarifu, typ, poznamka, verejna, ";
                $sql_rows .= "sikana_status, sikana_cas, sikana_text, upravil, id_nodu, ";
                $sql_rows .= "tunnelling_ip, tunnel_user, tunnel_pass";

                $vysl4 = pg_query($this->conn_pgsql, "SELECT ".$sql_rows." FROM objekty WHERE id_komplu='".intval($this->update_id)."' ");

                if(!$vysl4) {
                    $output .= "<div class=\"alert alert-danger\" role=\"alert\" style=\"padding: 10px; \">Chyba! Nelze zjistit puvodni data pro ulozeni do archivu zmen</div>";
                    $output .= "<div>pg_query failed! detail chyby: " . pg_last_error($this->conn_pgsql) . "</div>";
                    return $output;
                }

                if((pg_num_rows($vysl4) <> 1)) {
                    $output .= "<div class=\"alert alert-danger\" role=\"alert\" style=\"padding: 10px; \">Chyba! Nelze zjistit puvodni data pro ulozeni do archivu zmen</div>";
                    $output .= "<div>detail chyby: num_rows: " . pg_num_rows($vysl4) . "</div>";
                    return $output;
                } else {
                    while ($data4 = pg_fetch_array($vysl4)) {

                        $this->origDataArray["id_komplu"] = $data4["id_komplu"];
                        $this->origDataArray["dns_jmeno"] = $data4["dns_jmeno"];
                        $this->origDataArray["ip"] = $data4["ip"];
                        $this->origDataArray["mac"] = $data4["mac"];
                        $this->origDataArray["client_ap_ip"] = $data4["client_ap_ip"];
                        $this->origDataArray["dov_net"] = $data4["dov_net"];
                        $this->origDataArray["id_tarifu"] = $data4["id_tarifu"];
                        $this->origDataArray["typ"] = $data4["typ"];
                        $this->origDataArray["poznamka"] = $data4["poznamka"];
                        $this->origDataArray["verejna"] = $data4["verejna"];
                        $this->origDataArray["sikana_status"] = $data4["sikana_status"];
                        $this->origDataArray["sikana_cas"] = $data4["sikana_cas"];
                        $this->origDataArray["sikana_text"] = $data4["sikana_text"];
                        $this->origDataArray["upravil"] = trim($data4["upravil"]);
                        $this->origDataArray["id_nodu"] = $data4["id_nodu"];

                        $this->origDataArray["tunnelling_ip"] = $data4["tunnelling_ip"];
                        $this->origDataArray["tunnel_user"] = $data4["tunnel_user"];
                        $this->origDataArray["tunnel_pass"] = $data4["tunnel_pass"];

                    }

                } // konec else if radku <> 1

                $obj_upd = array( "dns_jmeno" => $this->form_dns, "ip" => $this->form_ip,
                    "client_ap_ip" => $this->form_client_ap_ip, "dov_net" => $dov_net_w,"id_tarifu" => $this->form_id_tarifu,
                    "typ" => $this->form_typ, "poznamka" => $this->form_pozn, "verejna" => $verejna_w,
                    "mac" => $this->form_mac, "upravil" => $this->loggedUserEmail, "sikana_status" => $sikana_status_w,
                    "sikana_cas" => $this->form_sikana_cas, "sikana_text" => $this->form_sikana_text, "id_nodu" => $this->form_selected_nod );

                if($this->form_typ_ip == 4) {
                    $obj_upd["tunnelling_ip"] = $tunnelling_ip;

                    $obj_upd["tunnel_user"] = $tunnel_user_w;
                    $obj_upd["tunnel_pass"] = $tunnel_pass_w;
                } else {
                    $obj_upd["tunnelling_ip"] = null;
                }

                $affected = DB::connection('pgsql')
                    ->table('objekty')
                    ->where('id_komplu', $this->update_id)
                    ->update($obj_upd);

                if($affected > 0) {
                    $vysledek_write = 1;
                    $output .= "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3>\n";
                } else {
                    $vysledek_write = 0;
                    $output .= "<br><H3><div style=\"color: red; \">".
                    "Chyba! Data v databázi nelze změnit. </div></h3>\n".pg_last_error($this->conn_pgsql);
                }

                //ted zvlozime do archivu zmen
                $this->updatedDataArray = $obj_upd;
                // require("objekty-add-inc-archiv.php");
                list($az_output) = $this->actionArchivZmenWifiDiff($vysledek_write);

                $output .= $az_output;

                $updated = "true";

            } else {
                // rezim pridani
                $sql_rows = "";
                $sql_values = "";

                $obj_add_i = 1;

                $obj_add = array( "dns_jmeno" => $this->form_dns, "ip" => $this->form_ip, "id_tarifu" => $this->form_id_tarifu, "dov_net" => $dov_net_w,
                    "typ" => $this->form_typ, "poznamka" => $this->form_pozn, "verejna" => $verejna_w, "pridal" => $this->loggedUserEmail, "id_nodu" => $this->form_selected_nod,
                    "sikana_status" => $sikana_status_w, "sikana_cas" => $this->form_sikana_cas, "sikana_text" => $this->form_sikana_text );

                if($this->form_typ_ip == 4) {
                    $obj_add["tunnelling_ip"] = $tunnelling_ip;

                    $obj_add["tunnel_user"] = $tunnel_user_w;
                    $obj_add["tunnel_pass"] = $tunnel_pass_w;

                }

                if((strlen($this->form_client_ap_ip) > 0)) {
                    $obj_add["client_ap_ip"] = $this->form_client_ap_ip;
                }

                if((strlen($this->form_mac) > 0)) {
                    $obj_add["mac"] = $this->form_mac;
                }

                $this->insertedId = DB::connection('pgsql')
                    ->table('objekty')
                    ->insertGetId($obj_add, "id_komplu");

                if($this->insertedId > 0) {
                    $output .= "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze.</div></H3>\n";
                    $vysledek_write = 1;
                } else {
                    $vysledek_write = 0;

                    $output .= "<H3><div style=\"color: red; padding-top: 20px; padding-left: 5px; \">".
                    "Chyba! Data do databáze nelze uložit. </div></H3>\n";

                    $output .= "<div style=\"color: red; padding-bottom: 10px; padding-left: 5px; \" >".
                    pg_last_error($this->conn_pgsql).
                    "</div>";
                }

                // pridame to do archivu zmen
                $this->addedDataArray = $obj_add;
                list($az_output) = $this->actionArchivZmenWifi($vysledek_write);

                $output .= $az_output;

                $writed = "true";
            } // konec else - rezim pridani

        } else {
        } // konec else ( !(isset(fail) ), muji tu musi bejt, pac jinak nefunguje nadrazeny if-elseif

        elseif(isset($this->send)) :
            $this->action_fail = "true";
            $this->p_bs_alerts["Chybí povinné údaje! </br>(aktuálně jsou povinné: dns, ip adresa, přípojný bod, tarif)"] = "danger";
        endif;

        if ($update_status == 1) {
            $output .= '<h3 align="center">Úprava objektu</h3>';
        } else {
            $output .= '<h3 align="center">Přidání nového objektu</h3>';
        }

        // jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
        if((isset($this->action_fail)) or (!isset($this->send))) :
            $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": rendering form");

            $output .= $this->action_error;
            $output .= $this->action_info;

            $output .= $this->actionFormWifi();

            $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": actionFormWifi rendered");
        elseif ((isset($writed) or isset($updated))) :

            $output .= '<table border="0" width="50%" >
                <tr>
                <td align="right">Zpět na objekty </td>
                <td><form action="" method="GET" ><input type="hidden"' . "value=\"".$this->form_dns."\"" . ' name="dns_find" >
                <input type="submit" value="ZDE" name="odeslat" > </form></td>
            </table>';

            $output .= '<br>
            Objekt byl přidán/upraven , zadané údaje:<br><br>
            <b>Dns záznam</b>: ' . $this->form_dns . '<br>
            <b>IP adresa</b>: ' . $this->form_ip . '<br>
            <b>client ap ip </b>: ' . $this->form_client_ap_ip . '<br>'
            . "<br><b>Typ objektu </b>:";

            if ($this->form_typ == 1) {
                $output .= "platiči";
            } elseif ($this->form_typ == 2) {
                $output .= "Free";
            } elseif ($this->form_typ == 3) {
                $output .= "AP";
            } else {
                $output .= "chybný výběr";
            }

        $output .= '<br>
                 <b>Linka</b>: ';

        $vysledek4 = $this->conn_mysql->query("SELECT jmeno_tarifu, zkratka_tarifu FROM tarify_int WHERE id_tarifu='".intval($this->form_id_tarifu)."' ");
        $radku4 = $vysledek4->num_rows;

        if($radku4 == 0) {
            $output .= "Nelze zjistit tarif";
        } else {
            while($zaznam4 = $vysledek4->fetch_array()) {
                $output .= $zaznam4["jmeno_tarifu"]." (".$zaznam4["zkratka_tarifu"].") ";
            }
        }

        $output .= '<br>
            <b>Povolet NET</b>: ';
        if ($this->form_dov_net == 2) {
            $output .= "Ano";
        } else {
            $output .= "Ne";
        }
        $output .= '<br>
            <br>
            <b>MAC </b>: ' . $this->form_mac . '<br>
            <br>
            <b>Poznámka</b>: ' . $this->form_pozn . '<br>
            <b>Přípojný bod</b>:';

        $vysledek3 = $this->conn_mysql->query("SELECT jmeno,id FROM nod_list WHERE id='".intval($this->form_selected_nod)."'");
        $radku3 = $vysledek3->num_rows;

        if($radku3 == 0) {
            $output .= "Nelze zjistit ";
        } else {
            while ($zaznam3 = $vysledek3->fetch_array()) {
                $output .= $zaznam3["jmeno"]." (".$zaznam3["id"].") ".'';
            }
        }

        $output .= "<br><br><b>Šikana: </b>";
        if($this->form_sikana_status == 2) {
            $output .= "Ano";

            $output .= "<br><b>Šikana - počet dní: </b>".$this->form_sikana_cas;
            $output .= "<br><b>Šikana - text: </b>".$this->form_sikana_text;
        } elseif($this->form_sikana_status == 1) {
            $output .= "Ne";
        } else {
            $output .= "Nelze zjistit";
        }

        endif;

        return $output;
    }

    public function actionFiber(): string
    {
        $output = "";

        if (($this->update_id > 0)) {
            $update_status = 1;
        } else {
            $update_status = 0;
        }

        //nacitani predchozich dat ...
        // TODO: add check others reload stuff
        if (($update_status == 1 and !(isset($this->send)))) {
            //rezim upravy,takze nacitame z databaze ...

            $dotaz_upd = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE id_komplu='".intval($this->update_id)."' ");
            $radku_upd = pg_num_rows($dotaz_upd);

            if ($radku_upd == 0) {
                $output .= "Chyba! Požadovaná data nelze načíst! ";
            } else {

                while ($data = pg_fetch_array($dotaz_upd)):

                    // primy promenny
                    $this->form_dns = $data["dns_jmeno"];
                    $this->form_ip = $data["ip"];
                    $this->form_mac = $data["mac"];
                    $this->form_selected_nod = $data["id_nodu"];
                    $this->form_id_tarifu = $data["id_tarifu"];

                    $this->form_typ = $data["typ"];
                    $this->form_typ_ip = $data["typ_ip"];
                    $this->form_port_id = $data["port_id"];

                    $dov_net_l = $data["dov_net"];
                    if ($dov_net_l == "a") {
                        $this->form_dov_net = 2;
                    } else {
                        $this->form_dov_net = 1;
                    }

                    $this->form_pozn = $data["poznamka"];

                    $this->form_sikana_text = $data["sikana_text"];

                    $sikana_status_l = $data["sikana_status"];
                    if (preg_match("/a/", $sikana_status_l)) {
                        $this->form_sikana_status = 2;
                    } else {
                        $this->form_sikana_status = 1;
                    }

                    $sikana_cas_l = $data["sikana_cas"];
                    if (strlen($sikana_cas_l) > 0) {
                        $this->form_sikana_cas = $sikana_cas_l;
                    }

                    $verejna_l = $data["verejna"];

                    if($verejna_l == "99") {
                        $this->form_typ_ip = "1";
                    } else {
                        $this->form_typ_ip = "2";
                        $vip_rozsah = "10.1";
                    }

                    $this->form_another_vlan_id = $data["another_vlan_id"];

                endwhile;

            }
        } else {
            // rezim pridani, nacitame z POSTu

            $this->form_dns = trim($_POST["dns"]);
            $this->form_ip = $_POST["ip"];

            $this->form_typ_ip = $_POST["typ_ip"];
            $this->form_selected_nod = $_POST["selected_nod"];

            $this->form_id_tarifu = $_POST["id_tarifu"];

            $this->form_mac = $_POST["mac"];
            $this->form_typ = $_POST["typ"];
            $this->form_dov_net = $_POST["dov_net"];

            $this->form_pozn = trim($_POST["pozn"]);

            $this->form_sikana_status = $_POST["sikana_status"];
            $this->form_sikana_text = $_POST["sikana_text"];
            $this->form_sikana_cas = $_POST["sikana_cas"];

            $this->form_port_id = $_POST["port_id"];
            $this->form_another_vlan_id = $_POST["another_vlan_id"];

        }

        //co mame: v promeny selected_nod mame id nodu kam se to bude pripojovat
        // co chcete: ip adresu , idealne ze spravnyho rozsahu :)

        $this->generujDataFiber();

        //kontrola vlozenych promennych ..
        if((strlen($this->form_ip) > 0)) {
            $this->checkip($this->form_ip);
        }

        if((strlen($this->form_dns) > 0)) {
            $this->checkdns($this->form_dns);
        }
        if((strlen($this->form_mac) > 0)) {
            $this->checkmac($this->form_mac);
        }

        if((strlen($this->form_sikana_cas) > 0)) {
            $this->checkcislo($this->form_sikana_cas);
        }
        //if( (strlen($this->form_selected_nod) > 0 ) ){ $this->checkcislo($this->form_selected_nod); }

        // checkne se jestli jsou vsechny udaje
        if((($this->form_dns != "") and ($this->form_ip != "")) and ($this->form_selected_nod > 0) and (($this->form_id_tarifu >= 0)) and ($this->form_mac != "")) :

            //kontrola dulplicitnich udaju
            if ($update_status != 1 and isset($this->odeslano)) {
                $ip = $this->form_ip."/32";

                //zjisti jestli neni duplicitni dns, ip
                $MSQ_DNS = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE dns_jmeno = '" . $this->form_dns ."' ");
                $MSQ_IP = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE ip <<= '" . $ip ."' ");

                if (pg_num_rows($MSQ_DNS) <> 0) {
                    $this->action_error .= "<h4>Dns záznam ( ".$this->form_dns." ) již existuje!!!</h4>";
                    $this->action_fail = "true";
                }
                if (pg_num_rows($MSQ_IP) <> 0) {
                    $this->action_error .= "<h4>IP adresa ( ".$this->form_ip." ) již existuje!!!</h4>";
                    $this->action_fail = "true";
                }
            }

            // check v modu uprava
            if(($update_status == 1 and (isset($this->odeslano)))) {
                $ip = $this->form_ip."/32";

                //zjisti jestli neni duplicitni dns, ip
                $MSQ_DNS2 = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE ( dns_jmeno = '$this->form_dns' AND id_komplu != '$this->update_id' ) ");
                $MSQ_IP2 = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE ( ip <<= '$ip' AND id_komplu != '$this->update_id' ) ");

                if(pg_num_rows($MSQ_DNS2) <> 0) {
                    $this->action_error .= "<h4>Dns záznam ( ".$this->form_dns." ) již existuje!!!</h4>";
                    $this->action_fail = "true";
                }
                if(pg_num_rows($MSQ_IP2) <> 0) {
                    $this->action_error .= "<h4>IP adresa ( ".$this->form_ip." ) již existuje!!!</h4>";
                    $this->action_fail = "true";
                }
            }

            // checknem stav vysilace a filtraci
            $msq_stav_nodu = $this->conn_mysql->query("SELECT * FROM nod_list WHERE id= '$this->form_selected_nod' ");
            $msq_stav_nodu_radky = $msq_stav_nodu->num_rows;

            while ($data = $msq_stav_nodu->fetch_array()) {
                $stav_nodu = $data["stav"];
                $router_id = $data["router_id"];
            }

            if ($stav_nodu == 2) {
                $this->action_info .= "<div style=\"color: orange; \" ><h4>UPOZORNĚNÍ: Tento přípojný bod je vytížen, mohou se vyskytovat problémy. </h4></div>";
            } elseif (($stav_nodu == 3) and ($update_status == 1)) {
                $this->action_info .= "<div style=\"color: orange; \"><h4>UPOZORNĚNÍ: Tento přípojný bod je přetížen. </h4></div>";
            } elseif ($stav_nodu == 3) {
                $this->action_fail = "true";
                $this->p_bs_alerts["<div style=\"color: red; \" ><h4>Tento přípojný bod je přetížen, vyberte prosím jiný."] = "danger";
            }

        // kontrola jestli se muze povolit inet / jestli jsou pozatavené fakturace
        $poz_fakt_clovek = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE id_komplu = '$this->update_id' ");
        $poz_fakt_clovek_radku = pg_num_rows($poz_fakt_clovek);

        while($data_poz_f_clovek = pg_fetch_array($poz_fakt_clovek)) {
            $id_cloveka = $data_poz_f_clovek["id_cloveka"];
            $dov_net_puvodni = $data_poz_f_clovek["dov_net"];
        }

        if ((($id_cloveka > 1) and ($update_status == 1))) {

            $pozastavene_fakt = pg_query($this->conn_pgsql, "SELECT billing_suspend_status FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka)."' ");
            $pozastavene_fakt_radku = pg_num_rows($pozastavene_fakt);


            if($pozastavene_fakt_radku == 1) {
                while ($data_poz_fakt = pg_fetch_array($pozastavene_fakt)) {
                    $billing_suspend_status = intval($data_poz_fakt["billing_suspend_status"]);
                }
            } else {
                $output .= "Chyba! nelze vybrat vlastníka.";
            }

            if($billing_suspend_status == 1) {
                // budeme zli
                // prvne zjisteni predchoziho stavu

                if((($dov_net_puvodni == "n") and ($this->form_dov_net == 2))) {
                    $this->action_fail = "true";
                    $this->action_error .= "<div class=\"objekty-add-mac\" >Klient má pozastavené fakturace. Před povolením internetu je potřeba změnit u vlastníka fakturační skupinu. </div>";
                }

            }

        } // konec if jestli id_cloveka > 1 and update == 1

        //checkem jestli se macklo na tlacitko "OK" :)
        if(!preg_match("/^OK*/", $this->odeslano) and !isset($this->action_fail)) {
            $this->action_fail = "true";
            $this->action_error .= "<div class=\"objekty-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>";
        }

        //ukladani udaju ...
        if(!(isset($this->action_fail))) {
            // priprava promennych

            if($this->form_dov_net == 2) {
                $dov_net_w = "a";
            } else {
                $dov_net_w = "n";
            }
            if($this->form_sikana_status == "2") {
                $sikana_status_w = 'a';
            } else {
                $sikana_status_w = 'n';
            }

            if ($this->form_typ_ip == 1) {
                $verejna_w = "99";
                //$vip_snat="0";
            } else {
                $verejna_w = "1";
                //$vip_snat="0";
            }

            if($this->form_another_vlan_id == 0) {
                $this->form_another_vlan_id = null;
            }

            if($update_status == 1) {

                if ($this->adminator->checkLevel(29) === false) {
                    $output .= "<br><div style=\"color: red; font-size: 18px; \" >Objekty nelze upravovat, není dostatečné oprávnění. </div><br>";
                    return $output;
                }

                // rezim upravy

                //prvne stavajici data docasne ulozime
                $vysl4 = pg_query($this->conn_pgsql, "select * from objekty WHERE id_komplu='$this->update_id' ");

                if((pg_num_rows($vysl4) <> 1)) {
                    $output .= "<p>Chyba! Nelze zjistit puvodni data pro ulozeni do archivu </p>";
                } else {
                    while ($data4 = pg_fetch_array($vysl4)):

                        $pole_puvodni_data["id_komplu"] = $data4["id_komplu"];

                        $pole_puvodni_data["dns_jmeno"] = $data4["dns_jmeno"];
                        $pole_puvodni_data["ip"] = $data4["ip"];

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

                        if($data4["verejna"] == 99) {
                            $pole_puvodni_data["typ_ip"] = "1";
                        } else {
                            $pole_puvodni_data["typ_ip"] = "2";
                        }

                    endwhile;

                } // konec else if radku <> 1

                $obj_upd = array( "dns_jmeno" => $this->form_dns, "ip" => $this->form_ip, "id_tarifu" => $this->form_id_tarifu,
                    "dov_net" => $dov_net_w, "typ" => $this->form_typ, "poznamka" => $this->form_pozn, "mac" => $this->form_mac,
                    "upravil" => $this->loggedUserEmail , "id_nodu" => $this->form_selected_nod, "sikana_status" => $sikana_status_w,
                    "sikana_cas" => $this->form_sikana_cas, "sikana_text" => $this->form_sikana_text, "port_id" => $this->form_port_id,
                    "verejna" => $verejna_w, "another_vlan_id" => $this->form_another_vlan_id );

                $obj_id = array( "id_komplu" => $this->update_id );
                $res = pg_update($this->conn_pgsql, 'objekty', $obj_upd, $obj_id);

                if($res) {
                    $vysledek_write = 1;
                    $output .= "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3>\n";
                } else {
                    $vysledek_write = 0;
                    $output .= "<br><H3><div style=\"color: red; \">Chyba! Data v databázi nelze změnit. </div></h3>\n".pg_last_error($this->conn_pgsql);
                }

                //ted zvlozime do archivu zmen

                //workaround
                $obj_upd["typ_ip"] = $this->form_typ_ip;

                $this->origDataArray = $pole_puvodni_data;
                $this->updatedDataArray = $obj_upd;
                // require("objekty-add-inc-archiv-fiber.php");
                $this->actionArchivZmenFiberDiff($vysledek_write);

                $updated = "true";
            } else {
                // rezim pridani
                $obj_add = array( "dns_jmeno" => $this->form_dns, "ip" => $this->form_ip, "id_tarifu" => $this->form_id_tarifu,
                    "dov_net" => $dov_net_w, "typ" => $this->form_typ, "poznamka" => $this->form_pozn, "mac" => $this->form_mac,
                    "pridal" => $this->loggedUserEmail , "id_nodu" => $this->form_selected_nod, "sikana_status" => $sikana_status_w,
                    "sikana_cas" => intval($this->form_sikana_cas), "sikana_text" => $this->form_sikana_text, "port_id" => $this->form_port_id,
                    "verejna" => $verejna_w, "another_vlan_id" => $this->form_another_vlan_id );

                $this->insertedId = DB::connection('pgsql')
                    ->table('objekty')
                    ->insertGetId($obj_add, "id_komplu");

                //zjistit, krz kterého reinharda jde objekt
                // $inserted_id = \Aglobal::pg_last_inserted_id($this->conn_pgsql, "objekty");

                if ($this->insertedId > 0) {
                    $vysledek_write = 1;
                    $output .= "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze.</div></H3>\n";
                } else {
                    $vysledek_write = 0;

                    $output .= "<H3><div style=\"color: red; padding-top: 20px; padding-left: 5px; \">".
                    "Chyba! Data do databáze nelze uložit. </div></H3>\n";

                    $output .= "<div style=\"color: red; padding-bottom: 10px; padding-left: 5px; \" >".
                    pg_last_error($this->conn_pgsql).
                    "</div>";

                    $output .= "<div style=\"padding-left: 5px; color: grey;\" >obj_add var dump: " . var_export($obj_add, true) ."</div>";
                }

                // pridame to do archivu zmen
                $this->addedDataArray = $obj_add;
                $this->actionArchivZmenFiberAdd($vysledek_write);

                $writed = "true";
                // konec else - rezim pridani
            }

        } else {
        } // konec else ( !(isset(fail) ), muji tu musi bejt, pac jinak nefunguje nadrazeny if-elseif

        elseif(isset($this->send)) :
            $this->action_fail = "true";
            $this->p_bs_alerts["Chybí povinné údaje! </br>(aktuálně jsou povinné: dns, ip adresa, mac adresa, přípojný bod, tarif)"] = "danger";
        endif;

        if ($update_status == 1) {
            $output .= '<h3 align="center">Úprava objektu</h3>';
        } else {
            $output .= '<h3 align="center">Přidání nového objektu</h3>';
        }

        // jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
        if((isset($this->action_fail)) or (!isset($this->send))) :
            $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": rendering form");

            $output .= $this->action_error;

            $output .= $this->action_info;

            // vlozeni vlastniho formu
            $output .= $this->actionFormFiber();

            $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": formFiber rendered");

        elseif ((isset($writed) or isset($updated))) :

            $output .= '<table border="0" width="50%" >
                <tr>
                <td align="right">Zpět na objekty </td>
                <td><form action="/objekty" method="GET" >
                <input type="hidden" ' . "value=\"".$this->form_dns."\"" . ' name="dns_find" >

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
            <b>Dns záznam</b>: ' .  $this->form_dns . '<br>
            <b>IP adresa</b>: ' . $this->form_ip . '<br>
            <b>MAC adresa:</b>' . $this->form_mac . '<br>';

            $output .= "<br><b>Typ objektu </b>:";

            if ($this->form_typ == 1) {
                $output .= "platiči";
            } elseif ($this->form_typ == 2) {
                $output .= "Free";
            } elseif ($this->form_typ == 3) {
                $output .= "AP";
            } else {
                $output .= "chybný výběr";
            }

        $output .= '<br>

            <b>Linka</b>:';

        $output .= "id tarifu: ".$this->form_id_tarifu;
        //if ( $tarif == 2 ) { $output .= "Metropolitní"; } else { $output .= "Small city"; }

        $output .= '<br>
            <b>Povolet NET</b>: ';
        if ($this->form_dov_net == 2) {
            $output .= "Ano";
        } else {
            $output .= "Ne";
        } $output .= '<br>
            <br>
            <b>Poznámka</b>: ' . $this->form_pozn . '<br>
            <b>Přípojný bod</b>:';

        $vysledek3 = $this->conn_mysql->query("select * from nod_list WHERE id=".intval($this->form_selected_nod));
        $radku3 = $vysledek3->num_rows;
        if($radku3 == 0) {
            $output .= "Nelze zjistit ";
        } else {
            while ($zaznam3 = $vysledek3->fetch_array()) {
                $output .= $zaznam3["jmeno"]." (".$zaznam3["id"].") ".'';
            }
        }

        // $output .= "data nejak upravena";

        $output .= "<br><br><b>Šikana: </b>";
        if($this->form_sikana_status == 2) {
            $output .= "Ano";

            $output .= "<br><b>Šikana - počet dní: </b>".$this->form_sikana_cas;
            $output .= "<br><b>Šikana - text: </b>".$this->form_sikana_text;
        } elseif($this->form_sikana_status == 1) {
            $output .= "Ne";
        } else {
            $output .= "Nelze zjistit";
        }

        $output .= "<br><b>Číslo portu (ve switchi)</b>: ".$this->form_port_id."<br>";

        $output .= "<br><b>Typ IP adresy</b>: ";
        if($this->form_typ_ip == "2") {
            $output .= "Veřejná";
        } elseif($this->form_typ_ip == "1") {
            $output .= "Neveřejná";
        } else {
            $output .= "Nelze zjistit";
        }

        $output .= "<br><b>Přílušnost MAC k jiné vlaně (ve domov. switchi)</b>: ";
        if(($this->form_another_vlan_id == null) or ($this->form_another_vlan_id == "")) {
            $output .= "Vypnuto";
        } else {
            $output .= "vlan id: ".$this->form_another_vlan_id;
        }

        $output .= "<br>";

        endif;

        return $output;
    }

    private function actionFormWifi()
    {
        $output = "";

        $output .= '
            <form name="form1" method="post" action="" >
            <input type="hidden" name="send" value="true" >
            <input type="hidden" name="update_id" value="'.intval($this->update_id).'" >';
        $output .= $this->csrf_html[0];

        $output .= '<table border="0" width="100%" >

            <tr>
            <td><span style="font-weight: bold; font-size: 18px; color: teal;" >Mód:</span></td>
            <td >
            <select size="1" name="mod_objektu" onChange="self.document.forms.form1.submit()" >
                <option value="1" style="color: #CC0033;" ';
        if($this->mod_objektu == 1) {
            $output .= " selected ";
        } $output .= ' >Bezdrátová síť</option>
                <option value="2" style="color: #e37d2b; font-weight: bold;" ';
        if($this->mod_objektu == 2) {
            $output .= " selected ";
        } $output .= ' >Optická síť</option>
            </select>
            </td>
            </tr>

            <tr><td colspan="4" ><br></td></td>

            <tr>
            <td width="170px" >dns záznam:</td>
            <td width="380px" ><input type="Text" name="dns" size="30" maxlength="50" value="'.$this->form_dns.'" ></td>

            <td width="" >Přípojný bod - hledání:</td>
            <td width="" ><input type="Text" name="nod_find" size="30" value="'.$this->nod_find.'" ></td>

            </tr>

            <tr><td colspan="4" ><br></td></td>

            </tr>

            <tr>
            <td>typ ip adresy:</td>
            <td width="" >
                    <table border="0">
                <tr>
                <td>
                <input type="radio" name="typ_ip" onChange="self.document.forms.form1.submit()" value="1" ';
        if (($this->form_typ_ip == 1 or (!isset($this->form_typ_ip)))) {
            $output .= " checked ";
        } $output .= ' >
                <label>Neveřejná </label>';

        // <!--
        // <input type="radio" name="typ_ip" onchange="self.document.forms.form1.submit()" value="2"
        // <?php if($this->form_typ_ip==2 ) { $output .= " checked "; } >
        // -->

        $output .= '<span style="padding-left: 5px; padding-right: 5px;"> | </span>
                <span style="padding-right: 10px;">Veřejná </span>
                </td>
                <td>
                <select size="1" name="typ_ip" onchange="self.document.forms.form1.submit()" >';
        $output .= '<option value="1" class="select-nevybrano" ';
        if($this->form_typ_ip == 1) {
            $output .= " selected ";
        } $output .= ' >vyberte typ</option>
                <option value="2" ';
        if($this->form_typ_ip == 2) {
            $output .= " selected ";
        } $output .= ' >default - routovaná</option>';

        if(($this->update_id > 0) and ($this->form_typ_ip == 3)) {
            $output .= "<option value=\"3\"";
            if($this->form_typ_ip == 3) {
                $output .= " selected ";
            }
            $output .= " >překládaná - snat/dnat</option> ";
        }

        $output .= '
                <option value="4" ';
        if($this->form_typ_ip == 4) {
            $output .= " selected ";
        } $output .= ' >tunelovaná - l2tp tunel</option>
                </select>
                </td>
                </tr>
                </table>

                <input type="hidden" name="vip_rozsah" value="1" >

            </td>

            <td><label> Přípojný bod: </label></td>
            <td>';

        $this->sql_nod = "SELECT * from nod_list WHERE ( jmeno LIKE '%$this->nod_find%' ";
        $this->sql_nod .= " OR ip_rozsah LIKE '%$this->nod_find%' OR adresa LIKE '%$this->nod_find%' ";
        $this->sql_nod .= " OR pozn LIKE '%$this->nod_find%' ) AND ( typ_nodu = '1' ) ORDER BY jmeno ASC ";

        $vysledek = $this->conn_mysql->query($this->sql_nod);
        $radku = $vysledek->num_rows;

        $output .= '<select size="1" name="selected_nod" onChange="self.document.forms.form1.submit()" >';

        if($this->form_typ_ip == 4) {
            $output .= "<option value=\"572\" selected > verejne_ip_tunelovane ( 212.80.82.160 ) </option>";
        } elseif(($radku == 0)) {
            $output .= "<option value=\"0\" style=\"color: gray; \" selected >nelze zjistit / žádný nod nenalezen </option>";
        } else {
            $output .= '<option value="0" style="color: gray; font-style: bold; " ';
            if(($_POST["selected"] == 0) or ((!isset($this->form_selected_nod)))) {
                $output .= "selected ";
            }
            $output .= ' > Není vybráno</option>';

            while ($zaznam2 = $vysledek->fetch_array()) {
                $output .= '<option value="'.$zaznam2["id"].'"';
                if (($this->form_selected_nod == $zaznam2["id"])) {
                    $output .= " selected ";
                }
                $output .= '>'." ".$zaznam2["jmeno"]." ( ".$zaznam2["ip_rozsah"]." )".'</option>'." \n";
            } //konec while
        } //konec else

        $output .= '</select>';

        $output .= '<input type="button" value="Generovat / hledat (nody)" name="G" onClick="self.document.forms.form1.submit()" >
                    </td>

            </tr>

            <tr><td colspan="4" ><br></td></tr>

            <tr>
                <td>ip adresa:</td>
                <td><input type="Text" name="ip" size="30" maxlength="20" value="'.$this->form_ip.'" >';
        if($this->form_ip_error == 1) {
            $output .= "<img title=\"error\" width=\"20px\" src=\"img2/warning.gif\" align=\"middle\" ";
            $output .= "onclick=\" window.open('objekty-vypis-ip.php?id_rozsah=".$ip_rozsah."'); "."\">";
        }

        $output .= '</td>
                <td>';

        if($this->form_typ_ip == 3) {
            $output .= "<label> Lokální adresa k veřejné: </label>";
        } elseif($this->form_typ_ip == 4) {
            $output .= "Přihlašovací údaje
                        <span style=\"font-size: 11px;\">(k tunelovacímu serveru): </span>";
        } else {
            $output .= "<span style=\"color: gray; \" >Není dostupné </span>";
        }

        $output .= '
                </td>
                <td>';

        /*
        if ( $this->form_typ_ip == 3)
        {
        $vysledek2=pg_query($this->conn_pgsql, "select * from objekty where typ != 3 AND verejna=99 ORDER BY dns_jmeno ASC" );
                $radku2=pg_num_rows($vysledek2);

                if ($radku==0) { $output .= "žádné objekty v databázi "; }
                else
                {
                $output .= '<select size="1" name="vip_snat_lip" onChange="self.document.forms.form1.submit()" >';
                $output .= '<option value="0" style="color: gray; font-style: bold; "';

                if ( ( $_POST["vip_snat_lip"] == 0 ) or ( (!isset($vip_snat_lip)) ) ) { $output .= "selected"; }
                $output .= ' > Není vybráno</option>';

                while ($zaznam3=pg_fetch_array($vysledek2) ):

                    $output .= '<option value="'.$zaznam3["ip"].'"';
                    if( ( $vip_snat_lip == $zaznam3["ip"]) ){ $output .= " selected "; }
                    $output .= '>'." ".$zaznam3["dns_jmeno"]." ( ".$zaznam3["ip"]." )".'</option>'." \n";

                endwhile;

        }
        }
        else
        */

        if($this->form_typ_ip == 4) {
            $output .= "<span style=\"padding-right: 10px; padding-left: 5px;\">login:</span>".
                "<input type=\"text\" name=\"tunnel_user\" size=\"6\" maxlength=\"4\" value=\"".$tunnel_user."\" >".

            "<span style=\"padding-left: 10px; padding-right: 5px\">heslo: </span>".

            "<input type=\"text\" name=\"tunnel_pass\" size=\"6\" maxlength=\"4\" value=\"".$tunnel_pass."\" >";

        } else {
            $output .= "<span style=\"color: gray; \" >Není dostupné </span>";
        }

        $output .= '
                </td>
            </tr>

            <tr><td colspan="4" ><br></td></tr>

            <tr>
                <td>mac adresa: <div style="font-size: 12px;">(prouze pro DHCP server/y)</div></td>
                <td>';

        if($this->form_typ_ip == 4) {
            $output .= "<span style=\"color: gray; \" >Není dostupné </span>";
        } else {
            $output .= "<input type=\"text\" name=\"mac\" maxlength=\"17\" value=\"".$this->form_mac."\">";
        }

        $output .= '
                </td>

                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>

            <tr><td colspan="4"><br></td></tr>

            <tr>
            <td>ip klientského zařízení: </td>
            <td>';
        if(($this->form_typ_ip <> 3) and ($this->form_typ_ip != 4)) {
            $output .= "<input type=\"text\" name=\"client_ap_ip\" value=\"".$this->form_client_ap_ip."\" > ";
        } else {
            $output .= "<span style=\"color: gray; \">není dostupné</span>";
        }
        $output .= '
            </td>
                <td>Povolen NET:</td>
                <td>';

        if(($this->form_typ == 3) or ($this->form_typ_ip == 3)) {
            if($this->form_typ_ip == 3) {
                $output .= "<input type=\"hidden\" name=\"dov_net\" value=\"2\" >";
            }
            $output .= "<div class=\"objekty-not-allow\">není dostupné</div>";
        } else {
            $output .= "<input type=\"radio\" name=\"dov_net\" value=\"2\"";
            if (($this->form_dov_net == 2 or (!isset($this->form_dov_net)))) {
                $output .= "checked";
            } $output .= ">";
            $output .= "<label>Ano | </label>";

            $output .= "<input type=\"radio\" name=\"dov_net\" value=\"1\"";
            if ($this->form_dov_net == 1) {
                $output .= "checked";
            } $output .= ">";
            $output .= "<label> Ne</label>";

        }
        $output .= "</td>";

        $output .= '
            </tr>
            <tr><td colspan="4" ><br></td></tr>

            <tr>
            <td>Typ:</td>
            <td>

            <select name="typ" onChange="self.document.forms.form1.submit()" >
                    <option value="1" ';
        if ($this->form_typ == 1) {
            $output .= " selected ";
        } $output .= ' >poc (platici)</option>
                    <option value="2" ';
        if ($this->form_typ == 2) {
            $output .= " selected ";
        } $output .= ' >poc (free)</option>
                    <option value="3" ';
        if ($this->form_typ == 3) {
            $output .= " selected ";
        } $output .= ' >AP</option>
            </select>

            </td>
            <td>Šikana: </td>
            <td>';

        if ($this->form_typ == 3 or $this->form_typ_ip == 3) {
            $output .= "<div class=\"objekty-not-allow\">není dostupné</div>";
        } else {
            $output .= "<select name=\"sikana_status\" size=\"1\" onChange=\"self.document.forms.form1.submit()\"> \n";
            $output .= "<option value=\"1\" ";
            if (($this->form_sikana_status == 1 or (!isset($this->form_sikana_status)))) {
                $output .= " selected ";
            } $output .= ">Ne</option> \n";
            $output .= "<option value=\"2\" ";
            if ($this->form_sikana_status == 2) {
                $output .= " selected ";
            } $output .= ">Ano</option> \n";
            $output .= "</select>";
        }

        $output .= '
                </td>
                </tr>

                <tr><td colspan="4" ><br></td></tr>

                <tr>
                <td style="" >Tarif:</td>
                <td>';

        if(!isset($this->form_id_tarifu)) {
            if($this->form_typ == 3) {
                $find_tarif = "2";
            } //ap-cko ...
            elseif($this->form_typ_ip == 3) { //snat/dnat verejka ...
                $find_tarif = "2";
            } elseif($garant == 2) { //garant linka ...
            } //.
            elseif($tarif == 1) {  // asi SmallCity
                $find_tarif = "1";
            } elseif($tarif == 2) {  // Mp linka
                $find_tarif = "0";
            } else {
                $find_tarif = "0";
            }
        }

        $output .= "<select name=\"id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";

        //$output .= "<option value=\"\" class=\"select-nevybrano\" >Nevybráno</option>";
        $dotaz_t2 = $this->conn_mysql->query("SELECT * FROM tarify_int WHERE typ_tarifu = '0' ORDER BY zkratka_tarifu ");

        while($data_t2 = $dotaz_t2->fetch_array()) {
            $output .= "<option value=\"".$data_t2["id_tarifu"]."\" ";

            if(isset($find_tarif)) {
                if(($find_tarif == $data_t2["id_tarifu"])) {
                    $output .= " SELECTED ";
                }
            } else {
                if($this->form_id_tarifu == $data_t2["id_tarifu"]) {
                    $output .= " SELECTED ";
                }
            }

            $output .= " >".$data_t2["zkratka_tarifu"];
            $output .= " (".$data_t2["jmeno_tarifu"]." :: ".$data_t2["speed_dwn"]."/".$data_t2["speed_upl"]." )</option> \n";
        }

        $output .= "</select>";

        $output .= '</td>';

        $output .= "<td>Šikana - počet dní: </td>
                <td>";

        if(($this->form_typ == 3 or ($this->form_sikana_status != 2))) {
            $output .= "<div class=\"objekty-not-allow\" >není dostupné</div>";
            $output .= "<input type=\"hidden\" name=\"sikana_cas\" value=\"".$this->form_sikana_cas."\">";
        } else {
            $output .= "<input type=\"text\" name=\"sikana_cas\" size=\"5\" value=\"".$this->form_sikana_cas."\" >";
        }

        $output .= '
            </td>
            </tr>

                <tr><td colspan="4" ><br></td></tr>

                <tr>
                    <td><label> poznámka:  </label></td>
                    <td>
                        <textarea name="pozn" cols="30" rows="6" wrap="soft">' . $this->form_pozn . '</textarea>
                    </td>

                    <td><label>Šikana - text: </label></td>
                    <td>';

        if(($this->form_typ == 3 or ($this->form_sikana_status != 2))) {
            $output .= "<div class=\"objekty-not-allow\" >není dostupné</div>";
            $output .= "<input type=\"hidden\" name=\"sikana_text\" value=\"".$this->form_sikana_text."\" >";
        } else {
            $output .= "<textarea name=\"sikana_text\" cols=\"30\" rows=\"4\" wrap=\"soft\" >".$this->form_sikana_text."</textarea>";
        }

        $output .= '
                    </td>
                </tr>

                <tr><td colspan="4" ><br></td></tr>

                <tr>
                    <td colspan="2" align="center">
                    <hr>
                    <input name="odeslano" type="submit" value="OK">
                    </td>
                    <td colspan="2" >
                    <br>
                    </td>
                </tr>

                </table>
                </form>';

        return $output;
    }

    public function actionFormFiber()
    {
        $output = "";

        $output .= '
            <form name="form1" method="post" action="" >
            <input type="hidden" name="send" value="true" >
            <input type="hidden" name="update_id" value="'.$this->update_id.'" >';

        $output .= $this->csrf_html[0];

        $output .= '<table border="0" width="100%" cellspacing="5" >

                <tr>
                <td><span style="font-weight: bold; font-size: 18px; color: teal;" >Mód:</span></td>
                <td >
                <select size="1" name="mod_objektu" onChange="self.document.forms.form1.submit()" >
                    <option value="1" style="color: #CC0033;" ';
        if($this->mod_objektu == 1) {
            $output .= " selected ";
        } $output .= ' >Bezdrátová síť</option>
                    <option value="2" style="color: #e37d2b; font-weight: bold;" ';
        if($this->mod_objektu == 2) {
            $output .= " selected ";
        } $output .= ' >Optická síť</option>
                </select>
                </td>
                </tr>

                <tr><td colspan="4" ><br></td></td>

                <tr>
                <td width="20%" >dns záznam:</td>
                <td width="" ><input type="Text" name="dns" size="30" maxlength="50" value="'.$this->form_dns.'" ></td>

                <td width="" >Přípojný bod - hledání:</td>
                <td width="" ><input type="Text" name="nod_find" size="30" value="'.$this->nod_find.'" ></td>

                </tr>

                <tr><td colspan="4" ><br></td></tr>

                <tr>
                <td>typ ip adresy:</td>
                <td>
                    <input type="radio" name="typ_ip" onChange="self.document.forms.form1.submit()" value="1" ';
        if (($this->form_typ_ip == 1 or (!isset($this->form_typ_ip)))) {
            $output .= "checked";
        } $output .= ' >
                    <label>Neveřejná | </label>

                    <input type="radio" name="typ_ip" onchange="self.document.forms.form1.submit()" value="2" ';
        if ($this->form_typ_ip == 2) {
            $output .= " checked ";
        } $output .= ' >
                    <label>Veřejná </label>
                </td>

                <td><label> Přípojný bod: </label></td>
                    <td>';

        $this->sql_nod = "SELECT * from nod_list WHERE ( jmeno LIKE '%$this->nod_find%' ";
        $this->sql_nod .= " OR ip_rozsah LIKE '%$this->nod_find%' OR adresa LIKE '%$this->nod_find%' ";
        $this->sql_nod .= " OR pozn LIKE '%$this->nod_find%' ) AND ( typ_nodu = '2' ) ORDER BY jmeno ASC ";

        $vysledek = $this->conn_mysql->query($this->sql_nod);
        $radku = $vysledek->num_rows;

        $output .= '<select size="1" name="selected_nod" onChange="self.document.forms.form1.submit()" >';

        if(($radku == 0)) {
            $output .= "<option value=\"0\" style=\"color: gray; \" selected >nelze zjistit / žádný nod nenalezen </option>";
        } else {
            $output .= '<option value="0" style="color: gray; font-style: bold; "';
            if((!isset($this->form_selected_nod))) {
                $output .= "selected";
            }
            $output .= ' > Není vybráno</option>';

            while ($zaznam2 = $vysledek->fetch_array()) {
                $output .= '<option value="'.$zaznam2["id"].'"';
                if (($this->form_selected_nod == $zaznam2["id"])) {
                    $output .= " selected ";
                }
                $output .= '>'." ".$zaznam2["jmeno"]." ( ".$zaznam2["ip_rozsah"]." )".'</option>'." \n";
            } //konec while
        } //konec else

        $output .= '</select>';


        $output .= '<input type="button" value="Hledat (nody)" name="G" onClick="self.document.forms.form1.submit()" >
                    </td>
                </tr>

                <tr><td colspan="4" ><br></td></tr>

                <tr>
                <td>ip adresa:</td>
                <td><input type="Text" name="ip" size="30" maxlength="20"  value="'.$this->form_ip.'" >';

        if ($this->form_ip_error == 1) {
            $output .= "<img title=\"error\" width=\"20px\" src=\"img2/warning.gif\" align=\"middle\" ";
            $output .= "onclick=\" window.open('objekty-vypis-ip.php?id_rozsah=".$ip_rozsah."'); "."\">";
        }
        $output .= '
                    </td>
                    <td>Linka: </td>

                    <td>';

        if(!isset($this->form_id_tarifu)) {
            $this->form_id_tarifu = "0";
        }

        $output .= "<select name=\"id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";

        //$output .= "<option value=\"\" class=\"select-nevybrano\" >Nevybráno</option>";

        $dotaz_t2 = $this->conn_mysql->query("SELECT * FROM tarify_int WHERE typ_tarifu = '1' ORDER BY gen_poradi ");

        while($data_t2 = $dotaz_t2->fetch_array()) {
            $output .= "<option value=\"".$data_t2["id_tarifu"]."\" ";

            if(isset($find_tarif)) {
                if(($find_tarif == $data_t2["id_tarifu"])) {
                    $output .= " SELECTED ";
                }
            } else {
                if($this->form_id_tarifu == $data_t2["id_tarifu"]) {
                    $output .= " SELECTED ";
                }
            }

            $output .= " >".$data_t2["zkratka_tarifu"];
            $output .= " (".$data_t2["jmeno_tarifu"]." :: ".$data_t2["speed_dwn"]."/".$data_t2["speed_upl"]." )</option> \n";
        }

        $output .= "</select>";
        $output .= "</td>";


        $output .= '
                </td>
                </tr>';

        $output .= '<tr><td colspan="4" ><br></td></tr>

                <tr>
                <td>mac adresa:</td>
                <td><input type="text" name="mac" size="30" "value="'.$this->form_mac.'" ></td>
                <td colspan="2" align="center" >
                <input type="button" value="Generovat údaje ...." name="G"
                    style="width: 300px; background-color: red; color: white; " onClick="self.document.forms.form1.submit()" >
                </td></tr>

                <tr><td colspan="4" ><br></td></tr>

                <tr>

                <td>Typ:</td>
                <td>

                <select name="typ" onChange="self.document.forms.form1.submit()" >
                        <option value="1" ';
        if ($this->form_typ == 1) {
            $output .= " selected ";
        } $output .= ' >poc (platici)</option>
                        <option value="2" ';
        if ($this->form_typ == 2) {
            $output .= " selected ";
        } $output .= ' >poc (free)</option>
                </select>

                </td>

                <td>Povolen NET:</td>
                <td>';

        if(($this->form_typ == 3) or ($this->form_typ_ip == 3)) {
            if($this->form_typ_ip == 3) {
                $output .= "<input type=\"hidden\" name=\"dov_net\" value=\"2\" >";
            }
            $output .= "<div class=\"objekty-not-allow\">není dostupné</div>";
        } else {

            $output .= "<input type=\"radio\" name=\"dov_net\" value=\"2\"";
            if (($this->form_dov_net == 2 or (!isset($this->form_dov_net)))) {
                $output .= "checked";
            } $output .= ">";
            $output .= "<label>Ano | </label>";

            $output .= "<input type=\"radio\" name=\"dov_net\" value=\"1\"";
            if ($this->form_dov_net == 1) {
                $output .= "checked";
            } $output .= ">";
            $output .= "<label> Ne</label>";

        }
        $output .= "</td>";

        $output .= '
                </tr>

                <tr><td colspan="4" ><br></td></tr>

            <tr>

                    <td>Číslo portu (ve switchi): </td>
                <td>
                <select name="port_id" onChange="self.document.forms.form1.submit()" >';

        $pocet_portu = 24;

        for($i = 1;$i <= $pocet_portu;$i++) {
            $output .= "<option value=\"".$i."\" ";

            if($this->form_port_id == $i) {
                $output .= " selected ";
            }

            $output .= " >".$i."</option>";
        }

        $output .= '
                </select>
                </td>

                <td>Šikana:</td>
                <td>';

        if ($this->form_typ == 3 or $this->form_typ_ip == 3) {
            $output .= "<div class=\"objekty-not-allow\">není dostupné</div>";
        } else {
            $output .= "<select name=\"sikana_status\" size=\"1\" onChange=\"self.document.forms.form1.submit()\"> \n";
            $output .= "<option value=\"1\" ";
            if (($this->form_sikana_status == 1 or (!isset($this->form_sikana_status)))) {
                $output .= " selected ";
            } $output .= ">Ne</option> \n";
            $output .= "<option value=\"2\" ";
            if ($this->form_sikana_status == 2) {
                $output .= " selected ";
            } $output .= ">Ano</option> \n";
            $output .= "</select>";
        }

        $output .= "</td>";

        $output .= '
                </tr>

                <tr><td colspan="4" ><br></td></tr>

                <tr>

                <td> </td>
                <td> </td>';

        $output .= "<td>Šikana - počet dní: </td><td>";

        if(($this->form_typ == 3 or ($this->form_sikana_status != 2))) {
            $output .= "<div class=\"objekty-not-allow\" >není dostupné</div>";
            $output .= "<input type=\"hidden\" name=\"sikana_cas\" value=\"".$this->form_sikana_cas."\">";
        } else {
            $output .= "<input type=\"text\" name=\"sikana_cas\" size=\"5\" value=\"".$this->form_sikana_cas."\" >";
        }

        $output .= '
                </td>
            </tr>

            <tr><td colspan="4" ><br></td></tr>

            <tr>
                <td><label> poznámka:  </label></td>
                <td>
                    <textarea name="pozn" cols="30" rows="6" wrap="soft" >' . $this->form_pozn . '</textarea>
                </td>

                <td><label>Šikana - text: </label></td>
                <td>';

        if(($this->form_typ == 3 or ($this->form_sikana_status != 2))) {
            $output .= "<div class=\"objekty-not-allow\" >není dostupné</div>";
            $output .= "<input type=\"hidden\" name=\"sikana_text\" value=\"".$this->form_sikana_text."\" >";
        } else {
            $output .= "<textarea name=\"sikana_text\" cols=\"30\" rows=\"4\" wrap=\"soft\" >".$this->form_sikana_text."</textarea>";
        }

        $output .= '
                </td>
            </tr>

            <tr><td colspan="4" ><br></td></tr>

            <tr>
                <td><label> příslušnost MAC do jiné vlany <br>(v domovním switchi):  </label></td>
                <td colspan="2">
                <select name="another_vlan_id" size="1">';

        $output .= "<option value=\"0\" style=\"color: grey;\">Nevybráno</option>";

        $dotaz_a_vlan = $this->conn_mysql->query("SELECT jmeno, vlan_id FROM nod_list WHERE typ_nodu = '2' ORDER BY vlan_id ");

        while($data_vlan = $dotaz_a_vlan->fetch_array()) {
            $output .= "<option value=\"".$data_vlan["vlan_id"]."\" ";

            if($this->form_another_vlan_id == $data_vlan["vlan_id"]) {
                $output .= " SELECTED ";
            }

            $output .= " >".$data_vlan["jmeno"];
            $output .= " ( vlan_id: ".$data_vlan["vlan_id"]." )
                        </option>";
        }

        /*
        $dotaz_t2 = mysql_query("SELECT * FROM tarify_int WHERE typ_tarifu = '1' ORDER BY gen_poradi ");

        while( $data_t2 = mysql_fetch_array($dotaz_t2) )
        {
        $output .= "<option value=\"".$data_t2["id_tarifu"]."\" ";

        if( isset($find_tarif) )
        { if( ( $find_tarif == $data_t2["id_tarifu"] ) ){ $output .= " SELECTED "; } }
        else
        {
            if( $this->form_id_tarifu == $data_t2["id_tarifu"] ){ $output .= " SELECTED "; }
        }

        $output .= " >".$data_t2["zkratka_tarifu"];
        $output .= " (".$data_t2["jmeno_tarifu"]." :: ".$data_t2["speed_dwn"]."/".$data_t2["speed_upl"]." )</option> \n";
        }
        */

        $output .= '
                </select>
                </td>

                <td></td>
            </tr>

            <tr><td colspan="4" ><br></td></tr>

            <tr><td colspan="4" align="center" >
            <input type="submit" value="OK / Odeslat / Uložit .... " name="odeslano" style="width: 400px; background-color: green; color: white; " >
            </td></tr>

            </table>
            </form>';

        return $output;
    }

    private function generujDataFiber(): bool
    {

        if($this->form_selected_nod < 1) {
            echo "";
            return false;
        }

        // skusime ip vygenerovat
        try {
            $vysl_nod = $this->conn_mysql->query("SELECT ip_rozsah FROM nod_list WHERE id = '".intval($this->form_selected_nod)."'");
            $radku_nod = $vysl_nod->num_rows;
        } catch (Exception $e) {
        }

        if($radku_nod <> 1) {
            if((strlen($this->form_ip) < 1)) {
                $this->form_ip = "E_1";
            }
            return false;
        } else {
            while($data_nod = $vysl_nod->fetch_array()):
                $ip_rozsah = $data_nod["ip_rozsah"];
            endwhile;
        }

        $vysl_tarif = $this->conn_mysql->query("SELECT gen_poradi FROM tarify_int WHERE id_tarifu = '".intval($this->form_id_tarifu)."' ");
        $radku_tarif = $vysl_tarif->num_rows;

        if($radku_tarif <> 1) {
            if((strlen($this->form_ip) < 1)) {
                $this->form_ip = "E_2";
            }
            return false;
        } else {
            while($data_tarif = $vysl_tarif->fetch_array()):
                $gen_poradi = $data_tarif["gen_poradi"];
            endwhile;
        }

        if(!($gen_poradi > 0)) {
            //znama chyba, nechame prazdne...
            //if( ( strlen($this->form_ip) < 1 ) ){ $ip = "E_3"; }
            return false;
        }

        list($r_a, $r_b, $r_c, $r_d) = preg_split("/[.]/", $ip_rozsah);

        if($gen_poradi == 1) {
            $r_d = $r_d + "0";
        } elseif($gen_poradi == 2) {
            $r_d = $r_d + "128";
        } elseif($gen_poradi == 3) {
            $r_c = $r_c + "1";
        } elseif($gen_poradi == 4) {
            $r_c = $r_c + "1";
            $r_d = $r_d + "128";
        } elseif($gen_poradi == 5) {
            $r_c = $r_c + "2";
            $r_d = $r_d + "0";
        } elseif($gen_poradi == 6) {
            $r_c = $r_c + "2";
            $r_d = $r_d + "128";
        } elseif($gen_poradi == 7) {
            $r_c = $r_c + "3";
            $r_d = $r_d + "0";
        } elseif($gen_poradi == 8) {
            $r_c = $r_c + "3";
            $r_d = $r_d + "128";
        } else {
            if((strlen($this->form_ip) < 1)) {
                $this->form_ip = "E_4";
            }
            return false;
        }

        $sub_rozsah = $r_a.".".$r_b.".".$r_c.".".$r_d;

        $sub_rozsah_d = $r_d;

        $r_d = $r_d + "8";

        try {
            $check_ip = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE ip <<= '$sub_rozsah/26' ORDER BY ip ASC");
            $check_ip_radku = pg_num_rows($check_ip);
        } catch (Exception $e) {
            $check_ip_radku = 0;
        }

        //echo "subrozsah: ".$sub_rozsah." xxx";

        if($check_ip_radku == 0) { // v rozsahu zadna ip, takze generujem prvni..
            $gen_ip = $r_a.".".$r_b.".".$r_c.".".$r_d;
        } else { //v db je vice ip adres ...
            //nacteni predchozi ip adresy ..
            while($data_check_ip = pg_fetch_array($check_ip)) {
                $gen_ip2 = $data_check_ip["ip"];
            }

            list($g_a, $g_b, $g_c, $g_d) = preg_split("/[.]/", $gen_ip2);

            if($sub_rozsah_d == "0") {
                $limit = 120;
            } elseif($sub_rozsah_d == "128") {
                $limit = 250;
            } else {
                if((strlen($this->form_ip) < 1)) {
                    $this->form_ip = "E_5";
                }
                return false;
            }

            if(($g_d >= $limit)) {
                $gen_ip = $ip_rozsah;
                $this->form_ip_error = 1;
            } else {
                //zde tedy pricist udaje a predat ...
                $g_d = $g_d + 2;

                //zpetna kontrola jeslti to neni lichy..
                $rs = $g_d % 2;

                if($rs == 1) { //je to lichy, chyba ...
                    if((strlen($this->form_ip) < 1)) {
                        $this->form_ip = "E_5";
                    }
                    return false;
                } else { //neni to lichy, takze je to spravne, cili finalni predani .
                    $gen_ip = $g_a.".".$g_b.".".$g_c.".".$g_d;
                }
            } // konec else if g_d pres limit

        } // konec else if  check_ip_radku == 0


        //tady asi cosi neni-li zadana ip, tak gen_ip = ip;
        if((strlen($this->form_ip) < 1)) {
            $this->form_ip = $gen_ip;
        }

        return true;
    }

    private function generujdata(): bool
    {
        // skusime ip adresu vygenerovat

        try {
            $vysl_ip = $this->conn_mysql->query("SELECT ip_rozsah FROM nod_list WHERE id = '".intval($this->form_selected_nod)."' ");
            $radku_ip = $vysl_ip->num_rows;
        } catch (Exception $e) {
        }

        if($radku_ip == 1) {
            while ($data_ip = $vysl_ip->fetch_array()) {
                $ip_rs = preg_split("/[\.]/", $data_ip["ip_rozsah"]);
            }

            if($ip_rs == false) {
                $gen_ip = "E4"; // split failed
                return false;
            } else {
                list($a, $b, $c, $d) = $ip_rs;
                $c = intval($c);
                $d = intval($d);
            }

            /*
            if( $ip_rozsah){

            $gen_ip="E_4";

            if( ( strlen($ip) <= 0) ){ $ip=$gen_ip; }
            return false;
            }
            */

            if($c == 0) {
                // b-ckova ip
                $gen_ip_find = $a.".".$b.".".$c.".".$d."/16";

                $msq_check_ip = pg_query($this->conn_pgsql, "SELECT ip FROM objekty WHERE ip <<= '$gen_ip_find' order by ip asc");
                $msq_check_ip_radku = pg_num_rows($msq_check_ip);

                if ($msq_check_ip_radku == 0) {
                    $c = 10;
                    $gen_ip = $a.".".$b.".".$c.".".$d;
                } else {

                    while ($data_check_ip = pg_fetch_array($msq_check_ip)) {
                        $gen_ip = $data_check_ip["ip"];
                    }

                    $ip_rs = preg_split("/[\.]/", $gen_ip);
                    if($ip_rs == false) {
                        $gen_ip = "E4"; // split failed
                        return false;
                    } else {
                        list($a, $b, $c, $d) = $ip_rs;
                        $c = intval($c);
                    }

                    $limit = 250;

                    if(($a == "212") and ($b == "80")) {
                        $gen_ip = $ip_rozsah;
                        $this->form_ip_error = 1;
                    } elseif(($c >= $limit)) {
                        $gen_ip = $ip_rozsah;
                        $this->form_ip_error = 1;
                    } elseif ($c == 0) {
                        $c = $c + 1;
                        $d = "3";
                        $gen_ip = $a.".".$b.".".$c.".".$d;

                    } else {
                        $gen_ip = "E3";
                        return false;
                    }

                } //konec else msq_check_ip_radku == 0

            } //konec if c == 0
            elseif(($a == "212") and ($b == "80")) { //verejny, 2 -- rout. prima, 4 -- tunelovana

                $sql_src = "SELECT INET_NTOA(ip_address) AS ip_address FROM public_ip_to_use ";

                if($this->form_typ_ip == 2) {
                    $sql_src .= " WHERE mode = '1' ";
                } elseif($this->form_typ_ip == 4) {
                    $sql_src .= " WHERE mode = '0' ";
                } else {
                    $gen_ip = $ip_rozsah;

                    if((strlen($this->form_ip) <= 0)) {
                        $this->form_ip = $gen_ip;
                    }
                    return false;
                }

                $sql_src .= " ORDER BY public_ip_to_use.ip_address ASC ";

                try {
                    $dotaz = $this->conn_mysql->query($sql_src);
                    $radku = $dotaz->num_rows;
                } catch (Exception $e) {
                    $radku = 0;
                }

                if($radku == 0) {
                    $gen_ip = "E3";

                    if((strlen($this->form_ip) <= 0)) {
                        $this->form_ip = $gen_ip;
                    }
                    return false;
                }

                while($data = $dotaz->fetch_array()) {
                    $ip_address = $data["ip_address"];

                    //kontrola :-)
                    //if(true){ $gen_ip = $ip_address; }

                    $dotaz_check = pg_query($this->conn_pgsql, "SELECT ip FROM objekty WHERE ip <<= '$ip_address' ");
                    $dotaz_check_radku = pg_num_rows($dotaz_check);

                    if(($dotaz_check_radku > 1)) { //chyba, vice adres vyhovelo vyberu
                        $gen_ip = "E_4";

                        if((strlen($this->form_ip) <= 0)) {
                            $this->form_ip = $gen_ip;
                        }
                        return false;
                    } elseif($dotaz_check_radku == 0) { //ip v DB není, OK
                        $gen_ip = $ip_address;

                        if((strlen($this->form_ip) <= 0)) {
                            $this->form_ip = $gen_ip;
                        }
                        break;
                    }

                } //end of while data fetch dotaz

            } //end of generate public IP address
            elseif (($d == 0 and $c != 0)) {
                // c-ckova ip
                $gen_ip_find = $a.".".$b.".".$c.".".$d."/24";

                $msq_check_ip = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE ip <<= '$gen_ip_find' order by ip asc");
                $msq_check_ip_radku = pg_num_rows($msq_check_ip);

                if($msq_check_ip_radku == 0) {
                    $d = 10;
                    $gen_ip = $a.".".$b.".".$c.".".$d;
                } else {
                    while($data_check_ip = pg_fetch_array($msq_check_ip)) {
                        $gen_ip = $data_check_ip["ip"];
                    }

                    list($a, $b, $c, $d) = preg_split("/[\.]/", $gen_ip);

                    if($d >= "254") {
                        $gen_ip = $a.".".$b.".".$c.".0";
                        $this->form_ip_error = 1;
                        $ip_rozsah = $gen_ip;
                    } else {
                        $d = $d + 2;
                        $gen_ip = $a.".".$b.".".$c.".".$d;
                    }
                } // konec else radku == 0

                // konec gen. ceckovy ip
            } else {
                $gen_ip = "E1"; //echo "chybnej vyber";
            }

            // vysledek predame
            if((strlen($this->form_ip) <= 0)) {
                $this->form_ip = $gen_ip;
            }


        } //end of: if $radku_ip == 1
        else {

            // vysledek predame
            if((strlen($this->form_ip) <= 0)) {
                $gen_ip = "E2"; //asi neprosel SQL dotaz
            }

            return false;
        }

        //zde generovani dalsich velicin
        if($this->form_typ_ip == 4) {
            if(((strlen($this->form_dns) <= 0) and (strlen($tunnel_user) <= 0) and (strlen($tunnel_pass) <= 0))) {
                $gen_user = "E_DNS";
                $gen_pass = "E_DNS";
            } else {
                $dns_trim = substr($this->form_dns, 0, 3).rand(0, 9);
                $dns_trim2 = substr($this->form_dns, 0, 2).rand(0, 99);


                $gen_user = $dns_trim;
                $gen_pass = $dns_trim2;
            }

            if((strlen($tunnel_user) <= 0)) {
                $tunnel_user = $gen_user;
            }
            if((strlen($tunnel_pass) <= 0)) {
                $tunnel_pass = $gen_pass;
            }


        } //konec if typ_ip == 4

        return true;
    }

    public function actionArchivZmenWifiDiff($vysledek_write): array
    {
        $output = "";

        $pole3 = "<b>akce: uprava objektu; </b><br>";
        $pole3 .= "[id_komplu]=> ".$this->update_id.",";
        $pole3 .= " diferencialni data: ";

        $obj_upd = $this->updatedDataArray;

        //novy zpusob archivovani dat
        foreach ($this->origDataArray as $key => $val) {
            if (!($obj_upd[$key] == $val)) {
                if (!($key == "id_komplu")) {
                    if($key == "ip") {
                        $pole3 .= "změna <b>IP adresy</b> z: ";
                        $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
                        $pole3 .= ", ";
                    } //konec key == ip
                    elseif($key == "mac") {
                        $pole3 .= "změna <b>MAC adresy</b> z: ";
                        $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
                        $pole3 .= ", ";
                    } //konec key == mac
                    elseif($key == "dov_net") {
                        $pole3 .= "změna <b>Povolen Inet</b> z: ";

                        if($val == "a") {
                            $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>";
                        } elseif($val == "n") {
                            $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>";
                        } else {
                            $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
                        }

                        $pole3 .= ", ";

                    } //konec key == dov_net
                    elseif($key == "id_nodu") {
                        $pole3 .= "změna <b>Připojného bodu</b> z: ";

                        $vysl_t1 = $this->conn_mysql->query("select jmeno from nod_list WHERE id = '$val'");
                        while ($data_t1 = $vysl_t1->fetch_array()) {
                            $pole3 .= "<span class=\"az-s1\">".$data_t1["jmeno"]."</span>";
                        }

                        $pole3 .= " na: ";

                        $val2 = $obj_upd[$key];

                        $vysl_t2 = $this->conn_mysql->query("select jmeno from nod_list WHERE id = '$val2'");
                        while ($data_t2 = $vysl_t2->fetch_array()) {
                            $pole3 .= "<span class=\"az-s2\">".$data_t2["jmeno"]."</span>";
                        }

                        $pole3 .= ", ";
                    } // konec key == id_nodu
                    elseif($key == "sikana_status") {
                        $pole3 .= "změna <b>Šikana</b> z: ";

                        if($val == "a") {
                            $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>";
                        } elseif($val == "n") {
                            $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>";
                        } else {
                            $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
                        }

                        $pole3 .= ", ";


                    } //konec sikana_status
                    elseif($key == "id_tarifu") {

                        $rs_tarif = $this->conn_mysql->query("SELECT jmeno_tarifu FROM tarify_int WHERE id_tarifu = '".intval($val)."' ");
                        $rs_tarif->data_seek(0);
                        list($tarif) = $rs_tarif->fetch_row();


                        $rs_tarif2 =  $this->conn_mysql->query("SELECT jmeno_tarifu FROM tarify_int WHERE id_tarifu = '".intval($obj_upd[$key])."' ");
                        $rs_tarif2->data_seek(0);
                        list($tarif2) = $rs_tarif2->fetch_row();

                        $pole3 .= "změna <b>Tarifu</b> z: "."<span class=\"az-s1\">".$tarif."</span>";
                        $pole3 .= " na: <span class=\"az-s2\">".$tarif2."</span>".", ";

                        //$pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
                        //$pole3 .= "na: <span class=\"az-s2\">".$obj_upd[$key]."</span>, ";

                    } //konec elseif id_tarifu
                    else { // ostatni mody, nerozpoznane
                        $pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
                        $pole3 .= "na: <span class=\"az-s2\">".$obj_upd[$key]."</span>, ";
                    }

                } //konec if nejde li od id_komplu ( to v tom poli neni )
            } // konec if obj == val
        } // konec foreach

        $add = $this->conn_mysql->query(
            "INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) "
                                        . "VALUES ('" . $pole3 . "','" . $this->loggedUserEmail . "','" . $vysledek_write . "')"
        );

        if($add) {
            $output .= "<br><H3><div style=\"color: green;\" >Změna objektu byla úspěšně zaznamenána do archivu změn.</div></H3>\n";
        } else {
            $output .= "<br><H3><div style=\"color: red;\" >Chyba! Změnu objektu do archivu změn se nepodařilo přidat.</div></H3>\n";
        }

        //pro osvezovani
        //
        list($work_output) = $this->work->workActionObjektyWifiDiff($pole3, $this->update_id);
        $output .= $work_output;

        return array($output);
    }

    public function actionArchivZmenWifi($vysledek_write): array
    {
        $output = "";

        $pole = "<b> akce: pridani objektu ; </b><br>";
        $pole .= "[id_komplu]=> ".intval($this->insertedId)." ";

        foreach ($this->addedDataArray as $key => $val) {

            if((strlen($val) > 0)) {
                //pokud v promenne neco, tak teprve resime vlozeni do Archivu zmen

                //nahrazovani na citelné hodnoty
                if($key == "id_tarifu") {

                    $rs_tarif = $this->conn_mysql->query("SELECT jmeno_tarifu FROM tarify_int WHERE id_tarifu = '".intval($val)."' ");
                    $rs_tarif->data_seek(0);
                    list($tarif) = $rs_tarif->fetch_row();
                    $pole .= " <b>tarif</b> => ".$tarif." ,";
                } elseif($key == "id_nodu") {
                    $rs_nod = $this->conn_mysql->query("SELECT jmeno FROM nod_list WHERE id = '".intval($val)."' ");
                    $rs_nod->data_seek(0);
                    list($nod) = $rs_nod->fetch_row();
                    $pole .= " <b>přípojný bod</b> => ".$nod." ,";
                } elseif($key == "typ") {

                    if($val == 1) {
                        $typ = "poc (platici)";
                    } elseif($val == 2) {
                        $typ = "poc (free)";
                    } elseif($val == 3) {
                        $typ = "AP";
                    } else {
                        $typ = $val;
                    }

                    $pole .= " <b>Typ</b> => ".$typ." ,";

                } elseif($key == "verejna") {

                    if($val == "99") {
                        $vip = "Ne";
                    } elseif($val == "1") {
                        $vip = "Ano";
                    } else {
                        $vip = $val;
                    }
                    //dalsi moznosti pripadne dodat

                    if(($val == "1") and (array_key_exists("tunnelling_ip", $this->addedDataArray) === true)) {
                        $vip = "Ano - tunelovaná";
                    }

                    $pole .= " <b>Veřejná IP</b> => ".$vip." ,";
                } elseif($key == "tunnelling_ip") {

                    //nic, resime v predchozim
                } else {
                    //nenaslo se nahrazovaci pravidlo, tj. pridat v "surovem" stavu
                    $pole .= " <b>[".$key."]</b> => ".$val." ,";
                }
            }

        } //end of foreach

        $add = $this->conn_mysql->query(
            "INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
                            "('".$this->conn_mysql->real_escape_string($pole)."',".
                            "'".$this->conn_mysql->real_escape_string($this->loggedUserEmail)."',".
            "'".$vysledek_write."')"
        );

        if($add) {
            $output .= "<br><H3><div style=\"color: green;\" >Změna objektu byla úspěšně zaznamenána do archivu změn.</div></H3>\n";
        } else {
            $output .= "<br><H3><div style=\"color: red;\" >Chyba! Změnu objektu do archivu změn se nepodařilo přidat.</div></H3>\n";
        }

        //automaticke osvezovani/restarty
        //
        $args = [
            'form_typ_ip' => $this->form_typ_ip
        ];

        list($work_output) = $this->work->workActionObjektyWifi($pole, $this->insertedId, $args);
        $output .= $work_output;

        return array($output);
    }

    private function actionArchivZmenFiberDiff($vysledek_write)
    {
        $pole3 = "<b>akce: uprava objektu; </b><br>";
        $pole3 .= "[id_komplu]=> ".$this->update_id.",";
        $pole3 .= " diferencialni data: ";

        $obj_upd = $this->updatedDataArray;

        //novy zpusob archivovani dat
        foreach ($this->origDataArray as $key => $val) {
            if (!($obj_upd[$key] == $val)) {
                if (!($key == "id_komplu")) {
                    if($key == "ip") {
                        $pole3 .= "změna <b>IP adresy</b> z: ";
                        $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
                        $pole3 .= ", ";
                    } //konec key == ip
                    elseif($key == "mac") {
                        $pole3 .= "změna <b>MAC adresy</b> z: ";
                        $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
                        $pole3 .= ", ";
                    } //konec key == mac
                    elseif($key == "port_id") {
                        $pole3 .= "změna <b>Číslo sw. portu</b> z: ";
                        $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
                        $pole3 .= ", ";
                    } //konec key == vezeni
                    elseif($key == "dov_net") {
                        $pole3 .= "změna <b>Povolen Inet</b> z: ";

                        if($val == "a") {
                            $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>";
                        } elseif($val == "n") {
                            $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>";
                        } else {
                            $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
                        }

                        $pole3 .= ", ";
                    } //konec key == dov_net
                    elseif($key == "verejna") {

                    } // konec key == verejna
                    elseif($key == "typ_ip") {
                        $pole3 .= "změna <b>Typ IP adresy</b> z: ";

                        if($val == "1") {
                            $pole3 .= "<span class=\"az-s1\">Neveřejná</span> na: <span class=\"az-s2\">Veřejná</span>";
                        } elseif($val == "2") {
                            $pole3 .= "<span class=\"az-s1\">Veřejná</span> na: <span class=\"az-s2\">Neveřejná</span>";
                        } else {
                            $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
                        }

                        $pole3 .= ", ";

                    } // konec key == typ_ip
                    elseif($key == "id_nodu") {
                        $pole3 .= "změna <b>Připojného bodu</b> z: ";

                        $vysl_t1 = $this->conn_mysql->query("select * from nod_list WHERE id = '$val'");
                        while ($data_t1 = $vysl_t1->fetch_array()) {
                            $pole3 .= "<span class=\"az-s1\">".$data_t1["jmeno"]."</span>";
                        }

                        $pole3 .= " na: ";

                        $val2 = $obj_upd[$key];

                        $vysl_t2 = $this->conn_mysql->query("select * from nod_list WHERE id = '$val2'");
                        while ($data_t2 = $vysl_t2->fetch_array()) {
                            $pole3 .= "<span class=\"az-s2\">".$data_t2["jmeno"]."</span>";
                        }

                        $pole3 .= ", ";
                    } // konec key == id_nodu
                    elseif($key == "sikana_status") {
                        $pole3 .= "změna <b>Šikana</b> z: ";

                        if($val == "a") {
                            $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>";
                        } elseif($val == "n") {
                            $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>";
                        } else {
                            $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
                        }

                        $pole3 .= ", ";

                    } //konec sikana_status
                    else { // ostatni mody, nerozpoznane
                        $pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
                        $pole3 .= "na: <span class=\"az-s2\">".$obj_upd[$key]."</span>, ";
                    }

                } //konec if nejde li od id_komplu ( to v tom poli neni )
            } // konec if obj == val
        } // konec foreach

        $add = $this->conn_mysql->query(
            "INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
                "('".$this->conn_mysql->real_escape_string($pole3)."','".
                $this->conn_mysql->real_escape_string($this->loggedUserEmail)."','".
            $this->conn_mysql->real_escape_string($vysledek_write)."') "
        );

        // if($add) {
        //     $output .= "<br><H3><div style=\"color: green;\" >Změna objektu byla úspěšně zaznamenána do archivu změn.</div></H3>\n";
        // } else {
        //     $output .= "<br><H3><div style=\"color: red;\" >Chyba! Změnu objektu do archivu změn se nepodařilo přidat.</div></H3>\n";
        // }

        // TODO: fix automatic restarts
        //zmena sikany nebo IP adresy
        // if( ereg(".*změna.*Šikana.*z.*", $pole3) )
        // {
        // Aglobal::work_handler("3"); //reinhard-fiber - sikana/net-n
        // }

        // //zmena NetN nebo IP adresy
        // if( ereg(".*změna.*Povolen.*Inet.*z.*", $pole3) )
        // {
        // Aglobal::work_handler("3"); //reinhard-fiber - sikana/net-n
        // }

        // //zmena IP adresy pokud je aktivni Sikana ci NetN
        // if( (
        //     ereg(".*změna.*IP.*adresy.*z.*", $pole3)
        //     and
        //     (
        //     ($pole_puvodni_data["sikana_status"] == "a")
        //     or
        //     ($pole_puvodni_data["dov_net"] == "n")
        //     )
        //     )
        // )
        // {
        //     Aglobal::work_handler("1"); //reinhard-3 (ros) - restrictions (net-n/sikana)
        //     Aglobal::work_handler("2"); //reinhard-wifi (ros) - restrictions (net-n/sikana)

        //     Aglobal::work_handler("3"); //reinhard-fiber - sikana/net-n

        //     Aglobal::work_handler("4"); //reinhard-fiber - radius
        //     Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)

        //     Aglobal::work_handler("6"); //(reinhard-fiber) - mikrotik.dhcp.leases.erase

        //     Aglobal::work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update

        // }
        // elseif(ereg(".*změna.*IP.*adresy.*z.*", $pole3)){

        //     Aglobal::work_handler("4"); //reinhard-fiber - radius

        //     Aglobal::work_handler("6"); //(reinhard-fiber) - mikrotik.dhcp.leases.erase

        //     Aglobal::work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update
        // }

        // if(ereg(".*změna.*MAC.*adresy.*", $pole3)){

        //     Aglobal::work_handler("4"); //reinhard-fiber - radius
        //     Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)

        //     Aglobal::work_handler("6"); //(reinhard-fiber) - mikrotik.dhcp.leases.erase
        //     Aglobal::work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update

        // }

        // //zmena pripojneho bodu

        // //zmena tarifu

        // //zmena cisla portu
        // if(ereg(".*Číslo sw. portu.*", $pole3)){
        //     Aglobal::work_handler("4"); //reinhard-fiber - radius
        //     Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)

        //     Aglobal::work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update

        // }
    }

    private function actionArchivZmenFiberAdd($vysledek_write)
    {
        $pole = "<b> akce: pridani objektu ; </b><br>";

        $pole .= "[id_komplu]=> ".intval($this->insertedId)." ";

        $obj_add = $this->addedDataArray;

        foreach ($obj_add as $key => $val) {

            if((strlen($val) > 0)) {
                //pokud v promenne neco, tak teprve resime vlozeni do Archivu zmen

                //nahrazovani na citelné hodnoty
                if($key == "id_tarifu") {

                    $rs_tarif = $this->conn_mysql->query("SELECT jmeno_tarifu FROM tarify_int WHERE id_tarifu = '".intval($val)."' ");

                    $rs_tarif->data_seek(0);
                    list($tarif) = $rs_tarif->fetch_row();

                    $pole .= " <b>tarif</b> => ".$tarif." ,";
                } elseif($key == "id_nodu") {
                    $rs_nod = $this->conn_mysql->query("SELECT jmeno FROM nod_list WHERE id = '".intval($val)."' ");

                    $rs_nod->data_seek(0);
                    list($nod) = $rs_nod->fetch_row();

                    $pole .= " <b>přípojný bod</b> => ".$nod." ,";
                } elseif($key == "typ") {

                    if($val == 1) {
                        $this->form_typ = "poc (platici)";
                    } elseif($val == 2) {
                        $this->form_typ = "poc (free)";
                    } elseif($val == 3) {
                        $this->form_typ = "AP";
                    } else {
                        $this->form_typ = $val;
                    }

                    $pole .= " <b>Typ</b> => ".$this->form_typ." ,";

                } elseif($key == "verejna") {

                    if($val == "99") {
                        $vip = "Ne";
                    } elseif($val == "1") {
                        $vip = "Ano";
                    } else {
                        $vip = $val;
                    }

                    $pole .= " <b>Veřejná IP</b> => ".$vip." ,";
                } else {
                    $pole = $pole." <b>[".$key."]</b> => ".$val."\n";
                }

            }

        }

        $add = $this->conn_mysql->query(
            "INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
                "('".$this->conn_mysql->real_escape_string($pole)."','".
                $this->conn_mysql->real_escape_string($this->loggedUserEmail)."','".
            $this->conn_mysql->real_escape_string($vysledek_write)."') "
        );

        // if($add) {
        //     $output .= "<br><H3><div style=\"color: green;\" >Změna objektu byla úspěšně zaznamenána do archivu změn.</div></H3>\n";
        // } else {
        //     $output .= "<br><H3><div style=\"color: red;\" >Chyba! Změnu objektu do archivu změn se nepodařilo přidat.</div></H3>\n";
        // }

        //ted automaticky pridavani restartu

        //asi vše :-)
        // \Aglobal::work_handler("3"); //rh-fiber - iptables
        // \Aglobal::work_handler("4"); //rh-fiber - radius
        // \Aglobal::work_handler("5"); //rh-fiber - shaper
        // \Aglobal::work_handler("6"); //reinhard-fiber - mikrotik.dhcp.leases.erase
        // \Aglobal::work_handler("7"); //trinity - sw.h3c.vlan.set.pl update

        // \Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
    }

    public function vypis_tab($par)
    {
        $output = "";
        if($par == 1) {
            $output .= "<table border=\"0\" width=\"100%\" class=\"objekty-table\" >\n";
        } elseif ($par == 2) {
            $output .= "\n".'</table>'."\n";
        } else {
            $output .= "chybny vyber";
        }

        return $output;
    }

    public function vypis_tab_first_rows($mod_vypisu)
    {
        $output = "";

        $output .= '<tr>
                    <td colspan="1"><b>dns </b></td>
                    <td colspan="3"><b>ip adresa </b></td>
                    <td><b>mac </b></td>
                    <td><b>typ </b></td>';


        if($mod_vypisu == 2) {
            $output .= "<td align=\"center\" ><b>Číslo portu:</b></td>";
        } else {
            $output .= "<td><b>client ap </b></td>";
        }

        $output .= '
            <td align="center" ><b>upravit</b></td>
            <td align="center" ><b>smazat</b></td>
            <td><b>třída </b></td>
            <td><b>Aktivní</b></td>
            <td><b>Test obj.</b></td>
            <td><b>Linka </b></td>
            <td><b>Omezení </b></td>';

        $output .= '</tr>';

        $styl = "border-bottom: 1px dashed black; ";

        $output .= "<tr style=\"color: grey; \"  >
            <td colspan=\"2\" style=\"".$styl."\" ><b>přípojný bod: </b></td>
            <td colspan=\"1\" style=\"".$styl."\" ><b>historie </b></td>
            <td colspan=\"1\" style=\"".$styl."\" align=\"center\" ><b>vlastník </b></td>
            <td colspan=\"2\" style=\"".$styl."\" ><b>mac klienta </b></td>
            <td colspan=\"1\" style=\"".$styl."\" ><b>ip rb </b></td>

            <td colspan=\"1\" style=\"".$styl."\" align=\"center\" ><b>přidal</b></td>
            <td colspan=\"1\" style=\"".$styl."\" align=\"center\" ><b>upravil </b></td>
            <td style=\"".$styl."\" >&nbsp;</td>
            <td colspan=\"3\" style=\"".$styl."\" ><b>Datum přidání </b></td>
            <td colspan=\"1\" style=\"".$styl."\" ><b>Reg. Form </b></td>
        </tr>";

        return $output;
    }

    public function select($se_id, $razeni): array
    {
        // co - co hledat, 1- podle dns, 2-podle ip

        if($this->pdo instanceof \PDO) {
            if($this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) != "sqlite") {
                $this->pdo->query(" SET DATESTYLE TO 'SQL, EUROPEAN' ");
            }
        } else {
            pg_query($this->conn_pgsql, " SET DATESTYLE TO 'SQL, EUROPEAN' ");
        }

        if($se_id == 1) {
            $se = '';
        } elseif($se_id == 2) {
            $se = " AND typ LIKE '1' ";
        } elseif($se_id == 3) {
            $se = " AND typ LIKE '2' ";
        } elseif($se_id == 4) {
            $se = " AND typ LIKE '3' ";
        } elseif($se_id == 5) {
            $se = " AND id_tridy > 0 ";
        } elseif($se_id == 6) {
            $se = " AND verejna !=99 ";
        } elseif($se_id == 7) {
            $se = " AND id_cloveka is null ";
        } elseif($se_id == 8) {
            $se = " AND dov_net LIKE 'n' ";
        } elseif($se_id == 9) {
            $se = " AND sikana_status LIKE 'a' ";
        }

        // tvoreni dotazu
        // $order=$_POST["razeni"];

        if ($razeni == 1) {
            $order = " order by dns_jmeno DESC";
        } elseif ($razeni == 2) {
            $order = " order by dns_jmeno ASC";
        } elseif ($razeni == 3) {
            $order = " order by ip DESC";
        } elseif ($razeni == 4) {
            $order = " order by ip ASC";
        } elseif ($razeni == 7) {
            $order = " order by mac DESC";
        } elseif ($razeni == 8) {
            $order = " order by mac ASC";
        }
        //# elseif ( $razeni == 9 ){ $order=" order by typ DESC"; }
        //# elseif ( $razeni == 10){ $order=" order by typ ASC"; }
        else {
            $order = " order by id_komplu ASC ";
        }

        if(isset($se)) {
            $pole[] = $se;
        } else {
            $pole[] = "";
        }

        $pole[] = $order;

        return $pole;

    } //konec funkce select

    //zde funkce export
    public function export_vypis_odkaz()
    {
        $output = "";

        // $fp=fopen("export/objekty.xls", "w");   // Otevřeme soubor tabulka.xls, pokud existuje, bude smazán, jinak se vytvoří nový sobor
        // fputs($fp, "<table border='1'> \n \n");   // Zapíšeme do souboru začátek tabulky
        // fputs($fp, "<tr>");   // Zapíšeme do souboru začátek řádky, kde budou názvy sloupců (polí)

        // $vysledek_pole=pg_query("SELECT column_name FROM information_schema.columns WHERE table_name ='objekty' ORDER BY ordinal_position ");

        // while ($vysledek_array_pole=pg_fetch_row($vysledek_pole) )
        // { fputs($fp, "<td><b> ".$vysledek_array_pole[0]." </b></td> \n");
        // }

        // fputs($fp, "</tr>");   // Zapíšeme do souboru konec řádky, kde jsou názvy sloupců (polí)

        // $vysledek = pg_query("SELECT * FROM objekty ORDER BY id_komplu ASC");

        // while ($data=pg_fetch_array($vysledek) )
        // {
        //     fputs($fp, "\n <tr>");

        //     fputs($fp, "<td> ".$data["id_komplu"]."</td> ");
        //     fputs($fp, "<td> ".$data["id_tridy"]."</td> ");
        //     fputs($fp, "<td> ".$data["id_cloveka"]."</td> ");
        //     fputs($fp, "<td> ".$data["dns_jmeno"]."</td> ");
        //     fputs($fp, "<td> ".$data["ip"]."</td> ");
        //     fputs($fp, "<td> ".$data["mac"]."</td> ");
        //     fputs($fp, "<td> ".$data["rra"]."</td> ");
        //     fputs($fp, "<td> ".$data["vezeni"]."</td> ");
        //     fputs($fp, "<td> ".$data["dov_net"]."</td> ");
        //     fputs($fp, "<td> ".$data["swz"]."</td> ");
        //     //     fputs($fp,"<td> ".$data["sc"]."</td> ");
        //     fputs($fp, "<td> ".$data["typ"]."</td> ");
        //     fputs($fp, "<td> ".$data["poznamka"]."</td> ");
        //     fputs($fp, "<td> ".$data["verejna"]."</td> ");
        //     fputs($fp, "<td> ".$data["ftp_update"]."</td> ");
        //     fputs($fp, "<td> ".$data["pridano"]."</td> ");
        //     fputs($fp, "<td> ".$data["id_nodu"]."</td> ");
        //     fputs($fp, "<td> ".$data["rb_mac"]."</td> ");
        //     fputs($fp, "<td> ".$data["rb_ip"]."</td> ");
        //     fputs($fp, "<td> ".$data["pridal"]."</td> ");
        //     fputs($fp, "<td> ".$data["upravil"]."</td> ");
        //     fputs($fp, "<td> ".$data["sikana_status"]."</td> ");
        //     fputs($fp, "<td> ".$data["sikana_cas"]."</td> ");
        //     fputs($fp, "<td> ".$data["sikana_text"]."</td> ");
        //     fputs($fp, "<td> ".$data["vip_snat"]."</td> ");
        //     fputs($fp, "<td> ".$data["vip_snat_lip"]."</td> ");

        //     fputs($fp, "</tr> \n ");
        //     // echo "vysledek_array: ".$vysledek_array[$i];

        // }

        // fputs($fp, "</table>");   // Zapíšeme do souboru konec tabulky
        // fclose($fp);   // Zavřeme soubor

        $output .= "<a href=\"export\objekty.xls\">export dat</a>";

        return $output;
    } //konec funkce vypis odkaz

    // TODO: remove this function, probably unused
    public function vypis_razeni_a2()
    {

        $input_value = "1";
        $input_value2 = "2";

        for ($i = 1; $i < 6 ; $i++):

            //vnejsi tab
            echo "<td>";

            //vnitrni tab
            echo "\n <table border=\"0\"><tr><td>";

            if($i == "3" or $i == "4") {
                echo "";
            } else {

                echo "\n\n <input type=\"radio\" ";
                if (($this->razeni == $input_value)) {
                    echo " checked ";
                }
                echo "name=\"razeni\" value=\"".$input_value."\" onClick=\"form1.submit();\" > ";

                // obr, prvni sestupne -descent
                echo "<img src=\"img2/ses.png\" alt=\"ses\" width=\"15px\" height=\"10px\" >";
                if ($i != 5) {
                    echo " | ";
                }
                echo "</td> \n\n <td>";

                echo "<input type=\"radio\" ";
                if (($this->razeni == $input_value2)) {
                    echo " checked ";
                }
                echo " name=\"razeni\" value=\"".$input_value2."\" onClick=\"form1.submit();\"> \n";

                // obr, druhy vzestupne - asc
                echo "<img src=\"img2/vzes.png\" alt=\"vzes\" width=\"15px\" height=\"10px\" >";

            }

            // vnitrni tab
            echo "\n </td></tr></table> \n\n";

            $input_value = $input_value + 2;
            $input_value2 = $input_value2 + 2;

            // konec vnitrni tab
            echo "</td>";

        endfor;

    }

    public function zjistipocet($mod, $id)
    {
        $tarif_sql = "";

        if ($mod == 1) { //wifi sit ...
            //prvne vyberem wifi tarify...
            $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '0' ");

            if($dotaz_f->num_rows < 1) {
                return 0;
            }

            $i = 0;
            while($data_f = $dotaz_f->fetch_array()) {
                if($i == 0) {
                    $tarif_sql .= " AND ( ";
                }
                if($i > 0) {
                    $tarif_sql .= " OR ";
                }

                $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";

                $i++;
            }

            if($i > 0) {
                $tarif_sql .= " ) ";
            }
        } elseif ($mod == 2) { //fiber sit ...
            $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '1' ");

            if($dotaz_f->num_rows < 1) {
                return 0;
            }

            $i = 0;
            while($data_f = $dotaz_f->fetch_array()) {
                if($i == 0) {
                    $tarif_sql .= " AND ( ";
                }
                if($i > 0) {
                    $tarif_sql .= " OR ";
                }

                $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";

                $i++;
            }

            if($i > 0) {
                $tarif_sql .= " ) ";
            }
        }

        $dotaz = pg_query($this->conn_pgsql, "SELECT id_cloveka FROM objekty WHERE ( id_cloveka = '".intval($id)."' ".$tarif_sql." ) ");
        $radku = pg_num_rows($dotaz);

        return $radku;
    }

    public function vypis($sql, $co, $id, $dotaz_final = ""): string
    {
        $output = "";
        $tarif_sql = "";

        // wifi sit ...vypis u vlastniku (dalsi pouziti nevim)
        if ($co == 3) {
            //prvne vyberem wifi tarify...
            $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '0' ");

            $i = 0;

            while($data_f = $dotaz_f->fetch_array()) {
                if($i == 0) {
                    $tarif_sql .= "AND ( ";
                }
                if($i > 0) {
                    $tarif_sql .= " OR ";
                }

                $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";

                $i++;
            }

            if($i > 0) {
                $tarif_sql .= " ) ";
            }

            $dotaz = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE id_cloveka='".intval($id)."' ".$tarif_sql);

        }
        // fiber sit ...vypis pouze u vlastniku
        elseif ($co == 4) {
            $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '1' ");

            $i = 0;

            while($data_f = $dotaz_f->fetch_array()) {
                if($i == 0) {
                    $tarif_sql .= "AND ( ";
                }
                if($i > 0) {
                    $tarif_sql .= " OR ";
                }

                $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";

                $i++;
            }

            if($i > 0) {
                $tarif_sql .= " ) ";
            }

            $dotaz = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE id_cloveka='".intval($id)."' ".$tarif_sql);

        } else {
            $dotaz = false;
            if($this->pdo instanceof \PDO) {
                list($data_rs, $dotaz_err) = $this->callPdoQueryAndFetch($dotaz_final);
            } else {
                $dotaz = pg_query($this->conn_pgsql, $dotaz_final);
            }
        }

        if($dotaz !== false and !($this->pdo instanceof \PDO)) {
            $radku = pg_num_rows($dotaz);
            $data_rs = pg_fetch_all($dotaz);
        } elseif (!($this->pdo instanceof \PDO)) {
            $this->p_bs_alerts["Dotaz pro výpis objektů selhal! </br>". pg_last_error($this->conn_pgsql)] = "danger";
            $this->listErrors .= "</br>";
            return $output;
        }

        if($dotaz_err != null and $this->pdo instanceof \PDO) {
            $this->p_bs_alerts["Dotaz pro výpis objektů selhal! </br>". $dotaz_err] = "danger";
            $this->listErrors .= "</br>";
            return $output;
        } elseif($this->pdo instanceof \PDO) {
            $radku = count($data_rs);
        }

        if ($radku == 0) {
            if($co == 3 or $co == 4) {
                $output .= "<tr><td colspan=\"9\" >";
                $output .= "<span style=\"color: #555555; \">Žádný objekt není přiřazen. </span></td></tr>";
            } else {
                $output .= "<tr><td colspan=\"8\" ><span style=\"color: red; \">Nenalezeny žádné odpovídající data dle hledaného \"".htmlspecialchars($sql)."\" ";
                // $output .= " (dotaz: ".$dotaz_final.") ";
                $output .= "</td></tr>";
            }

            return $output;
        }

        // while ($data = pg_fetch_array($dotaz)) {
        foreach ($data_rs as $row => $data) {
            // $output .= $data[sloupec1]." ".$data[sloupec2];
            // $output .= "<br />";

            //    if( $data["id_tridy"] > 0 ){ $garant=1; }
            if($data["verejna"] <> 99) {
                $verejna = 1;
            }

            /*
            if ( $garant==1)
            {
            $id_tridy=$data["id_tridy"];
            //zjistime sirku pasma
            $dotaz_g = pg_exec($this->conn_pgsql, "SELECT * FROM tridy WHERE id_tridy = '$id_tridy' ");

            while (  $data_g=pg_fetch_array($dotaz_g) ) { $sirka=$data_g["sirka"]; }
            }
            */

            //zacatek rady a prvni bunka
            $output .= "\n <tr>"."<td class=\"tab-objekty2\">".$data["dns_jmeno"]."</td> \n\n";

            $pridano = $data["pridano"];

            // treti bunka - ip adresa
            if ($verejna == 1) {
                if ($data["vip_snat"] == 1) {
                    $output .= "<td colspan=\"2\" class=\"tab-objekty2\" bgcolor=\"orange\" >".$data["ip"]." </td> \n";
                } elseif($data["tunnelling_ip"] == 1) {
                    $output .= "<td colspan=\"2\" class=\"tab-objekty2\" bgcolor=\"#00CC33\" >".$data["ip"]." </td> \n";
                } else {
                    $output .= "<td colspan=\"2\" class=\"tab-objekty2\" bgcolor=\"#FFFF99\" >".$data["ip"]." </td> \n";
                }
            } else {
                $output .= "<td colspan=\"2\" class=\"tab-objekty2\">".$data["ip"]."</td> \n";
            }

            // druha bunka - pozn
            $output .= "<td class=\"tab-objekty2\" align=\"center\" ><span class=\"pozn\"> <img title=\"poznamka\" src=\"img2/poznamka3.png\" align=\"middle\" ";
            $output .= " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." , Vytvořeno: ".$pridano." ');\" ></span></td> \n";

            // 4-ta bunka - mac
            $output .= "<td class=\"tab-objekty2\">".$data["mac"]."</td> \n";

            // 5-ta typ
            if ($data["typ"] == 1) {
                $output .= "<td class=\"tab-objekty\">"."daně"."</td> \n";
            } elseif ($data["typ"] == 2) {
                $output .= "<td class=\"tab-objekty\" bgcolor=\"#008000\" ><font color=\"#FFFFFF\">"." free "."</font></td> \n";
            } elseif ($data["typ"] == 3) {
                $output .= "<td class=\"tab-objekty\" bgcolor=\"yellow\" >"." ap "."</td> \n";
            } else {
                $output .= "<td class=\"tab-objekty\" >Error </td> \n";
            }

            // rra - client ip -- CISLO portu
            $output .= "<td class=\"tab-objekty2\" align=\"center\" ><span style=\"\"> ";

            if($this->mod_vypisu == 2) {
                $output .= "".$data["port_id"]."";
            } else {
                if(strlen($data["client_ap_ip"]) < 1) {
                    $output .= "&nbsp;";
                } else {
                    $output .= $data["client_ap_ip"];
                }
            }

            $output .= "</span></td> \n";

            //uprava a mazani

            $update_mod_vypisu = null;

            // detect mod_vypisu for update/erase form
            $error_msq_base = "Pro Objekt \"" . var_export($data['dns_jmeno'], true) ."\" nelze zjistit mod_vypisu (pro update/erase button)! </br>";
            $dotaz_final = "SELECT typ_tarifu FROM tarify_int2 WHERE id_tarifu = '".intval($data["id_tarifu"])."' ";
            list($data_rs, $dotaz_err) = $this->callPdoQueryAndFetch($dotaz_final, 'pdoMysql');

            if($dotaz_err != false) {
                $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": db query for detection of mod_vypisu failed! " . var_export($dotaz_err, true));
                $this->p_bs_alerts[$error_msq_base . " (" . var_export($dotaz_err, true) .")"] = "danger";
            } else {
                $rs_update = count($data_rs);

                if($rs_update == 1) {
                    foreach ($data_rs as $row => $data_update) {
                        if($data_update["typ_tarifu"] == 1) {
                            $update_mod_vypisu = 2;
                        } else {
                            $update_mod_vypisu = 1;
                        }
                    }
                } else {
                    $output .= $error_msq_base . " (wrong num_rows)";
                    $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": db query for detection of mod_vypisu failed! num_rows: " . var_export($rs_update, true));
                }
            }

            // 6-ta update
            if ($this->listAllowedActionUpdate === false) {
                $output .= "<td class=\"tab-objekty2\" style=\"font-size: 10px; font-family: arial; color: gray;\">Upravit</td> \n";
            } else {
                $output .= "<td class=\"tab-objekty2\" > <form method=\"POST\" action=\"/objekty/action\" >";
                $output .= "<input type=\"hidden\" name=\"update_id\" value=\"".$data["id_komplu"]."\" >";

                if(strlen($this->csrf_html) > 0) {
                    $output .= $this->csrf_html;
                }

                $output .= "<input type=\"hidden\" name=\"mod_objektu\" value=\"".$update_mod_vypisu."\" >";

                $output .= "<input class=\"\" type=\"submit\" value=\"update\" >";

                $output .= "</td></form> \n";
            }

            // 7 smazat
            if ($this->listAllowedActionErase === false) {
                $output .= "<td class=\"tab-objekty2\" style=\"font-size: 10px; font-family: arial; color: gray;\">Smazat</td>";
            } else {
                $output .= "<td class=\"tab-objekty2\" > <form method=\"POST\" action=\"objekty-erase.php\" >";
                $output .= "<input type=\"hidden\" name=\"erase_id\" value=\"".$data["id_komplu"]."\" >";
                $output .= "<input class=\"\" type=\"submit\" value=\"smazat\" >";

                $output .= "</td> </form> \n";
            }

            // 8-ma typ objektu :)
            $id = $data["id_komplu"];
            $class_id = $data["id_tridy"];

            global $garant_akce;

            // generovani tridy
            if($data["typ"] == 3) {
                $output .= "";
            } else {
                $output .= "<td class=\"tab-objekty2\"><font color=\"red\">"." peasant "."</font></td> \n";
            }

            // prirava promennych pro tresty a odmeny
            if($data["dov_net"] == "a") {
                $dov_net = "<font color=\"green\">NetA</font>";
            } else {
                $dov_net = "<font color=\"orange\">NetN</font> \n";
            }

            if(preg_match("/a/", $data["sikana_status"])) {
                $sikana_status_s = "<span class=\"obj-link-sikana\" >".
                "<a href=\"http://damokles.adminator.net:8009/index.php".
                "?sc=".intval($data["sikana_cas"])."&st=".urlencode($data["sikana_text"])."\" target=\"_new\" >".
                "Sikana-A (".$data["sikana_cas"].")</a></span>\n";

            } else {
                $sikana_status_s = "<span style=\"color: green;\" >Sikana-N</span>";
            }

            //tresty a odmeny - 6 bunek
            if($data["typ"] == 3) {
                $output .= "<td class=\"tab-objekty2\" colspan=\"5\" bgcolor=\"yellow\" align=\"center\"> ap-čko jaxvine </td> \n";
            } else {
                $output .= "<td class=\"tab-objekty2\" >".$dov_net."</td>";

                //test objetktu
                $output .= "<td class=\"tab-objekty2\" >";

                if($update_mod_vypisu == 2) {
                    $output .= "<a href=\"objekty-test.php?id_objektu=".$data["id_komplu"]."\" >test</a>";
                } else {
                    $output .= "<br>";
                }

                $output .= "</td> \n";
                //zde tarif 2 gen.
                $output .= "<td class=\"tab-objekty2\" >";
                $id_tarifu = $data["id_tarifu"];

                //dodelat klikatko pro sc
                //{ $tarif="<span class=\"tarifsc\"><a href=\"https://trinity.simelon.net/monitoring/data/cat_sc.php?ip=".$data["ip"]."\" target=\"_blank\" >sc</a></span>"; }

                $dotaz_final = "SELECT barva, id_tarifu, zkratka_tarifu FROM tarify_int WHERE id_tarifu = '".intval($id_tarifu)."' ";
                list($data_rs, $dotaz_err) = $this->callPdoQueryAndFetch($dotaz_final, 'pdoMysql');

                if(count($data_rs) <> 1) {
                    $output .= "<span style=\"font-weight: bold; color: red;\" >E_TARIF_1</span>";
                    $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": db query for tarif failed! " . var_export($dotaz_err, true));
                } else {
                    foreach ($data_rs as $row => $data_f) {
                        $output .= "<span style=\"color: ".$data_f["barva"]."; \" >";
                        $output .= "<a href=\"/admin/tarify?id_tarifu=".$data_f["id_tarifu"]."\" >".$data_f["zkratka_tarifu"]."</a>";
                        $output .= "</span>\n";
                    }
                }
                $output .= "</td>\n";

                $output .= "<td class=\"tab-objekty2\" colspan=\"2\" >".$sikana_status_s."</td>\n";
            }

            $output .= "</tr>\n<tr>\n";

            // tady uz asi druhej radek :)

            // pripojny nod
            $output .= "<td class=\"tab-objekty\" colspan=\"2\" >";
            $dotaz_final = "SELECT jmeno FROM nod_list WHERE id='".intval($data["id_nodu"])."' ";
            list($data_rs, $dotaz_err) = $this->callPdoQueryAndFetch($dotaz_final, 'pdoMysql');

            if(count($data_rs) == 0) {
                $output .= "<span style=\"color: gray; \">přípojný bod nelze zjistit</span>";
                $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": db query for node failed! " . var_export($dotaz_err, true));
            } else {
                foreach ($data_rs as $row => $zaznam_bod) {
                    $output .= "<span class=\"objekty-2radka objekty-odkaz\">".
                    "<a href=\"/topology/node-list?find=".urlencode($zaznam_bod["jmeno"])."\" >".
                    $zaznam_bod["jmeno"]."</a></span> ";
                }
            }

            $output .= "</td>\n";

            // sem historii
            $output .= "<td class=\"tab-objekty\" ><span class=\"objekty-2radka\" style=\"\" > H: ";
            $output .= "<a href=\"/archiv-zmen?id=".$id."\" >".$id."</a>";
            $output .= " </span>";

            $output .= "</td>\n";

            // id vlastnika
            $output .= "<td class=\"tab-objekty\" align=\"center\" ><span class=\"objekty-2radka\" >\n";

            if($data["id_cloveka"] == null) {
                $output .= "<span style=\"color: grey;\">Není</span>";
            } else {
                $sql_final = "SELECT firma, archiv FROM vlastnici WHERE id_cloveka = '".intval($data["id_cloveka"])."'";
                $vlastnik_dotaz = false;
                $firma_vlastnik = null;
                $archiv_vlastnik = null;

                if($this->pdo instanceof \PDO) {
                    list($data_vlastnik_rs, $dotaz_err) = $this->callPdoQueryAndFetch($dotaz_final);
                } else {
                    $vlastnik_dotaz = pg_query($this->conn_pgsql, $sql_final);
                    $data_vlastnik_rs = pg_fetch_all($vlastnik_dotaz);
                }

                if($dotaz_err == null and $this->pdo instanceof \PDO) {
                    // TODO: add returning error(s)
                    $output .= "V: (E_1)" . $data["id_cloveka"];
                } elseif(count($data_vlastnik_rs) != 1) {
                    // TODO: add returning error(s)
                    $output .= "V: (E_2 ". var_export(count($data_vlastnik_rs), true) . ")" . $data["id_cloveka"];
                } else {
                    foreach ($data_vlastnik_rs as $key => $data_vlastnik) {
                        $firma_vlastnik = $data_vlastnik["firma"];
                        $archiv_vlastnik = $data_vlastnik["archiv"];
                    }

                    if ($archiv_vlastnik == 1) {
                        $output .= "V: <a href=\"/vlastnici/archiv?find_id=".$data["id_cloveka"]."\" >".$data["id_cloveka"]."</a>";
                    } elseif($firma_vlastnik == 1) {
                        $output .= "V: <a href=\"/vlastnici2?find_id=".$data["id_cloveka"]."\" >".$data["id_cloveka"]."</a>";
                    } else {
                        $output .= "V: <a href=\"/vlastnici?find_id=".$data["id_cloveka"]."\" >".$data["id_cloveka"]."</a>";
                    }
                }
            }

            $output .= "</span> </td>\n";

            if($update_mod_vypisu == 2) {
                $output .= "<td class=\"tab-objekty\" colspan=\"3\" > <br></td>";
            } else {
                if (!($co == 3)) {
                    $output .= "<td class=\"tab-objekty\" colspan=\"2\" > <span class=\"objekty-2radka\" >";
                    //if( (strlen($data["rb_mac"]) > 0) ){ $output .= $data["rb_mac"]; }
                    $output .= "&nbsp;";
                    $output .= "</span></td> \n";

                    //$output .= "<td><br>b</td>";

                    $output .= "<td class=\"tab-objekty\" colspan=\"1\" ><span class=\"objekty-2radka\" >";
                    //if( (strlen($data["rb_ip"]) > 0) ){ $output .= $data["rb_ip"]; }
                    $output .= "&nbsp;";
                    $output .= "</span></td> \n";
                }
            }

            // kdo pridal a kdo naposledy upravil
            $output .= "<td class=\"tab-objekty\" colspan=\"1\" align=\"center\" ><span class=\"objekty-2radka\" >";
            if((strlen($data["pridal"]) > 0)) {
                $output .= $data["pridal"];
            } else {
                $output .= "<span style=\"color: #CC3366;\" >nezadáno</span>";
            }
            $output .= "</span></td> \n";

            $output .= "<td class=\"tab-objekty\" colspan=\"1\" align=\"center\" ><span class=\"objekty-2radka\" >";
            if((strlen($data["upravil"]) > 0)) {
                $output .= $data["upravil"];
            } else {
                $output .= "<span style=\"color: #CC3366;\" >nezadáno</span>";
            }
            $output .= "</span></td> \n";

            $output .= "<td class=\"tab-objekty\" >&nbsp;</td> \n";

            // kdy se objekty pridal
            //prvne to orezem
            $orez = $pridano;
            if(strlen($orez) > 0) {
                $orezano = explode(':', $orez);
                $pridano_orez = $orezano[0] . ":" . $orezano[1];
            } else {
                $pridano_orez = null;
            }

            $output .= "<td class=\"tab-objekty\" colspan=\"3\" ><span class=\"objekty-2radka\" >".$pridano_orez."</span></td>
                        <td class=\"tab-objekty\" >
                        <form method=\"POST\" action=\"/print/reg-form\" >\n";

            if(strlen($this->csrf_html) > 0) {
                $output .= $this->csrf_html;
            } else {
                $this->logger->warning(__CLASS__ . "\\" . __FUNCTION__ . ": missing csrf_html");
            }

            $output .= "<input type=\"hidden\" name=\"id_objektu\" value=\"".intval($data["id_komplu"])."\" >
                        <input type=\"submit\" name=\"odeslano_form\" value=\"R.F.\">
                        </form>\n
                        </td>\n";

            //sem odendat
            if ($co == 3) {

                if ($this->allowedUnassignFromVlastnik === true) {
                    $output .= "<td colspan=\"4\" ><a href=\"vlastnici2-obj-erase.php?id_komplu=".$data["id_komplu"]."\">Odendat</a> </td> \n";
                } else {
                    $output .= "<td colspan=\"4\" style=\"font-size: 10px; font-family: arial; color: gray; \">
                                    <div style=\"text-align: center; \">odendat</div> </td> \n";
                }

            }
            //opticky rezim
            elseif($co == 4) {

                if ($this->allowedUnassignFromVlastnik === true) {
                    $output .= "<td colspan=\"\" ><a href=\"vlastnici2-obj-erase.php?id_komplu=".$data["id_komplu"]."\">Odendat</a> </td> \n";
                } else {
                    $output .= "<td colspan=\"\" style=\"font-size: 10px; font-family: arial; color: gray; \">
                                    <div style=\"text-align: center; \">odendat</div> </td> \n";
                }
            }

            $output .= "</span>";
            // konec druhyho radku
            $output .= "</tr> \n";

            $verejna = 0;
            $garant = 0;

        } // konec while

        return $output;
    }

    public function checkmac($mac)
    {
        if (filter_var($mac, FILTER_VALIDATE_MAC) == false) {
            $this->action_fail = "true";
            $this->p_bs_alerts["MAC adresa (".$mac.") není ve správném formátu! </br>(Správný formát je: 00:00:64:65:73:74)"] = "danger";
        }

        //konec funkce check-mac
    }

    private function checkSikanaCas($sikanacas)
    {
        $sikanacas = intval($sikanacas);

        if(($sikanacas > 9) or ($sikanacas < 1)) {
            $this->action_fail = "true";
            $this->p_bs_alerts["Do pole \"Šikana - počet dní\" je třeba vyplnit číslo 1 až 9."] = "danger";
        }
    } //end of function checkSikanaCas

    private function checkSikanaText($sikanatext)
    {
        if((strlen($sikanatext) > 150)) {
            $this->action_fail = "true";
            $this->p_bs_alerts["Do pole \"Šikana - text\" je možno zadat max. 150 znaků. (aktuálně: ".strlen($sikanatext).")"] = "danger";
        }
    } //end of function checkSikanaText

    private function checkip($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) == false) {
            $this->action_fail = "true";
            $this->p_bs_alerts["IP adresa (".$ip.") není ve správném formátu!"] = "danger";
        }
    } //konec funkce check-ip

    private function checkcislo($cislo)
    {
        $rra_check = preg_match('/^([[:digit:]]+)$/', $cislo);

        if (!($rra_check)) {
            $this->action_fail = "true";
            $this->p_bs_alerts["Zadaný číselný údaj(e) ( ".$cislo." ) není ve  správném formátu!"] = "danger";
        }
    } //konec funkce check cislo

    private function checkdns($dns)
    {
        $dns_check = preg_match('/^([[:alnum:]]|\.|-)+$/', $dns);
        if (!($dns_check)) {
            $this->action_fail = "true";
            $this->p_bs_alerts["DNS záznam ( ".$dns." ) není ve správnem formátu!"] = "danger";
        }
    } // konec funkce check rra

    private function check_l2tp_cr($cr)
    {
        $cr_check = preg_match('/^([[:alnum:]])+$/', $cr);

        if(!($cr_check)) {
            $this->action_fail = "true";
            $this->p_bs_alerts["Tunel. login/heslo ( ".$cr." ) není ve správnem formátu!"] = "danger";
        }

        if((strlen($cr) <> 4)) {
            $this->action_fail = "true";
            $this->p_bs_alerts["Tunel. login/heslo ( ".$cr." ) musí mít 4 znaky!"] = "danger";
        }
    } //konec funkce check_l2tp_cr
}

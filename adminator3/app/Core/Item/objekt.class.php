<?php

namespace App\Core;

use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager as DB;

class objekt extends adminator
{
    public $container;

    public $conn_pgsql;
    public $conn_mysql;

    public $logger;

    public $validator;

    // protected $sentinel;

    public $loggedUserEmail;

    public $adminator; // handler for instance of adminator class

    public ?string $userIdentityUsername = null;

    public $dns_find;

    public $ip_find;

    public $mod_vypisu;

    public $es;

    public $razeni;

    public $list;

    public $dotaz_source;

    public $listErrors;

    public $csrf_html;

    public $listAllowedActionUpdate = false;

    public $listAllowedActionErase = false;

    public $listAllowedActionGarant = false;

    public $nod_find;

    public $sql_nod;

    public $update_id;
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

    public $insertedId;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->validator = $container->get('validator');
        $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');
        $this->logger = $container->get('logger');
        // $this->sentinel = $this->container->get('sentinel');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        // parent::__construct(
        //     $this->conn_mysql,
        //     $this->smarty,
        //     $this->logger,
        //     null,
        //     $this->pdoMysql,
        //     $this->settings,
        //     $this->conn_pgsql
        // );

        $this->loggedUserEmail = $this->userIdentityUsername;
        // $this->userIdentityUsername = $this->sentinel->getUser()->email;

        // $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": current identity: ".var_export($this->userIdentityUsername, true));
    }

    public function listGetOrderItems()
    {
        $output = "";

        $output .= "\n<tr>\n";
        $output .= '<td colspan="1">';

        $output .= "\n\n <input type=\"radio\" ";
        if (($_GET["razeni"] == 1)) {
            $output .= " checked ";
        }
        $output .= "name=\"razeni\" value=\"1\" onClick=\"form1.submit();\" > ";
        $output .= "<img src=\"img2/ses.png\" alt=\"ses\" width=\"15px\" height=\"10px\" >";

        $output .= " | <input type=\"radio\" ";
        if (($_GET["razeni"] == 2)) {
            $output .= " checked ";
        }
        $output .= " name=\"razeni\" value=\"2\" onClick=\"form1.submit();\"> \n";
        $output .= "<img src=\"img2/vzes.png\" alt=\"vzes\" width=\"15px\" height=\"10px\" >";

        $output .= '</td>
                    <td colspan="3">';

        $output .= "<input type=\"radio\" ";
        if (($_GET["razeni"] == 3)) {
            $output .= " checked ";
        }
        $output .= "name=\"razeni\" value=\"3\" onClick=\"form1.submit();\" > ";
        $output .= "<img src=\"img2/ses.png\" alt=\"ses\" width=\"15px\" height=\"10px\" >";

        $output .= " | <input type=\"radio\" ";
        if (($_GET["razeni"] == 4)) {
            $output .= " checked ";
        }
        $output .= " name=\"razeni\" value=\"4\" onClick=\"form1.submit();\"> \n";
        $output .= "<img src=\"img2/vzes.png\" alt=\"vzes\" width=\"15px\" height=\"10px\" >";

        $output .= '</td>
                    <td>';

        $output .= "<input type=\"radio\" ";
        if (($_GET["razeni"] == 9)) {
            $output .= " checked ";
        }
        $output .= "name=\"razeni\" value=\"9\" onClick=\"form1.submit();\" > ";
        $output .= "<img src=\"img2/ses.png\" alt=\"ses\" width=\"15px\" height=\"10px\" >";

        $output .= " | <input type=\"radio\" ";
        if (($_GET["razeni"] == 10)) {
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
        $mod_vypisu = $_GET["mod_vypisu"];

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

        $es = $_GET["es"];

        if((strlen($es) > 0)) {
            if(!(preg_match('/^([[:digit:]])+$/', $es))) {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v poli \"Sekundární hledání\". </div>";
            }
            $this->es = $es;
        }

        $razeni = $_GET["razeni"];

        if((strlen($razeni) > 0)) {
            if(!(preg_match('/^([[:digit:]])+$/', $razeni))) {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v promenné \"razeni\". </div>";
            }
            $this->razeni = $razeni;
        }

        $list = $_GET["list"];

        if((strlen($list) > 0)) {
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

        list($se, $order) = \objekt_a2::select($this->es, $this->razeni);

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

    public function listGetBodyContent()
    {
        $output = "";
        $exportLink = "";
        $error = "";

        $objekt_a2 = new \objekt_a2();
        $objekt_a2->echo = false;
        $objekt_a2->conn_mysql = $this->conn_mysql;
        $objekt_a2->conn_pgsql = $this->container->get('connPgsql');
        $objekt_a2->csrf_html = $this->csrf_html;
        $objekt_a2->mod_vypisu = $this->mod_vypisu;

        // checking levels for update/erase/..
        if ($this->checkLevel(29) === true) {
            $this->listAllowedActionUpdate = true;
        }
        if ($this->checkLevel(33) === true) {
            $this->listAllowedActionErase = true;
        }
        if ($this->checkLevel(34) === true) {
            $this->listAllowedActionGarant = true;
        }
        if ($this->checkLevel(59) === true) {
            $export_povolen = true;
        }

        $objekt_a2->listAllowedActionUpdate = $this->listAllowedActionUpdate;
        $objekt_a2->listAllowedActionErase = $this->listAllowedActionErase;
        // $objekt_a2->listAllowedActionGarant = $this->listAllowedActionGarant;

        if ($export_povolen === true) {
            $exportLink = $objekt_a2->export_vypis_odkaz();
        }

        // prepare vars
        //
        $prepVarsRs = $this->listPrepareVars();
        if($prepVarsRs === false) {
            return array("", $this->listErrors);
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

        $output .= $objekt_a2->vypis_tab(1);

        $output .= $objekt_a2->vypis_tab_first_rows($this->mod_vypisu);

        list($output_razeni) = $this->listGetOrderItems();
        $output .= $output_razeni;

        $output .=  "</form>";

        $generateSqlRes = $this->listGenerateSql();
        if($generateSqlRes === false) {
            return array("", '<div class="alert alert-danger" role="alert">Chyba! Nepodarilo se vygenerovat SQL dotaz.</div>');
        }
        // paging
        //
        $poradek = "es=".$this->es."&najdi=".$najdi."&odeslano=".$_GET['odeslano']."&dns_find=".$this->dns_find."&ip_find=".$this->ip_find."&razeni=".$_get['razeni'];
        $poradek .= "&mod_vypisu=".$this->mod_vypisu;

        //vytvoreni objektu
        $listovani = new \c_listing_objekty("/objekty?".$poradek."&menu=1", 30, $this->list, "<center><div class=\"text-listing2\">\n", "</div></center>\n", $this->dotaz_source);
        $listovani->echo = false;

        if(($this->list == "") || ($this->list == "1")) {
            $bude_chybet = 0;
        } //pokud není list zadán nebo je první bude ve výběru sql dotazem chybet 0 záznamů
        else {
            $bude_chybet = (($this->list - 1) * $listovani->interval);
        }   //jinak jich bude chybet podle závislosti na listu a intervalu

        //  $interval=$listovani->interval;

        if(intval($listovani->interval) > 0 and intval($bude_chybet) > 0) {
            $this->dotaz_source = $this->dotaz_source . " LIMIT ". intval($listovani->interval)." OFFSET ".intval($bude_chybet)." ";
        }

        $output .= $listovani->listInterval();

        $this->logger->debug(
            "objekt\listGetBodyContent: dump vars: "
                                ."dotaz_source: " . var_export($this->dotaz_source, true)
                                . ", sql: " . var_export($sql, true)
                                . ", co: " . var_export($co, true)
        );

        $output .= $objekt_a2->vypis($sql, $co, 0, $this->dotaz_source);

        $output .= $objekt_a2->vypis_tab(2);

        // listing
        $output .= $listovani->listInterval();

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

    public function actionWifi()
    {
        $output = "";

        if (($this->update_id > 0)) {
            $update_status = 1;
        }

        if(($update_status == 1 and !(isset($this->send)))) {
            //rezim upravy
            $dotaz_upd = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE id_komplu='".intval($this->update_id)."' ");
            $radku_upd = pg_num_rows($dotaz_upd);

            if ($radku_upd == 0) {
                $output .= "Chyba! Požadovaná data nelze načíst! ";
            } else {
                while($data = pg_fetch_array($dotaz_upd)):
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

                endwhile;

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

        \objektypridani::generujdata($this->form_selected_nod, $this->form_typ_ip, $this->form_dns, $this->conn_mysql);

        if((strlen($this->form_ip) > 0)) {
            \objektypridani::checkip($this->form_ip);
        }

        if((strlen($this->form_dns) > 0)) {
            \objektypridani::checkdns($this->form_dns);
        }
        if((strlen($this->form_mac) > 0)) {
            \objektypridani::checkmac($this->form_mac);
        }
        if((strlen($this->form_sikana_cas) > 0)) {
            \objektypridani::checkcislo($this->form_sikana_cas);
        }
        if((strlen($this->form_selected_nod) > 0)) {
            \objektypridani::checkcislo($this->form_selected_nod);
        }

        if((strlen($this->form_client_ap_ip) > 0)) {
            \objektypridani::checkip($this->form_client_ap_ip);
        }

        if($this->form_sikana_status == 2) {

            \objektypridani::checkSikanaCas($this->form_sikana_cas);

            \objektypridani::checkSikanaText($this->form_sikana_text);

        }


        if($this->form_typ_ip == 4) {
            if((strlen($tunnel_user) > 0)) {
                \objektypridani::check_l2tp_cr($tunnel_user);
            }
            if((strlen($tunnel_pass) > 0)) {
                \objektypridani::check_l2tp_cr($tunnel_pass);
            }
        }

        // jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
        if((($this->form_dns != "") and ($this->form_ip != "")) and ($this->form_selected_nod > 0) and (($this->form_id_tarifu >= 0))) :

            if(($update_status != 1)) {
                $this->ip_find = $this->form_ip."/32";

                //zjisti jestli neni duplicitni dns, ip
                $MSQ_DNS = pg_query($this->conn_pgsql, "SELECT ip FROM objekty WHERE dns_jmeno LIKE '$this->form_dns' ");
                $MSQ_IP = pg_query($this->conn_pgsql, "SELECT ip FROM objekty WHERE ip <<= '$this->ip_find' ");

                if (pg_num_rows($MSQ_DNS) > 0) {
                    $error .= "<h4>Dns záznam ( ".$this->form_dns." ) již existuje!!!</h4>";
                    $fail = "true";
                }
                if (pg_num_rows($MSQ_IP) > 0) {
                    $error .= "<h4>IP adresa ( ".$this->form_ip." ) již existuje!!!</h4>";
                    $fail = "true";
                }

                //duplicitni tunnel_pass/user
                if($this->form_typ_ip == 4) {
                    $MSQ_TUNNEL_USER = pg_query($this->conn_pgsql, "SELECT tunnel_user FROM objekty WHERE tunnel_user LIKE '$tunnel_user' ");
                    $MSQ_TUNNEL_PASS = pg_query($this->conn_pgsql, "SELECT tunnel_pass FROM objekty WHERE tunnel_pass LIKE '$tunnel_pass' ");

                    if(pg_num_rows($MSQ_TUNNEL_USER) > 0) {
                        $error .= "<h4>Login k tunelovacímu serveru (".$tunnel_user.") již existuje!!!</h4>";
                        $fail = "true";
                    }
                    if(pg_num_rows($MSQ_TUNNEL_PASS) > 0) {
                        $error .= "<h4>Heslo k tunelovacímu serveru (".$tunnel_pass.") již existuje!!!</h4>";
                        $fail = "true";
                    }
                }

            }

            // check v modu uprava
            if (($update_status == 1 and (isset($this->odeslano)))) {
                $this->ip_find = $this->form_ip."/32";

                //zjisti jestli neni duplicitni dns, ip
                $MSQ_DNS2 = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE ( dns_jmeno LIKE '$this->form_dns' AND id_komplu != '".intval($this->update_id)."' ) ");
                $MSQ_IP2 = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE ( ip <<= '$this->ip_find' AND id_komplu != '".intval($this->update_id)."' ) ");

                if(pg_num_rows($MSQ_DNS2) > 0) {
                    $error .= "<h4>Dns záznam ( ".$this->form_dns." ) již existuje!!!</h4>";
                    $fail = "true";
                }
                if(pg_num_rows($MSQ_IP2) > 0) {
                    $error .= "<h4>IP adresa ( ".$this->form_ip." ) již existuje!!!</h4>";
                    $fail = "true";
                }

                //duplicitni tunnel_pass/user
                if($this->form_typ_ip == 4) {
                    $MSQ_TUNNEL_USER = pg_query($this->conn_pgsql, "SELECT tunnel_user FROM objekty WHERE ( tunnel_user LIKE '$tunnel_user' AND id_komplu != '".intval($this->update_id)."' ) ");
                    $MSQ_TUNNEL_PASS = pg_query($this->conn_pgsql, "SELECT tunnel_pass FROM objekty WHERE ( tunnel_pass LIKE '$tunnel_pass' AND id_komplu != '".intval($this->update_id)."' ) ");

                    if(pg_num_rows($MSQ_TUNNEL_USER) > 0) {
                        $error .= "<h4>Login k tunelovacímu serveru (".$tunnel_user.") již existuje!!!</h4>";
                        $fail = "true";
                    }
                    if(pg_num_rows($MSQ_TUNNEL_PASS) > 0) {
                        $error .= "<h4>Heslo k tunelovacímu serveru (".$tunnel_pass.") již existuje!!!</h4>";
                        $fail = "true";
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
                $info .= "<div style=\"color: orange; \" ><h4>UPOZORNĚNÍ: Tento přípojný bod je vytížen, mohou se vyskytovat problémy. </h4></div>";
            } elseif (($stav_nodu == 3) and ($update_status == 1)) {
                $info .= "<div style=\"color: orange; \"><h4>UPOZORNĚNÍ: Tento přípojný bod je přetížen. </h4></div>";
            } elseif ($stav_nodu == 3) {
                $fail = "true";
                $error .= "<div style=\"color: red; \" ><h4>Tento přípojný bod je přetížen, vyberte prosím jiný. </h4></div>";
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
                    $fail = "true";
                    $error .= "<div class=\"objekty-add-mac\" >Klient má pozastavené fakturace. Před povolením internetu je potřeba změnit u vlastníka pole \"Pozastavené fakturace\". </div>";
                }
            }

        } // konec if jestli id_cloveka > 1 and update == 1

        //checkem jestli se macklo na tlacitko "OK" :)
        if(preg_match("/^OK$/", $this->odeslano)) {
            $output .= "";
        } else {
            $fail = "true";
            $error .= "<div class=\"objekty-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>";
        }

        //ulozeni
        if (!(isset($fail))) {
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

            if($update_status == "1") {
                // rezim upravy

                if ($this->checkLevel(29) === false) {
                    $output .= "<br><div style=\"color: red; font-size: 18px; \" >Objekty nelze upravovat, není dostatečné oprávnění. </div><br>";
                    return $output;
                }

                //prvne stavajici data docasne ulozime
                $pole2 .= "<b>akce: uprava objektu; </b><br>";

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
                $this->actionArchivZmenWifiDiff($vysledek_write);

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

                    $output .= "<div style=\"padding-left: 5px; \">sql: ".$sql."</div>";
                }

                // pridame to do archivu zmen
                // require("objekty-add-inc-archiv-wifi-add.php");
                $this->addedDataArray = $obj_add;
                $this->actionArchivZmenWifi($vysledek_write);

                $writed = "true";
            } // konec else - rezim pridani

        } else {
        } // konec else ( !(isset(fail) ), muji tu musi bejt, pac jinak nefunguje nadrazeny if-elseif

        elseif(isset($this->send)) :
            $error = "<h4>Chybí povinné údaje !!! (aktuálně jsou povinné:  dns, ip adresa, přípojný bod, tarif) </H4>";
        endif;

        if ($update_status == 1) {
            $output .= '<h3 align="center">Úprava objektu</h3>';
        } else {
            $output .= '<h3 align="center">Přidání nového objektu</h3>';
        }

        // jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
        if((isset($error)) or (!isset($this->send))) :
            $output .= $error;

            $output .= $info;

            // vlozeni vlastniho formu
            // require("objekty-add-inc.php");
            $output .= $this->actionFormWifi();

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

    public function actionFiber()
    {
        $output = "";

        if (($this->update_id > 0)) {
            $update_status = 1;
        }

        //nacitani predchozich dat ...
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
            \objektypridani::checkip($this->form_ip);
        }

        if((strlen($this->form_dns) > 0)) {
            \objektypridani::checkdns($this->form_dns);
        }
        if((strlen($this->form_mac) > 0)) {
            \objektypridani::checkmac($this->form_mac);
        }

        if((strlen($this->form_sikana_cas) > 0)) {
            \objektypridani::checkcislo($this->form_sikana_cas);
        }
        //if( (strlen($this->form_selected_nod) > 0 ) ){ \objektypridani::checkcislo($this->form_selected_nod); }

        // jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
        if((($this->form_dns != "") and ($this->form_ip != "")) and ($this->form_selected_nod > 0) and (($this->form_id_tarifu >= 0)) and ($this->form_mac != "")) :

            //kontrola dulplicitnich udaju
            if (($update_status != 1)) {
                $ip = $this->form_ip."/32";

                //zjisti jestli neni duplicitni dns, ip
                $MSQ_DNS = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE dns_jmeno LIKE '" . $this->form_dns ."' ");
                $MSQ_IP = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE ip <<= '" . $ip ."' ");

                if (pg_num_rows($MSQ_DNS) > 0) {
                    $error .= "<h4>Dns záznam ( ".$this->form_dns." ) již existuje!!!</h4>";
                    $fail = "true";
                }
                if (pg_num_rows($MSQ_IP) > 0) {
                    $error .= "<h4>IP adresa ( ".$this->form_ip." ) již existuje!!!</h4>";
                    $fail = "true";
                }
            }

            // check v modu uprava
            if(($update_status == 1 and (isset($this->odeslano)))) {
                $ip = $this->form_ip."/32";

                //zjisti jestli neni duplicitni dns, ip
                $MSQ_DNS2 = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE ( dns_jmeno LIKE '$this->form_dns' AND id_komplu != '$this->update_id' ) ");
                $MSQ_IP2 = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE ( ip <<= '$ip' AND id_komplu != '$this->update_id' ) ");

                if(pg_num_rows($MSQ_DNS2) > 0) {
                    $error .= "<h4>Dns záznam ( ".$this->form_dns." ) již existuje!!!</h4>";
                    $fail = "true";
                }
                if(pg_num_rows($MSQ_IP2) > 0) {
                    $error .= "<h4>IP adresa ( ".$this->form_ip." ) již existuje!!!</h4>";
                    $fail = "true";
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
                $info .= "<div style=\"color: orange; \" ><h4>UPOZORNĚNÍ: Tento přípojný bod je vytížen, mohou se vyskytovat problémy. </h4></div>";
            } elseif (($stav_nodu == 3) and ($update_status == 1)) {
                $info .= "<div style=\"color: orange; \"><h4>UPOZORNĚNÍ: Tento přípojný bod je přetížen. </h4></div>";
            } elseif ($stav_nodu == 3) {
                $fail = "true";
                $error .= "<div style=\"color: red; \" ><h4>Tento přípojný bod je přetížen, vyberte prosím jiný. </h4></div>";
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
                    $fail = "true";
                    $error .= "<div class=\"objekty-add-mac\" >Klient má pozastavené fakturace. Před povolením internetu je potřeba změnit u vlastníka fakturační skupinu. </div>";
                }

            }

        } // konec if jestli id_cloveka > 1 and update == 1

        //checkem jestli se macklo na tlacitko "OK" :)
        if(preg_match("/^OK*/", $this->odeslano)) {
            $output .= "";
        } else {
            $fail = "true";
            $error .= "<div class=\"objekty-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>";
        }

        //ukladani udaju ...
        if(!(isset($fail))) {
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

            if($update_status == "1") {

                if ($this->checkLevel(29) === false) {
                    $output .= "<br><div style=\"color: red; font-size: 18px; \" >Objekty nelze upravovat, není dostatečné oprávnění. </div><br>";
                    return $output;
                }

                // rezim upravy

                //prvne stavajici data docasne ulozime
                $pole2 .= "<b>akce: uprava objektu; </b><br>";

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

        elseif (isset($this->send)) :
            $error = "<h4>Chybí povinné údaje !!! (aktuálně jsou povinné:  dns, ip adresa, přípojný bod, tarif) </H4>";
        endif;

        if ($update_status == 1) {
            $output .= '<h3 align="center">Úprava objektu</h3>';
        } else {
            $output .= '<h3 align="center">Přidání nového objektu</h3>';
        }

        // jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
        if ((isset($error)) or (!isset($this->send))) :
            $output .= $error;

            $output .= $info;

            // vlozeni vlastniho formu
            $output .= $this->actionFormFiber();

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

    public function generujDataFiber()
    {

        if($this->form_selected_nod < 1) {
            echo "";
            return false;
        }

        // skusime ip vygenerovat
        $vysl_nod = $this->conn_mysql->query("SELECT ip_rozsah FROM nod_list WHERE id = '".intval($this->form_selected_nod)."'");
        $radku_nod = $vysl_nod->num_rows;

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
            //if( ( strlen($ip) < 1 ) ){ $ip = "E_3"; }
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

        $check_ip = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE ip <<= '$sub_rozsah/26' ORDER BY ip ASC");
        $check_ip_radku = pg_num_rows($check_ip);

        //echo "subrozsah: ".$sub_rozsah." xxx";

        if($check_ip_radku == 0) { // v rozsahu zadna ip, takze generujem prvni..
            $gen_ip = $r_a.".".$r_b.".".$r_c.".".$r_d;
            //$gen_ip = "vole...";
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
                $this->form_ip_error = "1";
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

        //return true;
    } //konec funkce generujdata

    public function actionArchivZmenWifiDiff($vysledek_write)
    {
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

        $pole2 .= "".$pole3;

        $add = $this->conn_mysql->query(
            "INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) "
                                        . "VALUES ('" . $pole2 . "','" . $this->loggedUserEmail . "','" . $vysledek_write . "')"
        );

        //
        //pro osvezovani
        //

        // TODO: fix automatic restarts

        // //zjistit, krz kterého reinharda jde objekt
        // $reinhard_id = Aglobal::find_reinhard($update_id);

        // //zmena sikany
        // if( ereg(".*změna.*Šikana.*z.*", $pole3) )
        // {
        // if($reinhard_id == 177){ Aglobal::work_handler("1"); } //reinhard-3 (ros) - restrictions (net-n/sikana)
        // elseif($reinhard_id == 1){ Aglobal::work_handler("2"); } //reinhard-wifi (ros) - restrictions (net-n/sikana)
        // elseif($reinhard_id == 236){ Aglobal::work_handler("24"); } //reinhard-5 (ros) - restrictions (net-n/sikana)
        // else{

        //     //nenalezet pozadovany reinhard, takze osvezime vsechny

        //     Aglobal::work_handler("1"); //reinhard-3 (ros) - restrictions (net-n/sikana)
        //     Aglobal::work_handler("2"); //reinhard-wifi (ros) - restrictions (net-n/sikana)
        //     Aglobal::work_handler("24"); //reinhard-5 (ros) - restrictions (net-n/sikana)

        // }

        // }

        // //zmena NetN
        // if( ereg(".*změna.*Povolen.*Inet.*z.*", $pole3) )
        // {
        // if($reinhard_id == 177){ Aglobal::work_handler("1"); } //reinhard-3 (ros) - restrictions (net-n/sikana)
        // elseif($reinhard_id == 1){ Aglobal::work_handler("2"); } //reinhard-wifi (ros) - restrictions (net-n/sikana)
        // elseif($reinhard_id == 236){ Aglobal::work_handler("24"); } //reinhard-5 (ros) - restrictions (net-n/sikana)
        // else{

        //     //nenalezet pozadovany reinhard, takze osvezime vsechny

        //     Aglobal::work_handler("1"); //reinhard-3 (ros) - restrictions (net-n/sikana)
        //     Aglobal::work_handler("2"); //reinhard-wifi (ros) - restrictions (net-n/sikana)
        //     Aglobal::work_handler("24"); //reinhard-5 (ros) - restrictions (net-n/sikana)

        // }
        // }

        // //zmena IP adresy pokud je aktivni Sikana ci NetN
        // if( (
        //     ereg(".*změna.*IP.*adresy.*z.*", $pole3)
        //     and
        //     (
        //     ($this->origDataArray["sikana_status"] == "a")
        // or
        // ($this->origDataArray["dov_net"] == "n")
        //     )
        //     )
        // )
        // {
        //     //radsi vynutit restart net-n/sikany u vseho

        //     Aglobal::work_handler("1"); //reinhard-3 (ros) - restrictions (net-n/sikana)
        //     Aglobal::work_handler("2"); //reinhard-wifi (ros) - restrictions (net-n/sikana)
        //     Aglobal::work_handler("3"); //reinhard-fiber (linux) - iptables (net-n/sikana)
        //     Aglobal::work_handler("24"); //reinhard-5 (ros) - restrictions (net-n/sikana)

        //     Aglobal::work_handler("5");  //reinhard-fiber - shaper
        //     Aglobal::work_handler("13"); //reinhard-wifi (ros) - shaper (client's tariffs)
        //     Aglobal::work_handler("20"); //reinhard-3 (ros) - shaper (client's tariffs)
        //     Aglobal::work_handler("23"); //reinhard-5 (ros) - shaper (client's tariffs)

        //     Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart

        // }
        // //zmena IP adresy bez aktivovaného omezení
        // elseif( ereg(".*změna.*IP.*adresy.*z.*", $pole3) )
        // {
        //     Aglobal::work_handler("5");  //reinhard-fiber - shaper
        //     Aglobal::work_handler("13"); //reinhard-wifi (ros) - shaper (client's tariffs)
        //     Aglobal::work_handler("20"); //reinhard-3 (ros) - shaper (client's tariffs)
        //     Aglobal::work_handler("23"); //reinhard-5 (ros) - shaper (client's tariffs)

        //     Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart

        // }

        // //zmena linky -- shaper / filtrace
        // if( ereg(".*změna.*pole.*id_tarifu.*", $pole3)
        //     or
        //     ereg(".*změna.*Tarifu.*", $pole3)
        // )
        // {
        //     if($reinhard_id == 177){ Aglobal::work_handler("20"); } //reinhard-3 (ros) - shaper (client's tariffs)
        //     elseif($reinhard_id == 1){ Aglobal::work_handler("13"); } //reinhard-wifi (ros) - shaper (client's tariffs)
        //     elseif($reinhard_id == 236){ Aglobal::work_handler("23"); } //reinhard-5 (ros) - shaper (client's tariffs)
        //     else
        //     {
        //     Aglobal::work_handler("13"); //reinhard-wifi (ros) - shaper (client's tariffs)
        //     Aglobal::work_handler("20"); //reinhard-3 (ros) - shaper (client's tariffs)
        //     Aglobal::work_handler("23"); //reinhard-5 (ros) - shaper (client's tariffs)
        //     }

        //     // filtrace asi neni treba
        //     // Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart

        // }

        // //zmena tunneling_ip ci tunel záznamů
        // // --> radius artemis
        // // zde dodelat zmenu IP adresy, pokud tunelovana verejka
        // if(
        // ereg(".*změna.*pole.*tunnelling_ip.*", $pole3)
        // or
        // ereg(".*změna.*pole.*tunnel_user.*", $pole3)
        // or
        // ereg(".*změna.*pole.*tunnel_pass.*", $pole3)
        // )
        // {
        //     Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
        // }

        // //zmena MAC adresy .. zatim se nepouziva u wifi

        // //zmena DNS záznamu, asi jen u veřejných IP adresa
        // // --> restart DNS auth. serveru
        // if( ereg(".*změna.*pole.*dns_jmeno.*", $pole3) )
        // {
        //     Aglobal::work_handler("9"); //erik - dns-restart
        //     Aglobal::work_handler("10"); //trinity - dns restart
        //     Aglobal::work_handler("11"); //artemis - dns restart
        //     Aglobal::work_handler("12"); //c.ns.simelon.net - dns.restart
        // }

        // if( ereg(".*změna.*pole.*client_ap_ip.*", $pole3) ){

        //     Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart

        //     if($reinhard_id == 177){ Aglobal::work_handler("20"); } //reinhard-3 (ros) - shaper (client's tariffs)
        //     elseif($reinhard_id == 1){ Aglobal::work_handler("13"); } //reinhard-wifi (ros) - shaper (client's tariffs)
        //     elseif($reinhard_id == 236){ Aglobal::work_handler("23"); } //reinhard-5 (ros) - shaper (client's tariffs)
        //     else
        //     {
        //     Aglobal::work_handler("13"); //reinhard-wifi (ros) - shaper (client's tariffs)
        //     Aglobal::work_handler("20"); //reinhard-3 (ros) - shaper (client's tariffs)
        //     Aglobal::work_handler("23"); //reinhard-5 (ros) - shaper (client's tariffs)
        //     }
        // }

        //nic vic mi nenapada :-)
    }

    public function actionArchivZmenWifi($vysledek_write)
    {
        //zjistit, krz kterého reinharda jde objekt
        // $inserted_id = \Aglobal::pg_last_inserted_id($this->conn_pgsql, "objekty");

        // pridame to do archivu zmen
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

        //automaticke osvezovani/restarty
        // TODO: fix automatic restarts
        // if( $this->form_typ_ip == 4 )
        // {
        //     //L2TP verejka
        //     Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
        // }

        // Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart

        // $reinhard_id = Aglobal::find_reinhard($this->insertedId);

        // //zde dodat if zda-li je NetN ci SikanaA
        // if( (preg_match("/.*<b>\[dov_net\]<\/b> => n.*/", $pole) == 1)
        //         or (preg_match("/.*<b>\[sikana_status\]<\/b> => a.*/", $pole) == 1) ){


        //     if($reinhard_id == 177){ Aglobal::work_handler("1"); } //reinhard-3 (ros) - restrictions (net-n/sikana)
        //     elseif($reinhard_id == 1){ Aglobal::work_handler("2"); } //reinhard-wifi (ros) - restrictions (net-n/sikana)
        //     elseif($reinhard_id == 236){ Aglobal::work_handler("24"); } //reinhard-5 (ros) - restrictions (net-n/sikana)
        //     else{

        //         //nenalezet pozadovany reinhard, takze osvezime vsechny

        //         Aglobal::work_handler("1"); //reinhard-3 (ros) - restrictions (net-n/sikana)
        //         Aglobal::work_handler("2"); //reinhard-wifi (ros) - restrictions (net-n/sikana)
        //         Aglobal::work_handler("24"); //reinhard-5 (ros) - restrictions (net-n/sikana)

        //     } //end of else - if reinhard_id

        // }

        // if($reinhard_id == 177){ Aglobal::work_handler("20"); } //reinhard-3 (ros) - shaper (client's tariffs)
        // elseif($reinhard_id == 1){ Aglobal::work_handler("13"); } //reinhard-wifi (ros) - shaper (client's tariffs)
        // elseif($reinhard_id == 236){ Aglobal::work_handler("23"); } //reinhard-5 (ros) - shaper (client's tariffs)
        // else
        // {
        //     Aglobal::work_handler("13"); //reinhard-wifi (ros) - shaper (client's tariffs)
        //     Aglobal::work_handler("20"); //reinhard-3 (ros) - shaper (client's tariffs)
        //     Aglobal::work_handler("23"); //reinhard-5 (ros) - shaper (client's tariffs)
        // }
    }

    private function actionArchivZmenFiberDiff($vysledek_write)
    {
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

        $pole2 .= "".$pole3;

        $add = $this->conn_mysql->query(
            "INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
                "('".$this->conn_mysql->real_escape_string($pole2)."','".
                $this->conn_mysql->real_escape_string($this->loggedUserEmail)."','".
            $this->conn_mysql->real_escape_string($vysledek_write)."') "
        );

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

        //ted automaticky pridavani restartu

        //asi vše :-)
        // \Aglobal::work_handler("3"); //rh-fiber - iptables
        // \Aglobal::work_handler("4"); //rh-fiber - radius
        // \Aglobal::work_handler("5"); //rh-fiber - shaper
        // \Aglobal::work_handler("6"); //reinhard-fiber - mikrotik.dhcp.leases.erase
        // \Aglobal::work_handler("7"); //trinity - sw.h3c.vlan.set.pl update

        // \Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
    }
}

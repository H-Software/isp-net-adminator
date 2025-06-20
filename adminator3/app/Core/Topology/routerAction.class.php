<?php

namespace App\Core\Topology;

use App\Core\adminator;
use Psr\Container\ContainerInterface;
use Exception;

class RouterAction extends adminator
{
    public \mysqli|\PDO $conn_mysql;

    // public \Smarty $smarty;

    public \Monolog\Logger $logger;

    // protected $settings;

    protected $sentinel;

    protected $work;

    protected $loggedUserEmail;

    public $csrf_html;

    private $error_messages;

    private string|null $form_odeslat = null;

    private int $form_error = 0;

    private $form_update_id;

    private $form_nod_find;
    private $form_selected_nod;
    private $form_nazev;
    private $form_ip_adresa;
    private $form_parent_router;
    private $form_monitoring;
    private $form_monitoring_cat;
    private $form_alarm;
    private $form_filtrace;
    private $form_mac;
    private $form_poznamka;

    /**
     * {@inheritdoc}
     */
    public array $p_bs_alerts = [];

    public $origDataArray;

    public function __construct(ContainerInterface $container)
    {
        $this->conn_mysql = $container->get('connMysql');
        // $this->conn_pgsql = $container->get('connPgsql');
        $this->logger = $container->get('logger');
        $this->sentinel = $container->get('sentinel');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->loggedUserEmail = $this->sentinel->getUser()->email;

        $this->work = new \App\Core\work($container);
    }

    public function action(): array
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $output = "";
        $http_status_code = 200;

        $this->loadFormData();

        if ($this->form_odeslat == "OK") { // zda je odesláno
            $this->checkFormData();
        }

        if (($this->form_odeslat == "OK") and ($this->form_error != 1)) {
            // proces ukladani ..

            list($content, $rs_db) = $this->saveDataIntoDatabase();
            $output .= $content;

            $output .= $this->showResults();

            // if save data failed, return only error msq and corresponding status code
            if ($rs_db == false) {
                $output = $this->error_messages . $output;
                return [$output, 500];
            }

        } else {
            // nechceme ukladat, tj. zobrazit form

            // pokud update, tak zjistit predchozi hodnoty
            // nacteni promennych, pokud se nedna o upravu a neodeslal sem form
            if ($this->form_update_id > 0 and ($this->form_odeslat != "OK")) {
                $rs = $this->loadPreviousData();

                // if load data failed, return only error msq and corresponding status code
                if ($rs === false) {
                    $output .= $this->error_messages;
                    return [$output, 500];
                }
            }

            //zobrazime formular
            list($content) = $this->showForm();
            $output .= $content;
        }

        return [$output, $http_status_code];
    }

    private function loadFormData(): void
    {
        $this->form_nod_find = $_POST["nod_find"];
        $this->form_odeslat = $_POST["odeslat"];

        $this->form_selected_nod = $_POST["selected_nod"];

        $this->form_nazev = $_POST["nazev"];
        $this->form_ip_adresa = $_POST["ip_adresa"];
        $this->form_parent_router = $_POST["parent_router"];
        $this->form_monitoring = $_POST["monitoring"];
        $this->form_monitoring_cat = $_POST["monitoring_cat"];
        $this->form_alarm = $_POST["alarm"];

        $this->form_filtrace = $_POST["filtrace"];
        $this->form_mac = $_POST["mac"];
        $this->form_monitoring_cat = $_POST["monitoring_cat"];
        $this->form_update_id = $_POST["update_id"];

        $this->form_poznamka = $_POST["poznamka"];
    }

    private function loadPreviousData(): bool
    {
        try {
            $dotaz_top = $this->conn_mysql->query("SELECT * FROM router_list WHERE id = '".intval($this->form_update_id)."' ");
            $dotaz_top_radku = $dotaz_top->num_rows;
        } catch (Exception $e) {
            $this->error_messages .= "<div style=\"color: red;\">"
                                    . "Chyba! Nelze načíst zdrojové hodnoty pro úpravu.</div>"
                                    . "<div style=\"color: red; \"> caught error: " . $e->getMessage() . "</div>"
            ;
            return false;
        }

        if ($dotaz_top_radku < 1) {
            $this->error_messages .= "<div style=\"color: red;\">"
                                     . "Chyba! Nelze načíst zdrojové hodnoty pro úpravu. (zero rows found in DB)"
                                     . "</div>"
            ;
            return false;
        } else {
            while ($data_top = $dotaz_top->fetch_array()):

                if ($this->form_nazev == "") {
                    $this->form_nazev = $data_top["nazev"];
                }
                if ($this->form_ip_adresa == "") {
                    $this->form_ip_adresa = $data_top["ip_adresa"];
                }
                if ($this->form_parent_router == "") {
                    $this->form_parent_router = $data_top["parent_router"];
                }
                if ($this->form_mac == "") {
                    $this->form_mac = $data_top["mac"];
                }
                if ($this->form_filtrace == "") {
                    $this->form_filtrace = $data_top["filtrace"];
                }
                if ($this->form_monitoring == "") {
                    $this->form_monitoring = $data_top["monitoring"];
                }
                if ($this->form_monitoring_cat == "") {
                    $this->form_monitoring_cat = $data_top["monitoring_cat"];
                }
                if ($this->form_alarm == "") {
                    $this->form_alarm = $data_top["alarm"];
                }
                if ($this->form_poznamka == "") {
                    $this->form_poznamka = $data_top["poznamka"];
                }
                if ($this->form_selected_nod == "") {
                    $this->form_selected_nod = $data_top["id_nodu"];
                }
            endwhile;
        }

        return true;
    }

    private function checkFormData(): void
    {

        if (($this->form_monitoring == 1)) {

            //monitoring potrebuje i monitoring kategorii
            if (($this->form_monitoring_cat == 0)) {
                $this->p_bs_alerts["Vyberte kategorii pro monitoring."] = "danger";
                $this->form_error = 1;
            }

            //test api a spravnosti konfigurace routeru
            $rs_test = adminator::test_router_for_monitoring($this->conn_mysql, $this->form_ip_adresa);

            if ($rs_test[0] == false) {
                $text = "Nelze uložit s parametrem \"<b>Monitoring - Ano</b>\", selhala kontrola nastavení či stavu routeru pro monitoring.";
                $text .= "<div style=\"color: grey;\" >výpis testu: <pre>".htmlspecialchars($rs_test[1])."</pre></div>";

                $this->p_bs_alerts[$text] = "danger";
                $this->form_error = 1;
            }
        } //end od if monitoring == 1

        //nadrazený router musí být vyplnen
        if (intval($this->form_parent_router) < 1) {
            $this->p_bs_alerts["Je třeba vyplnit pole \"Nadřazený router\" </br>(kvůli filtraci a QoSu na reinhardech)."] = "danger";
            $this->form_error = 1;
        }

        //kontrola IP adresy
        if ((strlen($this->form_ip_adresa) > 0)) {
            if (filter_var($this->form_ip_adresa, FILTER_VALIDATE_IP) == false) {
                $this->p_bs_alerts["IP adresa (".$this->form_ip_adresa.") není ve správném formátu !!!"] = "danger";
                $this->form_error = 1;
            }
        }

        //check dns nazvu
        if ((strlen($this->form_nazev) > 0)) {

            //kontrola správnosti zadání
            $dns_check = preg_match('/^([[:alnum:]]|\.|-)+$/', $this->form_nazev);

            if ($dns_check == false) {
                $this->p_bs_alerts["Název (".$this->form_nazev.") není ve správnem formátu."] = "danger";
                $this->form_error = 1;
            }

            //kontrola delky
            if ((strlen($this->form_nazev) > 40)) {
                $this->p_bs_alerts["DNS záznam (".$this->form_nazev.") je moc dlouhý! Maximální délka je 40 znaků."] = "danger";
                $this->form_error = 1;
            }
        }

        //kontrola mac adresy
        if ((strlen($this->form_mac) > 0)) {

            if (filter_var($this->form_mac, FILTER_VALIDATE_MAC) == false) {
                $this->p_bs_alerts["MAC adresa (".$this->form_mac.") není ve správném formátu!"] = "danger";
                $this->form_error = 1;
            }
        }

        //povinné údaje
        if ((strlen($this->form_nazev) == 0) or (strlen($this->form_ip_adresa) == 0) or (strlen($this->form_parent_router) == 0)) {
            $this->p_bs_alerts["Nejsou vyplněny všechny potřebné údaje. </br>(Název, IP adresa, Nadřazený router)"] = "danger";
            $this->form_error = 1;
        }
    }

    private function showForm(): array
    {
        $output = "";

        $output .= '<form method="POST" action="" name="form1">';

        $output .= $this->csrf_html;

        $output .= '<table border="0" width="100%" id="table2">
            <tr>
                <td width="200px"><label>Název: </label></td>
                <td><input type="text" name="nazev" size="30" value="'.$this->form_nazev.'"></td>
            </tr>

            <tr>
            <td><label>IP adresa : </label></td>
            <td><input type="text" name="ip_adresa" size="20" value="'.$this->form_ip_adresa.'" ></td>
            </tr>

            <tr>
            <td><label>Nadřazený router: </label></td>';

        $output .= "<td>";

        $output .= "<select name=\"parent_router\" size=\"1\" >";

        $dotaz_parent = $this->conn_mysql->query("SELECT * FROM router_list ORDER BY nazev");

        $output .= "<option value=\"0\" class=\"select-nevybrano\" > není zvoleno </option>";

        while ($data_parent = $dotaz_parent->fetch_array()) {
            $output .= "<option value=\"".$data_parent["id"]."\" ";

            if ($data_parent["id"] == $this->form_parent_router) {
                $output .= " selected ";
            }
            $output .= "> ".$data_parent["nazev"]." ( ".$data_parent["ip_adresa"]." ) </option>";
        }
        $output .= "</select></td>";

        $output .= "</tr>";

        $output .= "<tr><td><br></td></tr>";

        $output .= "<tr>";

        $output .= "<td><label>MAC: </label></td>";
        $output .= "<td><input type=\"text\" name=\"mac\" size=\"20\" maxlength=\"17\" value=\"".$this->form_mac."\" ></td>";

        $output .= "</tr>";

        $output .= "<tr>";

        $output .= " <td><label>Monitoring: </label></td>";
        $output .= " <td>";

        $output .= "<select name=\"monitoring\" size=\"1\" >";

        $output .= "<option value=\"0\" ";
        if (($this->form_monitoring == 0) or !isset($this->form_monitoring)) {
            $output .= " selected ";
        }
        $output .= " > Ne </option>";

        $output .= "<option value=\"1\" ";
        if ($this->form_monitoring == 1) {
            $output .= " selected ";
        }
        $output .= "> Ano </option>";

        $output .= "</select>";

        //klik na pregenerovaní fajlů
        $output .= "<span style=\"padding-left: 100px;\">Ruční vynucené přegenerování souborů (pro monitoring2) ".
                "<a target=\"_new\" href=\"https://monitoring.adminator.net/mon/www/rb_all.php?ip=".$this->form_ip_adresa."&only_create=only_create\">zde</a>".
                "</span>";

        $output .= "</td>";

        $output .= "</tr>";

        $output .= "<tr>";

        $output .= " <td><label>Monitoring kategorie: </label></td>";
        $output .= " <td>";

        $output .= "<select name=\"monitoring_cat\" size=\"1\" >";

        $dotaz_cat = $this->conn_mysql->query("SELECT * FROM kategorie WHERE sablona = 4 order by id");

        $output .= "<option value=\"0\" class=\"select-nevybrano\"> Není zvoleno </option>";

        while ($data_cat = $dotaz_cat->fetch_array()) {
            $output .= "<option value=\"".$data_cat["id"]."\" ";

            if ($data_cat["id"] == $this->form_monitoring_cat) {
                $output .= " selected ";
            }
            $output .= "> ".$data_cat["jmeno"]." </option>";
        }

        $output .= "</select>";

        $output .= "</td>";

        $output .= "</tr>";

        $output .= "<tr>";

        $output .= " <td><label>Alarm: </label></td>";
        $output .= " <td>";

        $output .= "<select name=\"alarm\" size=\"1\" >";

        $output .= "<option value=\"0\" ";
        if ($this->form_alarm == 0 or !isset($this->form_alarm)) {
            $output .= " selected ";
        }
        $output .= "> Ne </option>";

        $output .= "<option value=\"1\" ";
        if ($this->form_alarm == 1) {
            $output .= " selected ";
        }
        $output .= " > Ano </option>";

        $output .= "</select>";

        $output .= "</td>";

        $output .= "</tr>";

        $output .= "
            <tr>
                <td colspan=\"2\">&nbsp;</td>
            </tr>";

        $output .= "
            <tr>
                <td>Nadřazený nod: (kvůli filtraci)</td>
                <td>";

        $sql_nod = "SELECT * from nod_list WHERE ( jmeno LIKE '%$this->form_nod_find%' ";
        $sql_nod .= " OR ip_rozsah LIKE '%$this->form_nod_find%' OR adresa LIKE '%$this->form_nod_find%' ";
        $sql_nod .= " OR pozn LIKE '%$this->form_nod_find%' ) ORDER BY jmeno ASC ";

        $vysledek = $this->conn_mysql->query($sql_nod);
        $radku = $vysledek->num_rows;

        $output .= '<select size="1" name="selected_nod" onChange="self.document.forms.form1.submit()" >';

        if (($radku == 0)) {
            $output .= "<option value=\"0\" style=\"color: gray; \" selected >nelze zjistit / žádný nod nenalezen </option>";
        } else {
            $output .= '<option value="0" style="color: gray; font-style: bold; "';
            if ((!isset($this->form_selected_nod))) {
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

        $output .= "</td>
            </tr>";

        $output .= "
            <tr>
                <td></td>
            <td><span style=\"padding-right: 20px;\">hledání:</span>
            <input type=\"text\" name=\"nod_find\" size=\"30\" value=\"".$this->form_nod_find."\" >
            <span style=\"padding-left: 20px;\">
            <input type=\"button\" value=\"Filtrovat nody\" name=\"G\" onClick=\"self.document.forms.form1.submit()\" >
            </span>
            </td>
        </tr>";

        $output .= "
            <tr>
                <td colspan=\"2\"><br></td>
            </tr>";

        $output .= "<tr>";

        $output .= " <td><label>Filtrace: </label></td>";
        $output .= " <td>";

        $output .= "<select name=\"filtrace\" size=\"1\" >";

        $output .= "<option value=\"0\" ";
        if ($this->form_filtrace == 0 or !isset($this->form_filtrace)) {
            $output .= " selected ";
        }
        $output .= "> Ne </option>";

        $output .= "<option value=\"1\" ";
        if ($this->form_filtrace == 1) {
            $output .= " selected ";
        }
        $output .= " > Ano </option>";

        $output .= "</select>";

        $output .= "</td>";
        $output .= "</tr>";


        $output .= "
            <tr>
                <td colspan=\"2\"><br></td>
            </tr>";

        $output .= "
            <tr>
                <td>Poznámka</td>
            <td><textarea name=\"poznamka\" rows=\"8\" cols=\"40\">".$this->form_poznamka."</textarea></td>
            </tr>";

        $output .= '
            <tr>
                <td><br></td>
                <td></td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <input type="hidden" name="update_id" value="'.$this->form_update_id.'">
                    <input type="submit" value="OK" name="odeslat">
                </td>
            </tr>

            </table>';

        $output .= '</form>';

        return [$output];
    }

    private function showResults(): string
    {
        $output = "";

        $output .= "<b>Zadáno do formuláře : </b><br><br>";

        $output .= "<b>Název: </b>".$this->form_nazev."<br>";
        $output .= "<b>IP adresa: </b>".$this->form_ip_adresa."<br>";
        $output .= "<b>MAC: </b>".$this->form_mac."<br>";

        $output .= "<br>";

        $output .= "<b>Nadřazený router: </b>";

        $rs_rs = $this->conn_mysql->query("SELECT nazev FROM router_list WHERE id = '".intval($this->form_update_id)."' ");
        $rs_rs->data_seek(0);
        list($parent_router_name) = $rs_rs->fetch_row();

        $output .= $parent_router_name." (id: ".$this->form_parent_router.")<br>";

        $output .= "<br>";

        $output .= "<b>Monitorování: </b>";

        if ($this->form_monitoring == 1) {
            $output .= "Ano";
        } elseif ($this->form_monitoring == 0) {
            $output .= "Ne";
        } else {
            $output .= "nelze zjistit";
        }
        $output .= "<br>";
        $output .= "<b>Monitorování kategorie: </b>";

        $mon_cat_name = $this->conn_mysql->query("SELECT jmeno FROM kategorie WHERE id = '".intval($this->form_monitoring_cat)."' ");
        $mon_cat_name->data_seek(0);
        list($monitoring_cat_name) = $mon_cat_name->fetch_row();

        $output .= $monitoring_cat_name." (id: ".$this->form_monitoring_cat.")<br>";

        $output .= "<br>";

        $output .= "<b>Alarm: </b>";
        if ($this->form_alarm == 1) {
            $output .= "Ano";
        } elseif ($this->form_alarm == 0) {
            $output .= "Ne";
        } else {
            $output .= "nelze zjistit";
        }
        $output .= "<br>";
        $output .= "<b>Filtrace: </b>";

        if ($this->form_filtrace == 1) {
            $output .= "Ano";
        } elseif ($this->form_filtrace == 0) {
            $output .= "Ne";
        } else {
            $output .= "nelze zjistit";
        }
        $output .= "<br>";
        $output .= "<b>Nadřazený nod (kvůli filtraci): </b>";

        $rs_node_name = $this->conn_mysql->query("SELECT jmeno FROM nod_list WHERE id = '".intval($this->form_selected_nod)."' ");
        $rs_node_name->data_seek(0);
        list($nod_name) = $rs_node_name->fetch_row();

        $output .= $nod_name." (id: ".$this->form_selected_nod.")<br>";

        $output .= "<br><b>Poznámka: </b>".addslashes($this->form_poznamka)."<br>";

        return $output;
    }

    private function saveDataIntoDatabase(): array
    {
        $output = "";

        $this->form_poznamka = addslashes($this->form_poznamka);

        if (strlen($this->form_mac) <= 0) {
            $this->form_mac = "00:00:00:00:00:00";
        }

        if ($this->form_update_id > 0) {

            // prvne zjistime puvodni hodnoty
            try {
                $dotaz_top = $this->conn_mysql->query("SELECT nazev, ip_adresa, parent_router, mac, monitoring,
                                                        monitoring_cat, alarm, filtrace, id_nodu, poznamka
                                                        FROM router_list WHERE id = '".intval($this->form_update_id)."' ");
                $dotaz_top_radku = $dotaz_top->num_rows;
            } catch (Exception $e) {
                $this->error_messages .= "<div style=\"color: red;\">"
                                        . "Chyba! Nelze načíst zdrojové hodnoty pro ArchivZmen.</div>"
                                        . "<div style=\"color: red; \"> caught error: " . $e->getMessage() . "</div>"
                ;
                return [$output, false];
            }

            if ($dotaz_top_radku < 1) {
                $this->error_messages .= "<div style=\"color: red;\">"
                                        . "Chyba! Nelze načíst zdrojové hodnoty pro ArchivZmen. (zero rows found in DB)"
                                        . "</div>";
                return [$output, false];
            } else {
                while ($data_top = $dotaz_top->fetch_array()):

                    $this->origDataArray["nazev"] = $data_top["nazev"];
                    $this->origDataArray["ip_adresa"] = $data_top["ip_adresa"];
                    $this->origDataArray["parent_router"] = $data_top["parent_router"];
                    $this->origDataArray["mac"] = $data_top["mac"];
                    $this->origDataArray["monitoring"] = $data_top["monitoring"];
                    $this->origDataArray["monitoring_cat"] = $data_top["monitoring_cat"];
                    $this->origDataArray["alarm"] = $data_top["alarm"];
                    $this->origDataArray["filtrace"] = $data_top["filtrace"];
                    $this->origDataArray["id_nodu"] = $data_top["id_nodu"];
                    $this->origDataArray["poznamka"] = $data_top["poznamka"];

                endwhile;
            }

            $uprava = $this->conn_mysql->query("UPDATE router_list SET nazev='$this->form_nazev', ip_adresa='$this->form_ip_adresa', parent_router='$this->form_parent_router',
                            mac='$this->form_mac', monitoring='$this->form_monitoring', monitoring_cat='$this->form_monitoring_cat', alarm='$this->form_alarm',
                    filtrace='$this->form_filtrace', id_nodu='$this->form_selected_nod', poznamka = '$this->form_poznamka' WHERE id=".intval($this->form_update_id)." Limit 1 ");

            if ($uprava) {
                $output .= "<div style=\"color: green; font-weight: bold; padding-top: 10px; \">Záznam úspěšně upraven.</div><br>";
                $vysledek_write = 1;
            } else {
                $output .= "<div style=\"color: red; padding-top: 10px; \">Záznam nelze upravit v databázi. </div><br>";
                $vysledek_write = 0;
            }

            //ulozeni do archivu zmen
            $this->actionArchivZmenDiff($vysledek_write);

            //automatické restarty
            $this->work->workActionTopologyRouterDiff();

        } else {
            // rezim pridani
            $add = $this->conn_mysql->query(
                "INSERT INTO router_list (nazev,ip_adresa, parent_router, "
                            . "mac, monitoring, alarm, monitoring_cat, filtrace, id_nodu, poznamka) "
                            . "VALUES ('$this->form_nazev','$this->form_ip_adresa','$this->form_parent_router', "
                            . "'$this->form_mac','$this->form_monitoring','$this->form_alarm','$this->form_monitoring_cat', "
                            . " '$this->form_filtrace', '$this->form_selected_nod', '$this->form_poznamka' ) "
            );

            if ($add) {
                $output .= "<div style=\"color: green; font-weight: bold; padding-top: 10px; \">Záznam úspěšně vložen.</div>";
                $vysledek_write = 1;
            } else {
                $output .= "<div style=\"color: red; padding-top: 10px; \">Záznam nelze vložit do databáze. </div>";
                $output .= "<div style=\"color: red; \">".$this->conn_mysql->error."</div>";
                $vysledek_write = 0;
            }

            // pridame to do archivu zmen
            $this->actionArchivZmenAdd($vysledek_write);

            //automatické restarty
            $this->work->workActionTopologyRouterAdd();
        } //konec if/else update_id > 0

        return [$output, true];
    }

    private function actionArchivZmenDiff(int $vysledek_write): void
    {
        $nod_upd["nazev"] = $this->form_nazev;
        $nod_upd["ip_adresa"] = $this->form_ip_adresa;
        $nod_upd["parent_router"] = $this->form_parent_router;
        $nod_upd["mac"] = $this->form_mac;
        $nod_upd["monitoring"] = $this->form_monitoring;
        $nod_upd["monitoring_cat"] = $this->form_monitoring_cat;
        $nod_upd["alarm"] = $this->form_alarm;
        //    $nod_upd["device_type_id"] = $device_type_id;

        $nod_upd["filtrace"] = $this->form_filtrace;
        $nod_upd["id_nodu"] = $this->form_selected_nod;
        $nod_upd["poznamka"] = $this->form_poznamka;

        //$pole3 .= "[id_nodu] => ".$id_new;
        $pole3 = "<b>akce: uprava routeru;</b><br>";
        $pole3 .= ' [id_routeru] => <a href="/topology/router-list?odeslano=OK&f_id_routeru=' . $this->form_update_id . '">'.$this->form_update_id."</a>";
        $pole3 .= " diferencialni data: ";

        //novy zpusob archivovani dat
        foreach ($this->origDataArray as $key => $val) {
            if (!($nod_upd[$key] == $val)) {
                if ($key == "parent_router") {
                    $pole3 .= "změna <b>Nadřazený router</b> z: ";
                    $pole3 .= "<span class=\"az-s1\">";

                    $dotaz_router1 = $this->conn_mysql->query("SELECT nazev FROM router_list WHERE id = '$val'");
                    if (($dotaz_router1->num_rows == 1)) {
                        while ($data = $dotaz_router1->fetch_array()) {
                            $pole3 .= $data["nazev"]." (".$val.")";
                        }
                    } else {
                        $pole3 .= $val;
                    }

                    $pole3 .= "</span> na: <span class=\"az-s2\">";

                    $id = $nod_upd[$key];
                    $dotaz_router2 = $this->conn_mysql->query("SELECT nazev FROM router_list WHERE id = '$id'");
                    if (($dotaz_router2->num_rows == 1)) {
                        while ($data = $dotaz_router2->fetch_array()) {
                            $pole3 .= $data["nazev"]." (".$id.")";
                        }
                    } else {
                        $pole3 .= $id;
                    }

                    $pole3 .= "</span>";
                    $pole3 .= ", ";
                } //konec key == parent_router
                elseif ($key == "monitoring") {
                    $pole3 .= "změna <b>Monitorování</b> z: "."<span class=\"az-s1\">";

                    if ($val == 1) {
                        $pole3 .= "Ano";
                    } elseif ($val == 0) {
                        $pole3 .= "Ne";
                    } else {
                        $pole3 .= $val;
                    }

                    $pole3 .= "</span>";
                    $pole3 .= " na: <span class=\"az-s2\">";

                    if ($nod_upd[$key] == 1) {
                        $pole3 .= "Ano";
                    } elseif ($nod_upd[$key] == 0) {
                        $pole3 .= "Ne";
                    } else {
                        $pole3 .= $nod_upd[$key];
                    }

                    $pole3 .= "</span>";
                    $pole3 .= ", ";
                } //konec key == monitoring
                elseif ($key == "monitoring_cat") {
                    $pole3 .= "změna <b>Monitoring kategorie</b> z: "."<span class=\"az-s1\">".$val."</span>";
                    $pole3 .= " na: <span class=\"az-s2\">".$nod_upd[$key]."</span>, ";
                } //konec key == monitoring_cat
                elseif ($key == "alarm") {
                    $pole3 .= "změna <b>Alarmu</b> z: "."<span class=\"az-s1\">";

                    if ($val == 1) {
                        $pole3 .= "Zapnuto";
                    } elseif ($val == 0) {
                        $pole3 .= "Vypnuto";
                    } else {
                        $pole3 .= $val;
                    }

                    $pole3 .= "</span>";
                    $pole3 .= " na: <span class=\"az-s2\">";

                    if ($nod_upd[$key] == 1) {
                        $pole3 .= "Zapnuto";
                    } elseif ($nod_upd[$key] == 0) {
                        $pole3 .= "Vypnuto";
                    } else {
                        $pole3 .= $nod_upd[$key];
                    }

                    $pole3 .= "</span>";
                    $pole3 .= ", ";
                } //konec key == alarm
                elseif ($key == "id_nodu") {
                    $pole3 .= "změna <b>Připojného bodu</b> z: ";

                    $vysl_t1 = $this->conn_mysql->query("select jmeno FROM nod_list WHERE id = '$val'");
                    while ($data_t1 = $vysl_t1->fetch_array()) {
                        $pole3 .= "<span class=\"az-s1\">".$data_t1["jmeno"]."</span>";
                    }

                    $pole3 .= " na: ";

                    $val2 = $nod_upd[$key];

                    $vysl_t2 = $this->conn_mysql->query("SELECT jmeno FROM nod_list WHERE id = '$val2'");
                    while ($data_t2 = $vysl_t2->fetch_array()) {
                        $pole3 .= "<span class=\"az-s2\">".$data_t2["jmeno"]."</span>";
                    }

                    $pole3 .= ", ";
                } // konec key == id_nodu
                elseif ($key == "filtrace") {
                    $pole3 .= "změna <b>Filtrace</b> z: "."<span class=\"az-s1\">";

                    if ($val == 1) {
                        $pole3 .= "Ano";
                    } elseif ($val == 0) {
                        $pole3 .= "Ne";
                    } else {
                        $pole3 .= $val;
                    }

                    $pole3 .= "</span>";
                    $pole3 .= " na: <span class=\"az-s2\">";

                    if ($nod_upd[$key] == 1) {
                        $pole3 .= "Ano";
                    } elseif ($nod_upd[$key] == 0) {
                        $pole3 .= "Ne";
                    } else {
                        $pole3 .= $nod_upd[$key];
                    }

                    $pole3 .= "</span>";
                    $pole3 .= ", ";
                } //konec key == filtrace

                else { // ostatni mody, nerozpoznane
                    $pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
                    $pole3 .= "na: <span class=\"az-s2\">".$nod_upd[$key]."</span>, ";
                } //konec else
            } // konec if key == val
        } // konec foreach

        if (preg_match("/.*změna.*/", $pole3) == false) {
            $pole3 .= " <b>nebyly provedeny žádné změny</b> ";
        }

        try {
            $add = $this->conn_mysql->query(
                "INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) "
                . "VALUES ('" . $this->conn_mysql->real_escape_string($pole3) ."', '" . $this->loggedUserEmail . "', '$vysledek_write') "
            );
            $db_error = "";
        } catch (Exception $e) {
            $db_error = '<div>(Caught exception: ' . $e->getMessage() . ")</div>";
        }

        if ($add) {
            $this->p_bs_alerts["Akce byla úspěšně zaznamenána do archivu změn."] = "success";
        } else {
            $this->p_bs_alerts["Akci se nepodařilo zaznamenat do archivu změn." . $db_error] = "warning";
        }
    }

    private function actionArchivZmenAdd(int $vysledek_write): void
    {
        $pole = "<b>akce: pridani routeru;</b><br>";
        $pole .= " nazev: ".$this->form_nazev.", ip adresa: ".$this->form_ip_adresa.", monitoring: ".$this->form_monitoring.", monitoring_cat: ".$this->form_monitoring_cat;
        $pole .= " alarm: ".$this->form_alarm.", parent_router: ".$this->form_parent_router.", mac: ".$this->form_mac.", filtrace: ".$this->form_filtrace.", id_nodu: ".$this->form_selected_nod;

        try {
            $add = $this->conn_mysql->query(
                "INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) "
                . "VALUES ('" . $this->conn_mysql->real_escape_string($pole) ."', '" . $this->loggedUserEmail . "', '$vysledek_write') "
            );
            $db_error = "";
        } catch (Exception $e) {
            $db_error = '<div>(Caught exception: ' . $e->getMessage() . ")</div>";
        }

        if ($add) {
            $this->p_bs_alerts["Akce byla úspěšně zaznamenána do archivu změn."] = "success";
        } else {
            $this->p_bs_alerts["Akci se nepodařilo zaznamenat do archivu změn." . $db_error] = "warning";
        }
    }
}

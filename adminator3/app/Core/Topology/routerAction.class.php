<?php

namespace App\Core\Topology;

use App\Core\adminator;
use Psr\Container\ContainerInterface;
use Exception;

class RouterAction extends adminator
{
    public \mysqli|\PDO $conn_mysql;

    public $smarty;

    public $logger;

    protected $settings;

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

    public function __construct(ContainerInterface $container)
    {
        $this->conn_mysql = $container->get('connMysql');
        // $this->conn_pgsql = $container->get('connPgsql');
        $this->logger = $container->get('logger');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");
    }

    public function action(): array
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $output = "";
        $http_status_code = 200;

        $output .= "<div style=\"padding-bottom: 10px; font-size: 18px; \">Přidání/úprava routeru</div>";

        $this->loadFormData();

        if($this->form_odeslat == "OK") { // zda je odesláno
            $this->checkFormData();
        }

        if(($this->form_odeslat == "OK") and ($this->form_error != 1)) {
            // proces ukladani ..
            // T.B.A. L168 - 382

            // $this->showResults();

            // $this->saveDataIntoDatabase();

        } else {
            // nechceme ukladat, tj. zobrazit form

            // pokud update, tak zjistit predchozi hodnoty
            // nacteni promennych, pokud se nedna o upravu a neodeslal sem form
            if($this->form_update_id > 0 and ($this->form_odeslat != "OK")) {
                $rs = $this->loadPreviousData();

                if($rs === false){
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
            $dotaz_top = $this->conn_mysql->query("SELECT * FROM router_list2 WHERE id = '".intval($this->form_update_id)."' ");
            $dotaz_top_radku = $dotaz_top->num_rows;
        } catch (Exception $e) {
            $this->error_messages .= "<div style=\"color: red;\">"
                                        . "Chyba! Nelze načíst zdrojové hodnoty pro úpravu.</div>"
                                        . "<div style=\"color: red; \"> caught error: " . $e->getMessage() . "</div>"
                                    ;
            return false;
        }

        if ($dotaz_top_radku < 1) {
            $this->error_messages .= "<div style=\"color: red;\">
              <p> Chyba! Nelze načíst zdrojové hodnoty pro úpravu. (zero rows found in DB)</p></div>";
            return false;
        } else {
            while($data_top = $dotaz_top->fetch_array()):

                if($this->form_nazev == "") {
                    $this->form_nazev = $data_top["nazev"];
                }
                if($this->form_ip_adresa == "") {
                    $this->form_ip_adresa = $data_top["ip_adresa"];
                }
                if($this->form_parent_router == "") {
                    $this->form_parent_router = $data_top["parent_router"];
                }
                if($this->form_mac == "") {
                    $this->form_mac = $data_top["mac"];
                }
                if($this->form_filtrace == "") {
                    $this->form_filtrace = $data_top["filtrace"];
                }
                if($this->form_monitoring == "") {
                    $this->form_monitoring = $data_top["monitoring"];
                }
                if($this->form_monitoring_cat == "") {
                    $this->form_monitoring_cat = $data_top["monitoring_cat"];
                }
                if($this->form_alarm == "") {
                    $this->form_alarm = $data_top["alarm"];
                }
                if($this->form_poznamka == "") {
                    $this->form_poznamka = $data_top["poznamka"];
                }
                if($this->form_selected_nod == "") {
                    $this->form_selected_nod = $data_top["id_nodu"];
                }
            endwhile;
        }

        return true;
    }

    private function checkFormData(): void
    {
        //monitoring potrebuje i monitoring kategorii
        if(($this->form_monitoring_cat == 0)) {
            echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">
                Nelze uložit, musíte vybrat kategorii pro monitoring. </div>";

            $this->form_error = 1;
        }

        if(($this->form_monitoring == 1)) {

            //test api a spravnosti konfigurace routeru
            // TODO: fix this
            // $rs_test = $ag->test_router_for_monitoring($update_id);

            if($rs_test[0] === false) {
                echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">".
                    "Nelze uložit s parametrem \"<b>Monitoring - Ano</b>\", selhala kontrola nastavení či stavu routeru pro monitoring.</div>";

                echo "<div style=\"color: grey;\" >výpis testu: <pre>".htmlspecialchars($rs_test[1])."</pre></div>";

                $this->form_error = 1;
            } //end if rs_test === false

        } //end od if monitoring == 1

        //nadrazený router musí být vyplnen
        if(intval($this->form_parent_router) < 1) {
            echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">"
                    . "Nelze uložit, je třeba vyplnit pole \"Nadřazený router\" (kvůli filtraci a QoSu na reinhardech). </div>";

            $this->form_error = 1;
        }

        //kontrola IP adresy
        if((strlen($this->form_ip_adresa) > 0)) {

            if(!(validateIpAddress($this->form_ip_adresa))) {
                echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">".
                        "IP adresa (".$this->form_ip_adresa.") není ve správném formátu !!!</div>";

                $this->form_error = 1;
            }
        }

        //check dns nazvu
        if((strlen($this->form_nazev) > 0)) {

            //kontrola správnosti zadání
            $dns_check = preg_match('/^([[:alnum:]]|\.|-)+$/', $this->form_nazev);

            if(!($dns_check)) {
                echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">".
                        "DNS záznam (".$this->form_nazev.") není ve správnem formátu !!!</div>";

                $error = 1;
            }

            //kontrola delky
            if((strlen($this->form_nazev) > 40)) {

                echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">".
                    "DNS záznam (".$this->form_nazev.") je moc dlouhý!!! Maximální délka je 40 znaků.</div>";

                $this->form_error = 1;
            }
        }

        //kontrola mac adresy
        if((strlen($this->form_mac) > 0)) {

            $mac_check = preg_match('/^([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})$/', $this->form_mac);

            if(!($mac_check)) {
                echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">".
                    "MAC adresa (".$this->form_mac.") není ve správném formátu !!!</div>";

                $this->form_error = 1;
            }
        }

        //povinné údaje
        if((strlen($this->form_nazev) == 0) or (strlen($this->form_ip_adresa) == 0) or (strlen($this->form_parent_router) == 0)) {

            echo "<div style=\"color: red; font-weight: bold; padding-top: 10px; \">
                Nelze uložit, nejsou vyplněny všechny potřebné údaje. (Název, IP adresa, Nadřazený router). </div>";

            $this->form_error = 1;
        }
    }

    private function showForm(): array
    {
        $output = "";

        print '<form method="POST" action="" name="form1">';

        $output .= '<table border="0" width="100%" id="table2">
            <tr>
                <td width="200px"><label>Název: </label></td>
                <td><input type="text" name="nazev" size="30" value="'.$this->form_nazev.'"></td>
            </tr>

            <tr>
            <td><label>IP adresa : </label></td>
            <td><input type="text" name="ip_adresa" size="20" value="'.$ip_adresa.'" ></td>
            </tr>

            <tr>
            <td><label>Nadřazený router: </label></td>';

        echo "<td>";

        echo "<select name=\"parent_router\" size=\"1\" >";

        $dotaz_parent = $this->conn_mysql->query("SELECT * FROM router_list ORDER BY nazev");

        echo "<option value=\"0\" class=\"select-nevybrano\" > není zvoleno </option>";

        while($data_parent = $dotaz_parent->fetch_array()) {
            echo "<option value=\"".$data_parent["id"]."\" ";

            if ($data_parent["id"] == $parent_router) {
                echo " selected ";
            }
            echo "> ".$data_parent["nazev"]." ( ".$data_parent["ip_adresa"]." ) </option>";
        }
        echo "</select></td>";

        echo "</tr>";

        echo "<tr><td><br></td></tr>";

        echo "<tr>";

        echo "<td><label>MAC: </label></td>";
        echo "<td><input type=\"text\" name=\"mac\" size=\"20\" maxlength=\"17\" value=\"".$mac."\" ></td>";

        echo "</tr>";

        echo "<tr>";

        echo " <td><label>Monitoring: </label></td>";
        echo " <td>";

        echo "<select name=\"monitoring\" size=\"1\" >";

        echo "<option value=\"0\" ";
        if (($monitoring == 0) or !isset($monitoring)) {
            echo " selected ";
        }
        echo " > Ne </option>";

        echo "<option value=\"1\" ";
        if ($monitoring == 1) {
            echo " selected ";
        }
        echo "> Ano </option>";

        echo "</select>";

        //klik na pregenerovaní fajlů
        echo "<span style=\"padding-left: 100px;\">Ruční vynucené přegenerování souborů (pro monitoring2) ".
                "<a target=\"_new\" href=\"https://monitoring.adminator.net/mon/www/rb_all.php?ip=".$ip_adresa."&only_create=only_create\">zde</a>".
                "</span>";

        echo "</td>";

        echo "</tr>";

        echo "<tr>";

        echo " <td><label>Monitoring kategorie: </label></td>";
        echo " <td>";

        echo "<select name=\"monitoring_cat\" size=\"1\" >";

        $dotaz_cat = $this->conn_mysql->query("SELECT * FROM kategorie WHERE sablona LIKE 4 order by id");

        echo "<option value=\"0\" class=\"select-nevybrano\"> Není zvoleno </option>";

        while($data_cat = $dotaz_cat->fetch_array()) {
            echo "<option value=\"".$data_cat["id"]."\" ";

            if ($data_cat["id"] == $monitoring_cat) {
                echo " selected ";
            }
            echo "> ".$data_cat["jmeno"]." </option>";
        }

        echo "</select>";

        echo "</td>";

        echo "</tr>";

        echo "<tr>";

        echo " <td><label>Alarm: </label></td>";
        echo " <td>";

        echo "<select name=\"alarm\" size=\"1\" >";

        echo "<option value=\"0\" ";
        if ($alarm == 0 or !isset($alarm)) {
            echo " selected ";
        }
        echo "> Ne </option>";

        echo "<option value=\"1\" ";
        if ($alarm == 1) {
            echo " selected ";
        }
        echo " > Ano </option>";

        echo "</select>";

        echo "</td>";

        echo "</tr>";

        echo "
            <tr>
                <td colspan=\"2\">&nbsp;</td>
            </tr>";

        echo "
            <tr>
                <td>Nadřazený nod: (kvůli filtraci)</td>
                <td>";

        $sql_nod = "SELECT * from nod_list WHERE ( jmeno LIKE '%$nod_find%' ";
        $sql_nod .= " OR ip_rozsah LIKE '%$nod_find%' OR adresa LIKE '%$nod_find%' ";
        $sql_nod .= " OR pozn LIKE '%$nod_find%' ) ORDER BY jmeno ASC ";

        $vysledek = $this->conn_mysql->query($sql_nod);
        //$vysledek=$conn_mysql->query("SELECT * from nod_list ORDER BY jmeno ASC" );
        $radku = $vysledek->num_rows;

        print '<select size="1" name="selected_nod" onChange="self.document.forms.form1.submit()" >';

        if(($radku == 0)) {
            echo "<option value=\"0\" style=\"color: gray; \" selected >nelze zjistit / žádný nod nenalezen </option>";
        } else {
            echo '<option value="0" style="color: gray; font-style: bold; "';
            if((!isset($selected_nod))) {
                echo "selected";
            }
            echo ' > Není vybráno</option>';

            while ($zaznam2 = $vysledek->fetch_array()) {
                echo '<option value="'.$zaznam2["id"].'"';
                if (($selected_nod == $zaznam2["id"])) {
                    echo " selected ";
                }
                echo '>'." ".$zaznam2["jmeno"]." ( ".$zaznam2["ip_rozsah"]." )".'</option>'." \n";
            } //konec while
        } //konec else

        print '</select>';

        echo "</td>
            </tr>";

        echo "
            <tr>
                <td></td>
            <td><span style=\"padding-right: 20px;\">hledání:</span>
            <input type=\"text\" name=\"nod_find\" size=\"30\" value=\"".$nod_find."\" >
            <span style=\"padding-left: 20px;\">
            <input type=\"button\" value=\"Filtrovat nody\" name=\"G\" onClick=\"self.document.forms.form1.submit()\" >
            </span>
            </td>
        </tr>";

        echo "
            <tr>
                <td colspan=\"2\"><br></td>
            </tr>";

        echo "<tr>";

        echo " <td><label>Filtrace: </label></td>";
        echo " <td>";

        echo "<select name=\"filtrace\" size=\"1\" >";

        echo "<option value=\"0\" ";
        if ($filtrace == 0 or !isset($filtrace)) {
            echo " selected ";
        }
        echo "> Ne </option>";

        echo "<option value=\"1\" ";
        if ($filtrace == 1) {
            echo " selected ";
        }
        echo " > Ano </option>";

        echo "</select>";

        echo "</td>";
        echo "</tr>";


        echo "
            <tr>
                <td colspan=\"2\"><br></td>
            </tr>";

        echo "
            <tr>
                <td>Poznámka</td>
            <td><textarea name=\"poznamka\" rows=\"8\" cols=\"40\">".$poznamka."</textarea></td>
            </tr>";

        echo '
            <tr>
                <td><br></td>
                <td></td>
            </tr>

                <tr>
                <td></td>
                <td><input type="hidden" name="update_id" value="'.$update_id.'"><input type="submit" value="OK" name="odeslat">

                </td>
                </tr>

                </table>

            </form>';

        return [$output];
    }
}

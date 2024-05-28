<?php

namespace App\Core\Topology;

use App\Core\adminator;
use Psr\Container\ContainerInterface;

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

        $output .= "<div style=\"padding-bottom: 10px; font-size: 18px; \">Přidání/úprava routeru</div>";

        $this->loadFormData();

        if($this->form_odeslat == "OK") { //zda je odesláno
            // T.B.A. L73 - 164
            $this->checkFormData();

        }

        if(($this->form_odeslat == "OK") and ($this->form_error != 1)) {
            //proces ukladani ..
            // T.B.A. L168 - 382 
        } else{
            //nechceme ukladat, tj. zobrazit form

            //pokud update, tak zjistit predchozi hodnoty
            // nacteni promennych, pokud se nedna o upravu a neodeslal sem form
            if($this->form_update_id > 0 and ($this->form_odeslat != "OK")) {
                $this->loadPreviousData();
            }


        }

        return [$output];
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
        $dotaz_top = $this->conn_mysql->query("SELECT * FROM router_list WHERE id = '".intval($this->form_update_id)."' ");
        $dotaz_top_radku = $dotaz_top->num_rows;

        if ($dotaz_top_radku < 1) {
            // TODO: populate do renderer/controller
            $this->error_messages .= "<span style=\"color: red; font-size: 16px; font-weight: bold;\">
              <p> Chyba! Nelze načíst zdrojové hodnoty pro úpravu. </p></span>";
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
}

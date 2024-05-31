<?php

namespace App\Core\Topology;

use App\Core\adminator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class nodeAction extends adminator
{
    public \mysqli|\PDO $conn_mysql;

    // public \Smarty $smarty;

    public \Monolog\Logger $logger;

    // protected $settings;

    protected $sentinel;

    // protected $work;

    protected $loggedUserEmail;

    public $csrf_html;

    private $error;

    public function __construct(ContainerInterface $container)
    {
        $this->conn_mysql = $container->get('connMysql');
        // $this->conn_pgsql = $container->get('connPgsql');
        $this->logger = $container->get('logger');
        $this->sentinel = $container->get('sentinel');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->loggedUserEmail = $this->sentinel->getUser()->email;

        // $this->work = new \App\Core\work($container);
    }

    public function add(ServerRequestInterface $request): string
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $output = "";

        foreach ($request->getParsedBody() as $i => $v) {
            if(preg_match('/^(odeslano|jmeno|adresa|pozn|ip_rozsah|typ_vysilace|stav|router_id|typ_nodu|filter_router_id)$/', $i) and strlen($v) > 0) {
                $$i = $request->getParsedBody()[$i];
            }
        }

        //kontrola platnych udaju
        if(isset($odeslano)) { /* @phpstan-ignore isset.variable */
            if(preg_match("/\//", $ip_rozsah)) {
                $this->error .= "<div style=\"color: red; \" ><H4>Pole \"IP rozsah\" obsahuje nepovolený znak \"/\" !</H4></div>";
            }

            if(preg_match("/\.254$/", $ip_rozsah)) {
                $this->error .= "<div style=\"color: red; \" ><H4>Pole \"IP rozsah\" nemůže končit .254, neplatný subnet!</H4></div>";
            }

            if (($typ_nodu < 1) or ($typ_nodu > 2)) {
                $this->error .= "<div style=\"color: red; \" ><H4>Špatná hodnota u prvku \"Mód nodu\"!</H4></div>";
            }

            if ($filter_router_id == 0) {
                $this->error .= "<div style=\"color: red; \" ><H4>Špatná hodnota u prvku \"Router ID\"!</H4></div>";
            }

            if ($router_id == 0) {
                $this->error .= "<div style=\"color: red; \" ><H4>Špatná hodnota u prvku \"Router, kde se provádí filtrace\"!</H4></div>";
            }
        } //konec if isset odeslano

        $output .= "<div style=\"padding-bottom: 10px; padding-top: 10px; font-size: 18px; \">Přidání lokality/nodu</div>";

        if((isset($_POST["jmeno"]) and !isset($this->error))) {

            //budeme ukladat
            $output .= "<b>Zadáno do formuláře : </b><br><br>";

            $output .= "<b>Nazev</b>: ".$jmeno."<br>";
            $output .= "<b>Adresa</b>: ".$adresa."<br>";
            $output .= "<b>Poznamka</b>: ".$pozn."<br>";
            $output .= "<b>IP rozsah</b>: ".$ip_rozsah."<br>";

            $output .= "<br>";

            $output .= "<b>Typ vysílače</b>: ".$typ_vysilace."<br>";
            $output .= "<b>Stav</b>: ".$stav."<br>";

            $output .= "<b>Router id</b>: ".$router_id."<br>";

            $output .= "<b>Router id filtrace</b>: ".$filter_router_id."<br>";

            $output .= "<b>Mód nodu</b>: ".$typ_nodu."<br>";

            $add = $this->conn_mysql->query("INSERT INTO nod_list (jmeno, adresa, pozn, ip_rozsah,typ_vysilace,stav,router_id,typ_nodu, filter_router_id)
                  VALUES ('$jmeno','$adresa','$pozn','$ip_rozsah','$typ_vysilace','$stav','$router_id','$typ_nodu', '$filter_router_id') ");

            if($add) {
                $output .= "<br><span style=\"color: green; font-size: 18px; \">Záznam úspěšně vložen.</span><br><br>";
            } else {
                $output .= "<span style=\"color: red; font-weight: bold; font-size: 16px; \">Záznam nelze vložit do databáze. </span>";
            }

            // pridame to do archivu zmen
            $pole = "<b>akce: pridani nodu ; </b><br>";
            $pole .= "[nazev]=> ".$jmeno.", [adresa]=> ".$adresa.", [poznamka]=> ".$pozn.", [ip_rozsah]=> ".$ip_rozsah;
            $pole .= ", [typ_vysilace]=> ".$typ_vysilace.", [stav]=> ".$stav.", [router_id]=> ".$router_id.", ";
            $pole .= " [typ_nodu]=> ".$typ_nodu.", [filter_router_id]=> ".$filter_router_id;

            if ($add) {
                $vysledek_write = 1;
            } else {
                $vysledek_write = 0;
            }

            $add = $this->conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
                              "('".$this->conn_mysql->real_escape_string($pole)."','".
                              $this->loggedUserEmail . "','".
                              $vysledek_write . "') ");
        } else {
            //zobrazime formular

            $output .= $this->error;

            $output .= '<form method="POST" action="" >';
            $output .= $this->csrf_html;

            $output .= '<table border="0" width="100%" id="table2">
                    <tr>
                    <td width="25%"><label>Jméno lokality/nodu: </label></td>
                    <td><input type="text" name="jmeno" size="30" value="'.htmlspecialchars($jmeno).'" ></td>
                    </tr>

                    <tr>
                    <td><label>Adresa nodu (umístění) : </label></td>
                    <td><input type="text" name="adresa" size="40" value="'.htmlspecialchars($adresa).'" ></td>
                    </tr>

                    <tr>
                    <td><label>Poznámka : </label></td>
                    <td><textarea name="pozn" cols="30" rows="3">'.htmlspecialchars($pozn).'</textarea></td>
                    </tr>

                    <tr>
                        <td><label>IP rozsah pro lokalitu/nod: </label></td>
                        <td><input type="text" name="ip_rozsah" size="20" value="'.htmlspecialchars($ip_rozsah).'" ></td>
                    </tr>

                    <tr>
                        <td><br></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td><label>Typ vysílače: </label></td>
                        <td>
                        <select name="typ_vysilace" size="1" >';

            $output .= "<option value=\"0\" "." class=\"select-nevybrano\" > Není zvoleno </option> \n";
            $output .= "<option value=\"1\" ";
            if($typ_vysilace == 1) {
                $output .= " selected ";
            } $output .= "> Metallic </option>\n";
            $output .= "<option value=\"2\" ";
            if($typ_vysilace == 2) {
                $output .= " selected ";
            } $output .= "> ap-2,4GHz-OMNI </option>\n";
            $output .= "<option value=\"3\" ";
            if($typ_vysilace == 3) {
                $output .= " selected ";
            } $output .= "> ap-2,4Ghz-sektor </option>\n";
            $output .= "<option value=\"4\" ";
            if($typ_vysilace == 4) {
                $output .= " selected ";
            } $output .= "> ap-2.4Ghz-smerovka </option>\n";
            $output .= "<option value=\"5\" ";
            if($typ_vysilace == 5) {
                $output .= " selected ";
            } $output .= "> ap-5.8Ghz-OMNI </option>\n";
            $output .= "<option value=\"6\" ";
            if($typ_vysilace == 6) {
                $output .= " selected ";
            } $output .= "> ap-5.8Ghz-sektor</option>\n";
            $output .= "<option value=\"7\" ";
            if($typ_vysilace == 7) {
                $output .= " selected ";
            } $output .= "> ap-5.8Ghz-smerovka </option>\n";
            $output .= "<option value=\"8\" ";
            if($typ_vysilace == 8) {
                $output .= " selected ";
            } $output .= "> jiné </option>\n";

            $output .= '</select>
                        </td>
                    </tr>

                    <tr>
                        <td><label>Stav: </label></td>
                        <td><select name="stav" >';

            $output .= "<option value=\"0\" class=\"select-nevybrano\" > Není zvoleno </option>\n";
            $output .= "<option value=\"1\" ";
            if ($stav == 1) {
                $output .= " selected ";
            } $output .= "> v pořádku </option>\n";
            $output .= "<option value=\"2\" ";
            if ($stav == 2) {
                $output .= " selected ";
            } $output .= "> vytížen </option>\n";
            $output .= "<option value=\"3\" ";
            if ($stav == 3) {
                $output .= " selected ";
            } $output .= "> přetížen </option>\n";


            $output .= '</select>
                    </td>
                </tr>
                <tr>
                    <td><br></td>
                    <td></td>
                </tr>

                <tr>
                  <td><label>Router id: </label></td>
                  <td>';

            $output .= "<select name=\"router_id\" size=\"1\" >\n";
            $output .= "<option value=\"0\" class=\"select-nevybrano\" > není zvoleno </option>\n";

            $dotaz_parent = $this->conn_mysql->query("SELECT id, nazev, ip_adresa FROM router_list ORDER BY nazev");
            while($data_parent = $dotaz_parent->fetch_array()) {
                $output .= "\t\t\t<option value=\"".intval($data_parent["id"])."\" ";
                if ($data_parent["id"] == $router_id) {
                    $output .= " selected ";
                }
                $output .= "> ".htmlspecialchars($data_parent["nazev"])." ( ".htmlspecialchars($data_parent["ip_adresa"])." ) </option>\n";
            }

            $output .= "</select>\n";

            $output .= '</td>
                        </tr>';

            $output .= "<tr>
                        <td>Mód nodu:</td>
                        <td>\n";

            $output .= "<select size=\"1\" name=\"typ_nodu\" >\n";

            $output .= "\t\t\t<option value=\"0\" style=\"color: gray; \" ";
            if($typ_nodu == 0) {
                $output .= " selected ";
            }
            $output .= " >Nezvoleno</option>\n";

            $output .= "\t\t\t<option value=\"1\" style=\"color: #CC0033; \" ";
            if($typ_nodu == 1) {
                $output .= " selected ";
            }
            $output .= " >Bezdrátová síť</option>\n";

            /*
                     $output .= "\t\t\t<option value=\"2\" style=\"color: #e37d2b; font-weight: bold;\" ";
                             if($typ_nodu == 2 )$output .= " selected ";
                     $output .= " >Optická síť</option>\n";
            */
            $output .= "</select>\n";

            $output .= "</td>
                        </tr>\n";

            $output .= '<tr>
                        <td><label>Router, kde se provádí filtrace: </label></td>
                    <td>';

            $output .= "<select name=\"filter_router_id\" size=\"1\" >\n";

            $dotaz_parent = $this->conn_mysql->query("SELECT id,nazev,ip_adresa FROM router_list ORDER BY nazev");
            $output .= "<option value=\"0\" class=\"select-nevybrano\" > není zvoleno </option>\n";

            while($data_parent = $dotaz_parent->fetch_array()) {
                $output .= "\t\t\t<option value=\"".intval($data_parent["id"])."\" ";

                if($data_parent["id"] == $filter_router_id) {
                    $output .= " selected ";
                }
                $output .= "> ".htmlspecialchars($data_parent["nazev"])." ( ".htmlspecialchars($data_parent["ip_adresa"])." ) </option>\n";
            }

            $output .= "</select>\n";

            $output .= '</td>
                        </tr>

                    <tr>
                        <td><br></td>
                        <td></td>
                    </tr>';

            $output .= '</tr>
                        <td><br></td>
                        <td></td>
                    </tr>

                    <tr>
                    <td></td>
                    <td><input type="submit" value="OK" name="odeslano" >

                    </td>
                        </tr>
                        </table>

                    </form>';
        }

        return $output;
    }

    public function update()
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

    }
}

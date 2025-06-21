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

    protected $work;

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

        $this->work = new \App\Core\work($container);
    }

    public function add(ServerRequestInterface $request): string
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $output = "";

        foreach ($request->getParsedBody() as $i => $v) {
            if (preg_match('/^(odeslano|jmeno|adresa|pozn|ip_rozsah|typ_vysilace|stav|router_id|typ_nodu|filter_router_id)$/', $i) and strlen($v) > 0) {
                $$i = $request->getParsedBody()[$i];
            }
        }

        //kontrola platnych udaju
        if (isset($odeslano)) { /* @phpstan-ignore isset.variable */
            if (preg_match("/\//", $ip_rozsah)) {
                $this->error .= "<div style=\"color: red; \" ><H4>Pole \"IP rozsah\" obsahuje nepovolený znak \"/\" !</H4></div>";
            }

            if (preg_match("/\.254$/", $ip_rozsah)) {
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

        if ((isset($_POST["jmeno"]) and !isset($this->error))) {

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

            if ($add) {
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

            //automaticke restarty
            $this->work->workActionTopologyNodeAdd();

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
            if ($typ_vysilace == 1) {
                $output .= " selected ";
            } $output .= "> Metallic </option>\n";
            $output .= "<option value=\"2\" ";
            if ($typ_vysilace == 2) {
                $output .= " selected ";
            } $output .= "> ap-2,4GHz-OMNI </option>\n";
            $output .= "<option value=\"3\" ";
            if ($typ_vysilace == 3) {
                $output .= " selected ";
            } $output .= "> ap-2,4Ghz-sektor </option>\n";
            $output .= "<option value=\"4\" ";
            if ($typ_vysilace == 4) {
                $output .= " selected ";
            } $output .= "> ap-2.4Ghz-smerovka </option>\n";
            $output .= "<option value=\"5\" ";
            if ($typ_vysilace == 5) {
                $output .= " selected ";
            } $output .= "> ap-5.8Ghz-OMNI </option>\n";
            $output .= "<option value=\"6\" ";
            if ($typ_vysilace == 6) {
                $output .= " selected ";
            } $output .= "> ap-5.8Ghz-sektor</option>\n";
            $output .= "<option value=\"7\" ";
            if ($typ_vysilace == 7) {
                $output .= " selected ";
            } $output .= "> ap-5.8Ghz-smerovka </option>\n";
            $output .= "<option value=\"8\" ";
            if ($typ_vysilace == 8) {
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
            while ($data_parent = $dotaz_parent->fetch_array()) {
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
            if ($typ_nodu == 0) {
                $output .= " selected ";
            }
            $output .= " >Nezvoleno</option>\n";

            $output .= "\t\t\t<option value=\"1\" style=\"color: #CC0033; \" ";
            if ($typ_nodu == 1) {
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

            while ($data_parent = $dotaz_parent->fetch_array()) {
                $output .= "\t\t\t<option value=\"".intval($data_parent["id"])."\" ";

                if ($data_parent["id"] == $filter_router_id) {
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

    public function update(ServerRequestInterface $request): string
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $output = "";

        $output .= "<div style=\"padding-bottom: 10px; padding-top: 10px; font-size: 18px; font-weight: bold; \">";
        $output .= "Úprava lokality/nodu</div>";

        if ($_POST["jmeno_new"]) {
            //budeme updatovat
            foreach ($request->getParsedBody() as $i => $v) {
                if (preg_match('/^(typ_vysilace|stav|router_id|typ_nodu|vlan_id|filter_router_id|device_type_id|rid_recom)$/', $i) and strlen($v) > 0) {
                    $$i = $request->getParsedBody()[$i];
                }
            }

            $jmeno = $_POST["jmeno_new"];
            $adresa = $_POST["adresa_new"];
            $pozn = $_POST["pozn_new"];
            $ip_rozsah = $_POST["ip_rozsah_new"];
            $umisteni_aliasu = $_POST["umisteni_aliasu_new"];
            $id_new = $_POST["update_id_new"];
            $mac = $_POST["mac_new"];
            $filtrace = $_POST["filtrace_new"];

            $id = $_POST["update_id"];

            $filter_router_id = $_POST["filter_router_id"];
            $device_type_id = intval($device_type_id);
        }

        if (($_POST["B1"] == "OK")) {
            $dotaz_router = $this->conn_mysql->query("SELECT nazev FROM router_list WHERE id = '$router_id'");
            if (($dotaz_router->num_rows == 1)) {
                while ($data_parent = $dotaz_router->fetch_array()) {
                    $nazev_routeru = $data_parent["nazev"]." (".$router_id.")";
                }
            } else {
                $nazev_routeru = $router_id;
            }

            $output .= "<b><H4>Zadáno do formuláře:</b></H4>";
            $output .= "<b>Název</b>: ".$jmeno."<br>";
            $output .= "<b>Adresa</b>: ".$adresa."<br>";
            $output .= "<b>Poznámka</b>: ".$pozn."<br>";
            $output .= "<b>IP rozsah</b>: ".$ip_rozsah."<br>";
            $output .= "<b>Typ vysílače</b>: ";

            if ($typ_vysilace == 0) {
                $output .= "Nezvoleno";
            } elseif ($typ_vysilace == 1) {
                $output .= "Metallic";
            } elseif ($typ_vysilace == 2) {
                $output .= "ap-2,4GHz-OMNI";
            } elseif ($typ_vysilace == 3) {
                $output .= "ap-2,4Ghz-sektor";
            } elseif ($typ_vysilace == 4) {
                $output .= "ap-2.4Ghz-smerovka";
            } elseif ($typ_vysilace == 5) {
                $output .= "ap-5.8Ghz-OMNI";
            } elseif ($typ_vysilace == 6) {
                $output .= "ap-5.8Ghz-sektor";
            } elseif ($typ_vysilace == 7) {
                $output .= "ap-5.8Ghz-smerovka";
            } elseif ($typ_vysilace == 8) {
                $output .= "jiné";
            } else {
                $output .= $typ_vysilace;
            }

            $output .= "<br>";
            $output .= "<b>stav vysílače</b>: ";

            if ($stav == 0) {
                $output .= "Není zvoleno";
            } elseif ($stav == 1) {
                $output .= "v pořádku ";
            } elseif ($stav == 2) {
                $output .= "vytížen";
            } elseif ($stav == 3) {
                $output .= "přetížen";
            } else {
                $output .= $stav;
            }

            $output .= "<br>";
            $output .= "<b>Router</b>: ".$nazev_routeru."<br>";

            $output .= "<b>Typ nodu</b>: ".$typ_nodu."<br>";
            $output .= "<b>vlan id</b>: ".$vlan_id."<br>";
            $output .= "<b>id routeru, kde se filtrujou IP</b>: ".$filter_router_id."<br>";

            $output .= "<b>Typ(model) koncového zařízení (switche):</b> ".$device_type_id."<br>";

            $pole = "<b>akce: uprava nodu;</b><br>";
            //$pole .= "puvodni data: ";

            $vysledek = $this->conn_mysql->query("select * from nod_list where id=".$id_new);
            $radku = $vysledek->num_rows;

            if ($radku == 0) {
                $output .= "<div style=\"padding: 5px; color: red; font-weight: bold; \">Chyba! Nelze zjistit přechozí hodnoty! </div>";
                $pole .= "[error] => nelze zjistit predchozi hodnoty, ";
            } else {
                while ($zaznam = $vysledek->fetch_array()):

                    $pole_puvodni_data["jmeno"] = $zaznam["jmeno"];
                    $pole_puvodni_data["adresa"] = $zaznam["adresa"];
                    $pole_puvodni_data["ip_rozsah"] = $zaznam["ip_rozsah"];
                    $pole_puvodni_data["typ_vysilace"] = $zaznam["typ_vysilace"];
                    $pole_puvodni_data["stav"] = $zaznam["stav"];
                    $pole_puvodni_data["router_id"] = $zaznam["router_id"];
                    $pole_puvodni_data["pozn"] = $zaznam["pozn"];
                    $pole_puvodni_data["typ_nodu"] = $zaznam["typ_nodu"];

                    $pole_puvodni_data["vlan_id"] = $zaznam["vlan_id"];
                    $pole_puvodni_data["filter_router_id"] = $zaznam["filter_router_id"];
                    $pole_puvodni_data["device_type_id"] = $zaznam["device_type_id"];

                endwhile;
            }

            $uprava = $this->conn_mysql->query("UPDATE nod_list SET jmeno='$jmeno', adresa='$adresa' , pozn='$pozn', ip_rozsah='$ip_rozsah',
                    typ_vysilace='$typ_vysilace',stav='$stav',router_id='$router_id',
                    typ_nodu = '$typ_nodu', vlan_id = '$vlan_id', filter_router_id = '$filter_router_id',
                    device_type_id = '$device_type_id' WHERE id=".$id_new." Limit 1 ");

            if ($uprava) {
                $output .= "<br><span style=\"color: green; font-size: 18px; \">Záznam úspěšně upraven.</span><br><br>";
            } else {
                $output .= "<div style=\"color: red; font-weight: bold; font-size: 16px; \">Chyba! Záznam nelze upravit. </div>";
                $output .= "<div>chyba: ".$this->conn_mysql->error."</div>\n";
            }

            //ulozeni do archivu zmen
            $nod_upd["jmeno"] = $jmeno;
            $nod_upd["adresa"] = $adresa;
            $nod_upd["pozn"] = $pozn;
            $nod_upd["ip_rozsah"] = $ip_rozsah;
            $nod_upd["stav"] = $stav;
            $nod_upd["typ_vysilace"] = $typ_vysilace;
            $nod_upd["router_id"] = $router_id;
            $nod_upd["typ_nodu"] = $typ_nodu;

            $nod_upd["vlan_id"] = $vlan_id;
            $nod_upd["filter_router_id"] = $filter_router_id;

            $nod_upd["device_type_id"] = $device_type_id;

            //$pole .= "<br> aktualni data: ";
            //foreach ($nod_upd as $key => $val) { $pole .= " [".$key."] => ".$val."\n"; }

            $pole3 .= "[id_nodu] => ".$id_new;
            $pole3 .= " diferencialni data: ";

            //novy zpusob archivovani dat
            foreach ($pole_puvodni_data as $key => $val) {
                if (!($nod_upd[$key] == $val)) {
                    if ($key == "pozn") {
                        $pole3 .= "změna <b>Poznámky</b> z: ";
                        $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$nod_upd[$key]."</span>";
                        $pole3 .= ", ";
                    } //konec key == pozn
                    elseif ($key == "typ_nodu") {
                        $pole3 .= "změna <b>Módu</b> z: "."<span class=\"az-s1\">";

                        if ($val == 0) {
                            $pole3 .= "Nezvoleno";
                        } elseif ($val == 1) {
                            $pole3 .= "bezdrátová síť";
                        } elseif ($val == 2) {
                            $pole3 .= "optická síť";
                        } else {
                            $pole3 .= $val;
                        }

                        $pole3 .= "</span>";
                        $pole3 .= " na: <span class=\"az-s2\">";

                        if ($nod_upd[$key] == 0) {
                            $pole3 .= "Nezvoleno";
                        } elseif ($nod_upd[$key] == 1) {
                            $pole3 .= "bezdrátová síť";
                        } elseif ($nod_upd[$key] == 2) {
                            $pole3 .= "optická síť";
                        } else {
                            $pole3 .= $nod_upd[$key];
                        }

                        $pole3 .= "</span>";
                        $pole3 .= ", ";
                    } //konec key == typ_nodu
                    elseif ($key == "typ_vysilace") {
                        $pole3 .= "změna <b>Typu vysílače</b> z: ";

                        if ($val == 0) {
                            $pole3 .= "<span class=\"az-s1\">Nezvoleno</span>";
                        } elseif ($val == 1) {
                            $pole3 .= "<span class=\"az-s1\">Metallic</span>";
                        } elseif ($val == 2) {
                            $pole3 .= "<span class=\"az-s1\">ap-2,4GHz-OMNI</span>";
                        } elseif ($val == 3) {
                            $pole3 .= "<span class=\"az-s1\">ap-2,4Ghz-sektor</span>";
                        } elseif ($val == 4) {
                            $pole3 .= "<span class=\"az-s1\">ap-2.4Ghz-smerovka</span>";
                        } elseif ($val == 5) {
                            $pole3 .= "<span class=\"az-s1\">ap-5.8Ghz-OMNI</span>";
                        } elseif ($val == 6) {
                            $pole3 .= "<span class=\"az-s1\">ap-5.8Ghz-sektor</span>";
                        } elseif ($val == 7) {
                            $pole3 .= "<span class=\"az-s1\">ap-5.8Ghz-smerovka</span>";
                        } elseif ($val == 8) {
                            $pole3 .= "<span class=\"az-s1\">jiné</span>";
                        } else {
                            $pole3 .= "<span class=\"az-s1\">".$val."</span>";
                        }

                        $pole3 .= " na: <span class=\"az-s2\">";

                        if ($nod_upd[$key] == 0) {
                            $pole3 .= "Nezvoleno";
                        } elseif ($nod_upd[$key] == 1) {
                            $pole3 .= "Metallic";
                        } elseif ($nod_upd[$key] == 2) {
                            $pole3 .= "ap-2,4GHz-OMNI";
                        } elseif ($nod_upd[$key] == 3) {
                            $pole3 .= "ap-2,4Ghz-sektor";
                        } elseif ($nod_upd[$key] == 4) {
                            $pole3 .= "ap-2.4Ghz-smerovka";
                        } elseif ($nod_upd[$key] == 5) {
                            $pole3 .= "ap-5.8Ghz-OMNI";
                        } elseif ($nod_upd[$key] == 6) {
                            $pole3 .= "ap-5.8Ghz-sektor";
                        } elseif ($nod_upd[$key] == 7) {
                            $pole3 .= "ap-5.8Ghz-smerovka";
                        } elseif ($nod_upd[$key] == 8) {
                            $pole3 .= "jiné";
                        } else {
                            $pole3 .= $nod_upd[$key];
                        }

                        $pole3 .= "</span>";
                        $pole3 .= ", ";
                    } //konec key == typ_vysilace
                    elseif ($key == "stav") {
                        $pole3 .= "změna <b>Stavu</b> z: ";
                        $pole3 .= "<span class=\"az-s1\">";

                        if ($val == 0) {
                            $pole3 .= "Není zvoleno";
                        } elseif ($val == 1) {
                            $pole3 .= "v pořádku ";
                        } elseif ($val == 2) {
                            $pole3 .= "vytížen";
                        } elseif ($val == 3) {
                            $pole3 .= "přetížen";
                        } else {
                            echo $val;
                        }

                        $pole3 .= "</span> na: <span class=\"az-s2\">";

                        if ($nod_upd[$key] == 0) {
                            $pole3 .= "Není zvoleno";
                        } elseif ($nod_upd[$key] == 1) {
                            $pole3 .= "v pořádku ";
                        } elseif ($nod_upd[$key] == 2) {
                            $pole3 .= "vytížen";
                        } elseif ($nod_upd[$key] == 3) {
                            $pole3 .= "přetížen";
                        } else {
                            echo $nod_upd[$key];
                        }

                        $pole3 .= "</span>";
                        $pole3 .= ", ";
                    } //konec key == stav
                    elseif ($key == "router_id") {
                        $pole3 .= "změna <b>Routeru</b> z: ";
                        $pole3 .= "<span class=\"az-s1\">";

                        $dotaz_router1 = $this->conn_mysql->query("SELECT * FROM router_list WHERE id = '".intval($val)."'");
                        if (($dotaz_router1->num_rows == 1)) {
                            while ($data = $dotaz_router1->fetch_array()) {
                                $pole3 .= $data["nazev"]." (".$val.")";
                            }
                        } else {
                            $pole3 .= $val;
                        }

                        $pole3 .= "</span> na: <span class=\"az-s2\">";

                        $nod_zmena = $nod_upd[$key];
                        $dotaz_router2 = $this->conn_mysql->query("SELECT * FROM router_list WHERE id = '".intval($nod_zmena)."'");
                        if (($dotaz_router2->num_rows == 1)) {
                            while ($data = $dotaz_router2->fetch_array()) {
                                $pole3 .= $data["nazev"]." (".$nod_zmena.")";
                            }
                        } else {
                            $pole3 .= $nod_zmena;
                        }

                        $pole3 .= "</span>";
                        $pole3 .= ", ";
                    } //konec key == router_id
                    elseif ($key == "filter_router_id") {

                        $pole3 .= "změna <b>Routeru, kde se provádí filtrace</b> z: ";
                        $pole3 .= "<span class=\"az-s1\">";

                        $dotaz_router1 = $this->conn_mysql->query("SELECT nazev FROM router_list WHERE id = '".intval($val)."'");
                        if (($dotaz_router1->num_rows == 1)) {
                            while ($data = $dotaz_router1->fetch_array()) {
                                $pole3 .= $data["nazev"]." (".$val.")";
                            }
                        } else {
                            $pole3 .= $val;
                        }

                        $pole3 .= "</span> na: <span class=\"az-s2\">";

                        $nod_zmena = $nod_upd[$key];
                        $dotaz_router2 = $this->conn_mysql->query("SELECT nazev FROM router_list WHERE id = '".intval($nod_zmena)."'");
                        if (($dotaz_router2->num_rows == 1)) {
                            while ($data = $dotaz_router2->fetch_array()) {
                                $pole3 .= $data["nazev"]." (".$nod_zmena.")";
                            }
                        } else {
                            $pole3 .= $nod_zmena;
                        }

                        $pole3 .= "</span>";
                        $pole3 .= ", ";

                        // } elseif($key == "device_type_id") {

                        //     $pole3 .= "změna <b>koncového zařízení</b> z: ";
                        //     $pole3 .= "<span class=\"az-s1\">";

                        //     $rs_device_id = $this->conn_mysql->query("SELECT name FROM nod_device_type WHERE id = '".intval($val)."' ");
                        //     $pole3 .= mysql_result($rs_device_id, 0, 0)." (".intval($val).")";

                        //     $pole3 .= "</span> na: <span class=\"az-s2\">";

                        //     $rs_device_id2 = $this->conn_mysql->query("SELECT name FROM nod_device_type WHERE id = '".intval($nod_upd[$key])."' ");
                        //     $pole3 .= mysql_result($rs_device_id2, 0, 0)." (".intval($nod_upd[$key]).")";

                        //     $pole3 .= "</span>";
                        //     $pole3 .= ", ";

                    } else {
                        // ostatni mody, nerozpoznane
                        $pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
                        $pole3 .= "na: <span class=\"az-s2\">".$nod_upd[$key]."</span>, ";
                    } //konec else
                } // konec if key == val

            } // konec foreach

            $pole .= "".$pole3;

            if ($uprava) {
                $vysledek_write = 1;
            } else {
                $vysledek_write = 0;
            }
            $add = $this->conn_mysql->query(
                "INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
                      "('".$this->conn_mysql->real_escape_string($pole)."',".
                      "'" . $this->loggedUserEmail . "',".
                      "'" . $vysledek_write . "') "
            );

            //automaticke restarty
            $this->work->workActionTopologyNodeDiff();

        } else {
            //zobrazime formular
            if (array_key_exists('update_id', $request->getParsedBody())) {
                $id = $request->getParsedBody()['update_id'];
            } elseif (array_key_exists('update_id_new', $request->getParsedBody())) {
                $id = $request->getParsedBody()['update_id_new'];
            }

            $vysledek = $this->conn_mysql->query("select * from nod_list where id=".intval($id)."");
            $radku = $vysledek->num_rows;

            if ($radku == 0) {
                $output .= "<div style=\"padding: 5px; color: red; font-weight: bold; \">";
                $output .= "Chyba! Nelze zjistit původní hodnoty!</div>";
            } else {
                while ($zaznam = $vysledek->fetch_array()):

                    $id = $zaznam["id"];
                    $jmeno = $zaznam["jmeno"];
                    $adresa = $zaznam["adresa"];
                    $pozn = $zaznam["pozn"];
                    $ip_rozsah = $zaznam["ip_rozsah"];
                    $umisteni_aliasu = $zaznam["umisteni_aliasu"];
                    $mac = $zaznam["mac"];
                    $filtrace = $zaznam["filtrace"];
                    $typ_vysilace = $zaznam["typ_vysilace"];

                    $stav = $zaznam["stav"];
                    $typ_nodu = $zaznam["typ_nodu"];

                    $router_id = $zaznam["router_id"];
                    $vlan_id = $zaznam["vlan_id"];
                    $filter_router_id = $zaznam["filter_router_id"];

                    $device_type_id = $zaznam["device_type_id"];

                endwhile;
            }

            //checkem jestli se macklo na tlacitko "OK" :)
            if (preg_match("/^OK$/", $_POST["B1"])) {
                $output .= "";
            } else {
                $output .= "<div class=\"objekty-add-no-click-ok\"><h4>Data neuloženy, nebylo použito ".
                "tlačítko \"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>";
            }

            //zde kontrola zda jiz jsme odeslali $_POST["jmeno_new"]

            $output .= '
            <form method="POST" action="">';

            $output .= $this->csrf_html;

            $output .= '<table border="0" width="950px;" id="table2" name="form1" >

            <tr>
            <td width="25%"><label>Jméno lokality/nodu: </label></td>
            <td><input type="text" name="jmeno_new" size="30" value="'.$jmeno.'"></td>
            </tr>

            <tr>
            <td><label>Adresa nodu (umístění) : </label></td>
            <td><input type="text" name="adresa_new" size="40" value="'.$adresa.'"></td>
            </tr>

           <tr>
            <td><label>Poznámka : </label></td>
            <td><textarea name="pozn_new" cols="30" rows="3">'.$pozn.'</textarea></td>
           </tr>

            <tr>
               <td><label>IP rozsah pro lokalitu/nod: </label></td>
               <td><input type="text" name="ip_rozsah_new" size="20" value="'.$ip_rozsah.'"></td>
           </tr>

           <tr>
               <td><label>Typ vysílače: </label></td>
               <td>
               <select name="typ_vysilace" size="1" >';

            $output .= "<option value=\"0\" "." class=\"select-nevybrano\" > Není zvoleno </option>";
            $output .= "<option value=\"1\" ";
            if ($typ_vysilace == 1) {
                $output .= " selected ";
            } $output .= "> Metallic </option>";
            $output .= "<option value=\"2\" ";
            if ($typ_vysilace == 2) {
                $output .= " selected ";
            } $output .= "> ap-2,4GHz-OMNI </option>";
            $output .= "<option value=\"3\" ";
            if ($typ_vysilace == 3) {
                $output .= " selected ";
            } $output .= "> ap-2,4Ghz-sektor </option>";
            $output .= "<option value=\"4\" ";
            if ($typ_vysilace == 4) {
                $output .= " selected ";
            } $output .= "> ap-2.4Ghz-smerovka </option>";
            $output .= "<option value=\"5\" ";
            if ($typ_vysilace == 5) {
                $output .= " selected ";
            } $output .= "> ap-5.8Ghz-OMNI </option>";
            $output .= "<option value=\"6\" ";
            if ($typ_vysilace == 6) {
                $output .= " selected ";
            } $output .= "> ap-5.8Ghz-sektor</option>";
            $output .= "<option value=\"7\" ";
            if ($typ_vysilace == 7) {
                $output .= " selected ";
            } $output .= "> ap-5.8Ghz-smerovka </option>";
            $output .= "<option value=\"8\" ";
            if ($typ_vysilace == 8) {
                $output .= " selected ";
            } $output .= "> jiné </option>";

            $output .= '</select>
               </td>
           </tr>

           <tr>
               <td><label>Stav: </label></td>
               <td><select name="stav" >';

            $output .= "<option value=\"0\" "." class=\"select-nevybrano\" > Není zvoleno </option>";
            $output .= "<option value=\"1\" ";
            if ($stav == 1) {
                $output .= " selected ";
            } $output .= "> v pořádku </option>";
            $output .= "<option value=\"2\" ";
            if ($stav == 2) {
                $output .= " selected ";
            } $output .= "> vytížen </option>";
            $output .= "<option value=\"3\" ";
            if ($stav == 3) {
                $output .= " selected ";
            } $output .= "> přetížen </option>";

            $output .= '</select></td>
           </tr>

           <tr>
             <td><label>Router id: (na kterém routeru IP alias visí)</label></td>
             <td>';

            $output .= "<select name=\"router_id\" size=\"1\" >";

            $dotaz_parent = $this->conn_mysql->query("SELECT * FROM router_list order by nazev");
            $output .= "<option value=\"0\" class=\"select-nevybrano\" > není zvoleno </option>";

            while ($data_parent = $dotaz_parent->fetch_array()) {
                $output .= "<option value=\"".$data_parent["id"]."\" ";
                if ($data_parent["id"] == $router_id) {
                    $output .= " selected ";
                }
                $output .= "> ".$data_parent["nazev"]." ( ".$data_parent["ip_adresa"]." ) </option>\n";
            }
            $output .= "</select>\n";

            $output .= '</td>
           </tr>';

            $output .= "<tr>
             <td><br></td>
             <td></td>
           </tr>";

            $output .= "<tr>
             <td>Mód nodu:</td>
             <td>";

            $output .= "<select size=\"1\" name=\"typ_nodu\" >";

            $output .= "<option value=\"0\" style=\"color: gray; \" ";
            if ($typ_nodu == 0) {
                $output .= " selected ";
            }
            $output .= " >Nezvoleno</option>";

            $output .= "<option value=\"1\" style=\"color: #CC0033; \" ";
            if ($typ_nodu == 1) {
                $output .= " selected ";
            }
            $output .= " >Bezdrátová síť</option>";

            $output .= "<option value=\"2\" style=\"color: #e37d2b; font-weight: bold;\" ";
            if ($typ_nodu == 2) {
                $output .= " selected ";
            }
            $output .= " >Optická síť</option>";

            $output .= "</select>";

            $output .= "</td>
               </tr>";

            $output .= "<tr>
             <td><br></td>
             <td></td>
           </tr>\n";

            $output .= "<tr>
             <td>Vlan id:</td>
             <td><input type=\"text\" name=\"vlan_id\" size=\"10\" value=\"".$vlan_id."\" ></td>
            </tr>\n";

            if ($typ_nodu == 1) {

                $output .= '<tr>
              <td><br></td>
              <td></td>
             </tr>

            <tr>
             <td><label>Router, kde se provádí filtrace: </label></td>
             <td>';

                $output .= "<select name=\"filter_router_id\" size=\"1\" >";

                if ($rid_recom == "yes") {
                    $sql_filtr = "SELECT id,nazev,ip_adresa FROM router_list WHERE (filtrace = 1) ORDER BY nazev";
                } else {
                    $sql_filtr = "SELECT id,nazev,ip_adresa FROM router_list ORDER BY nazev";
                }

                $dotaz_parent = $this->conn_mysql->query($sql_filtr);
                $output .= "<option value=\"0\" class=\"select-nevybrano\" > není zvoleno </option>\n";

                while ($data_parent = $dotaz_parent->fetch_array()) {
                    $output .= "<option value=\"".$data_parent["id"]."\" ";
                    if ($data_parent["id"] == $filter_router_id) {
                        $output .= " selected ";
                    }
                    $output .= "> ".$data_parent["nazev"]." ( ".$data_parent["ip_adresa"]." ) </option>\n";
                }
                $output .= "</select>\n";

                $output .= "<span style=\"padding-left: 40px;\">Pouze doporučené:
               <input type=\"checkbox\" name=\"rid_recom\" value=\"yes\" onclick=\"this.form.submit();\" ";
                if ($rid_recom == "yes") {
                    $output .= " checked ";
                } $output .= " ></span>";

                $output .= '</td>
               </tr>';

            } else {
                $output .= "<input type=\"hidden\" name=\"filter_router_id\" value=\"114\" >\n";
            }

            $output .= '<tr>
             <td colspan="2"><br></td>
             <td></td>
           </tr>';

            if ($typ_nodu == 2) {
                $output .= ' <tr>
               <td><label>Typ(model) koncového zařízení (switche): </label></td>
               <td>';

                $output .= "<select name=\"device_type_id\" size=\"1\" >";
                $output .= "<option value=\"0\" >default :: AT-8000S/24</option>\n";
                $output .= "<option value=\"1\" ";
                if ($device_type_id == 1) {
                    $output .= " selected ";
                }
                $output .= " >h3c s3100 (26tp-ei) - with mac-vlan</option>\n";

                $output .= "<option value=\"2\" ";
                if ($device_type_id == 2) {
                    $output .= " selected ";
                }
                $output .= " >h3c s3100 (26tp-ei) - with DVA</option>\n";


                $output .= "</select>";

                $output .= '</td>
              </tr>';
            }

            $output .= '<tr>
             <td colspan="2"><br></td>
             <td></td>
           </tr>';

            $output .= '<tr>
             <td><input type="hidden" name="update_id_new" value="'.$id.'">&nbsp;</td>
             <td><input type="submit" value="OK" name="B1"></td>
            </tr>

            </table>
           </form>';
        }

        return $output;
    }
}

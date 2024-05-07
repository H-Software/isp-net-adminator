<?php

namespace App\Core;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

use App\Models\Stb as Model;

class stb extends adminator
{
    public $conn_mysql;

    public $conn_pgsql;

    public $container;

    public $csrf_html;

    public $find_id_nodu;        //promenne pro hledani
    public $find_search_string;
    public $find_var_vlastnik;

    public $id_stb;             //pro vypis konkretniho stb, z archivu zmen atd

    public $order;            //razeni

    public $vypis_pocet_sloupcu;    //pocet sloupcu v tabulce

    public $debug = 0;         //vypis sekudarnich informaci (sql dotazy atd)

    public $enable_modify_action = false;

    public $enable_unpair_action = false;

    public $sql_query;

    //var $sql_query_listing;

    public $listing_mod;         // v jakym modu bude vypis /vlastnici -- dle id_cloveka, objekty -- beznej vypis

    public $id_cloveka;         //pokud se vypisou STB dle ic_cloveka //u vlastniku//, tak zde prislusny clovek

    public $find_par_vlastnik;

    public $action_form;

    public $action_form_validation_errors = "";

    public $action_form_validation_errors_wrapper_start = '<div class="alert alert-danger" role="alert">';
    public $action_form_validation_errors_wrapper_end = '</div>';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->validator = $container->get('validator');
        $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');
        $this->logger = $container->get('logger');

        $this->loggedUserEmail = \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email;
    }

    public function stbListGetBodyContent()
    {
        $output = "";

        $odeslano = $_GET["odeslano"];
        $par_vlastnik = intval($_GET["par_vlastnik"]);
        $id_nodu = intval($_GET["id_nodu"]);
        $search = $_GET["search"];
        $order = intval($_GET["order"]);

        if((strlen($_GET["list"]) > 0)) {
            $list = intval($_GET["list"]);
        }

        if((strlen($_GET["id_stb"]) > 0)) {
            $id_stb = intval($_GET["id_stb"]);
        }

        $get_odkazy = "".urlencode("&par_vlastnik")."=".urlencode($par_vlastnik).
                "".urlencode("&id_nodu")."=".urlencode($id_nodu).
                "".urlencode("&search")."=".urlencode($search).
                "".urlencode("&list")."=".urlencode($list).
                "".urlencode("&odeslano")."=".urlencode($odeslano).
                "".urlencode("&id_stb")."=".urlencode($id_stb);

        if($order > 0) {
            $this->order = $order;
        }

        if($id_nodu > 0) {
            $this->find_id_nodu = $id_nodu;
        }

        if($par_vlastnik > 0) {
            $this->find_par_vlastnik = $par_vlastnik;
        }

        if((strlen($search) > 0)) {
            $this->find_search_string = $search;
        }

        if($id_stb > 0) {
            $this->id_stb = $id_stb;
        }

        $this->vypis_pocet_sloupcu = 8;

        $output .= "";

        $topologyClass = new Topology($this->conn_mysql, $this->smarty, $this->logger);

        $rs_select_nod = $topologyClass->filter_select_nods();

        if(isset($rs_select_nod["error"])) {

            $output .= "<div style=\"padding: 10px; color: red; font-size: 14px;\">".
                "Chyba! Funkce \"filter_select_nod\" hlásí chybu: ";

            foreach ($rs_select_nod["error"] as $key => $val) {
                $output .= "#".$key.":<br> ".$val;
            }

            $output .= "</div>\n";
        }

        if(!is_array($rs_select_nod["data"])) {

            $output .= "<div style=\"padding: 10px; color: red; font-size: 14px;\">".
                "Chyba! Funkce \"filter_select_nod\" nevrací žádné relevatní data</div>\n";
        }

        $output .= "<form method=\"GET\" action=\"" . $_SERVER['SCRIPT_URL']. "\" >";

        //filtr - hlavni okno
        if($_GET["odeslano"] == "OK") {
            $display = "visible";
        } else {
            $display = "none";
        }

        $this->generate_sql_query();

        $paging_url = "?".urlencode("order")."=".$this->order.$get_odkazy;

        $paging = new paging_global($this->conn_mysql, $paging_url, 20, $list, "<div class=\"text-listing2\" style=\"text-align: center; padding-top: 10px; padding-bottom: 10px;\">", "</div>\n", $this->sql_query);

        $bude_chybet = ((($list == "") || ($list == "1")) ? 0 : ((($list - 1) * $paging->interval)));

        $interval = $paging->interval;

        $this->sql_query = $this->sql_query . " LIMIT ".$interval." OFFSET ".$bude_chybet." ";

        $this->logger->debug("stb\stbListGetBodyContent: dump var this->sql_query: ".var_export($this->sql_query, true));

        $output .= "<div id=\"objekty_stb_filter\" style=\"display: ".$display.";\" >";

        //vlastnik - bez
        $output .= "<div style=\"width: 150px; float: left;\" >".
                "přiřazeno k vlastníkovi: </div>";

        $output .= "<div style=\"float: left; \">".
                "<select size=\"1\" name=\"par_vlastnik\" style=\"width: 70px;\" >".
                "<option value=\"0\" style=\"color: gray;\" >obojí</option>".
                "<option value=\"1\" ";
        if($par_vlastnik == 1) {
            $output .= " selected ";
        } $output .= ">Ano (spárované)</option>".
        "<option value=\"2\" ";
        if($par_vlastnik == 2) {
            $output .= " selected ";
        } $output .= ">Ne (nespárované)</option>".
        "</select>".
        "</div>";

        //pripojnej bod
        $output .= "<div style=\"width: 100px; float: left; padding-left: 10px; \" >".
        "Přípojný bod: </div>\n";

        $output .= "<div style=\"float: left; padding-left: 10px; \">\n".
        "<select size=\"1\" name=\"id_nodu\" >\n".
        "<option value=\"0\" style=\"color: gray;\" >nevybráno (všechny)</option>\n";

        foreach ($rs_select_nod["data"] as $nod_id => $nod_name) {
            $output .= "<option value=\"".$nod_id."\" ";

            if($nod_id == $id_nodu) {
                $output .= " selected ";
            }

            $output .= " >".$nod_name."</option>\n";
        }

        $output .= "</select>".
        "</div>\n";

        //tarif
        $output .= "<div style=\"width: 50px; float: left; padding-left: 10px; \" >".
        "Tarif: </div>\n";

        $output .= "<div style=\"float: left; padding-left: 10px; \">\n".
        "<select size=\"1\" name=\"id_tarifu\" >\n".
        "<option value=\"0\" style=\"color: gray;\" >nevybráno (všechny)</option>\n".
        "</select>\n".
        "</div>\n";

        //tlacitko
        $output .= "<div style=\"float: left; padding-left: 100%; width: 250px; text-align: right; padding-left: 10px; \" >".
        "<input type=\"submit\" name=\"odeslano\" value=\"OK\" ></div>\n";

        //oddelovac
        $output .= "<div style=\"clear: both; height: 5px; \"></div>\n";

        //druha radka
        $output .= "<div style=\"float: left; \" >Hledání: </div>\n";

        $output .= "<div style=\"float: left; padding-left: 20px; \" >".
        "<input type=\"text\" name=\"search\" value=\"".htmlspecialchars($search)."\" ></div>\n";

        $output .= "<div style=\"float: left; padding-left: 20px; \" >Id Stb: </div>\n";

        $output .= "<div style=\"float: left; padding-left: 20px; \" >".
        "<input type=\"text\" name=\"id_stb\" size=\"3\" value=\"".htmlspecialchars($id_stb)."\" ></div>\n";

        //tlacitko
        $output .= "<div style=\"float: left; padding-left: 10px; \" >".
        "<input type=\"submit\" name=\"odeslano\" value=\"OK\" ></div>\n";

        //oddelovac
        $output .= "<div style=\"clear: both; \"></div>\n";

        $output .= "</div>\n";

        $output .= "</form>\n";

        //listovani
        $output .= $paging->listInterval();

        //zacatek tabulky ... popis

        $output .= "<table border=\"0\" width=\"100%\" style=\"padding-left: 0px; \" >";

        $output .= "
            <tr>\n";

        //popis
        $output .= "<td width=\"200px\" style=\"border-bottom: 1px dashed gray; \" >\n";
        $output .= "\t<div style=\"font-weight: bold; float: left; \">popis</div>\n";

        $output .= "\t<div style=\"float: left; padding-left: 55%; \">".
            "<a href=\"?".urlencode("order")."=1".$get_odkazy."\">";

        if($order == 1) {
            $output .= "<img src=\"//img2/sorting_a-z_hot.jpg\" width=\"20px\" alt=\"sorting_a-z-hot\" >";
        } else {
            $output .= "<img src=\"/img2/sorting_a-z_normal.jpg\" width=\"20px\" alt=\"sorting_a-z-normal\" >";
        }
        $output .= "</a>".
            "</div>\n";

        $output .= "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
            "<a href=\"?".urlencode("order")."=2".$get_odkazy."\">";

        if($order == 2) {
            $output .= "<img src=\"/img2/sorting_z-a_hot.jpg\" width=\"20px\" alt=\"sorting_z-a_hot\" >";
        } else {
            $output .= "<img src=\"/img2/sorting_z-a_normal.jpg\" width=\"20px\" alt=\"sorting_z-a_normal\" >";
        }

        $output .= "</a>".
            "</div>\n";

        $output .= "</td>\n";

        //ip adresa
        $output .= "<td style=\"border-bottom: 1px dashed gray;\" >\n";
        $output .= "\t<div style=\"font-weight: bold; float: left; \">IP adresa</div>\n";

        $output .= "\t<div style=\"float: left; padding-left: 20%; \">".
            "<a href=\"?".urlencode("order")."=3".$get_odkazy."\">";

        if($order == 3) {
            $output .= "<img src=\"/img2/sorting_1-9_hot.jpg\" width=\"20px\" alt=\"sorting_1-9_hot\" >";
        } else {
            $output .= "<img src=\"/img2/sorting_1-9_normal.jpg\" width=\"20px\" alt=\"sorting_1-9_normal\" >";
        }

        $output .= "</a>".
                    "</div>\n";

        $output .= "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
             "<a href=\"?".urlencode("order")."=4".$get_odkazy."\">";

        if($order == 4) {
            $output .= "<img src=\"/img2/sorting_9-1_hot.jpg\" width=\"20px\" alt=\"sorting_9-1_hot\" >";
        } else {
            $output .= "<img src=\"/img2/sorting_9-1_normal.jpg\" width=\"20px\" alt=\"sorting_9-1_normal\" >";
        }

        $output .= "</a>".
            "</div>\n";

        $output .= "</td>\n";

        //poznamka
        $output .= "<td style=\"border-bottom: 1px dashed gray;\" ><b>poznámka</b></td>\n";

        //mac adresa
        $output .= "<td style=\"border-bottom: 1px dashed gray; width: 168px; \" >";
        $output .= "\t<div style=\"font-weight: bold; float: left; \">MAC adresa</div>\n";

        $output .= "\t<div style=\"float: left; padding-left: 20%; \">".
            "<a href=\"?".urlencode("order")."=5".$get_odkazy."\">";

        if($order == 5) {
            $output .= "<img src=\"/img2/sorting_1-9_hot.jpg\" width=\"20px\" alt=\"sorting_1-9_hot\" >";
        } else {
            $output .= "<img src=\"/img2/sorting_1-9_normal.jpg\" width=\"20px\" alt=\"sorting_1-9_normal\" >";
        }

        $output .= "</a>".
            "</div>\n";

        $output .= "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
            "<a href=\"?".urlencode("order")."=6".$get_odkazy."\">";

        if($order == 6) {
            $output .= "<img src=\"/img2/sorting_9-1_hot.jpg\" width=\"20px\" alt=\"sorting_9-1_hot\" >";
        } else {
            $output .= "<img src=\"/img2/sorting_9-1_normal.jpg\" width=\"20px\" alt=\"sorting_9-1_normal\" >";
        }

        $output .= "</a>".
            "</div>\n";

        $output .= "</td>\n";

        //uprava
        $output .= "<td style=\"border-bottom: 1px dashed gray;\" ><b>úprava</b></td>
            
             <td style=\"border-bottom: 1px dashed gray;\" ><b>smazat</b></td>
        
             <td style=\"border-bottom: 1px dashed gray;\" ><b>test</b></td>
        
             <td style=\"border-bottom: 1px dashed gray;\" ><b>tarif</b></td>
            
            </tr>\n\n";

        //2. radka
        $output .= "<tr>
             <td style=\"border-bottom: 1px solid black;\" >\n";
        $output .= "\t<div style=\"font-weight: bold; float: left; \">přípojný nod</div>\n";

        $output .= "\t<div style=\"float: left; padding-left: 32%; \">".
            "<a href=\"?".urlencode("order")."=9".$get_odkazy."\">";

        if($order == 9) {
            $output .= "<img src=\"/img2/sorting_1-9_hot.jpg\" width=\"20px\" alt=\"sorting_1-9_hot\" >";
        } else {
            $output .= "<img src=\"/img2/sorting_1-9_normal.jpg\" width=\"20px\" alt=\"sorting_1-9_normal\" >";
        }

        $output .= "</a>".
                    "</div>\n";

        $output .= "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
            "<a href=\"?".urlencode("order")."=10".$get_odkazy."\">";

        if($order == 10) {
            $output .= "<img src=\"/img2/sorting_z-a_hot.jpg\" width=\"20px\" alt=\"sorting_z-a_hot\" >";
        } else {
            $output .= "<img src=\"/img2/sorting_z-a_normal.jpg\" width=\"20px\" alt=\"sorting_z-a_normal\" >";
        }

        $output .= "</a>".
            "</div>\n";

        $output .= "</td>\n";

        //PUK
        $output .= "<td style=\"border-bottom: 1px solid black;\" >".
           "\t<div style=\"font-weight: bold; float: left; \">PUK</div>\n";

        $output .= "\t<div style=\"float: left; padding-left: 43%; \">".
            "<a href=\"?".urlencode("order")."=7".$get_odkazy."\" >";

        if($order == 7) {
            $output .= "<img src=\"/img2/sorting_1-9_hot.jpg\" width=\"20px\" alt=\"sorting_1-9_hot\" >";
        } else {
            $output .= "<img src=\"/img2/sorting_1-9_normal.jpg\" width=\"20px\" alt=\"sorting_1-9_normal\" >";
        }

        $output .= "</a>".
        "</div>\n";

        $output .= "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
             "<a href=\"?".urlencode("order")."=8".$get_odkazy."\" >";

        if($order == 8) {
            $output .= "<img src=\"/img2/sorting_9-1_hot.jpg\" width=\"20px\" alt=\"sorting_9-1_hot\" >";
        } else {
            $output .= "<img src=\"/img2/sorting_9-1_normal.jpg\" width=\"20px\" alt=\"sorting_9-1_normal\" >";
        }

        $output .= "</a>".
            "</div>\n";

        $output .= "</td>\n";

        $output .= "<td style=\"border-bottom: 1px solid black;\" ><b>id stb (historie)</b></td>
            
             <td style=\"border-bottom: 1px solid black;\" ><b>id človeka</b></td>
        
             <td style=\"border-bottom: 1px solid black;\" ><b>switch port</b></td>
        
             <td colspan=\"2\" style=\"border-bottom: 1px solid black;\" ><b>datum vytvoření</b></td>
        
             <td style=\"border-bottom: 1px solid black;\" ><b>reg. form</b></td>
                  
            </tr>\n";

        $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\"><br></td></tr>\n";

        $output .= $this->vypis();

        $output .= "</table>\n";

        $output .= $paging->listInterval();

        $ret = array($output);

        return $ret;
    }

    public function stbActionValidateFormData(array $input_data)
    {
        // first, validation

        // https://respect-validation.readthedocs.io/en/2.3/08-list-of-rules-by-category/
        $validation = $this->validator->validate(
            $input_data,
            [
                'Popis objektu#popis' => v::noWhitespace()->notEmpty()->alnum("-")->length(3, 20),
                'IP adresa#ip' => v::noWhitespace()->notEmpty()->ip(),
                'Přípojný bod#id_nodu' => v::number()->greaterThan(0),
                'MAC adresa#mac' => v::notEmpty()->macAddress(),
                // 'puk' => v::number(),
                // 'pin1' => v::number(),
                // 'pin2' => v::number(),
                'Číslo portu (ve switchi)#port_id' => v::number(),
                'Tarif#id_tarifu' => v::number()->greaterThan(0),
            ]
        );

        if ($validation->failed()) {
            $valResults = $validation->getErrors();
            foreach ($valResults as $valField => $valError) {
                $this->action_form_validation_errors .= $valError;
            }
        }

        // TODO: add validation optional items (puk, pin1, pin2)

        // second, check duplicities

        $sql_base = "SELECT * FROM objekty_stb ";
        $sql_where_update_id = "";
        if(intval($input_data['update_id']) > 0) {
            $sql_where_update_id = " AND id_stb <> " . $input_data['update_id'] ." ";
        }

        // echo "<pre>" . var_export($input_data, true) ."</pre>";

        $MSQ_POPIS = $this->conn_mysql->query($sql_base . " WHERE (popis LIKE '" . $input_data['popis'] . "' " . $sql_where_update_id .")");
        $MSQ_IP    = $this->conn_mysql->query($sql_base . " WHERE (ip_adresa LIKE '" . $input_data['ip'] . "' " . $sql_where_update_id .")");
        $MSQ_MAC   = $this->conn_mysql->query($sql_base . " WHERE (mac_adresa LIKE '" . $input_data['mac'] . "' " . $sql_where_update_id .")");

        if($MSQ_POPIS->num_rows > 0) {
            $this->action_form_validation_errors .= "<div class=\"alert alert-danger\" role=\"alert\">Popis (".$input_data['popis']." ) již existuje!!!</div>";
        }
        if($MSQ_IP->num_rows > 0) {
            $this->action_form_validation_errors .= "<div class=\"alert alert-danger\" role=\"alert\">IP adresa ( ".$input_data['ip']." ) již existuje!!!</div>";
        }
        if($MSQ_MAC->num_rows > 0) {
            $this->action_form_validation_errors .= "<div class=\"alert alert-danger\" role=\"alert\">MAC adresa ( ".$input_data['mac']." ) již existuje!!!</div>";
        }

        if(empty($this->action_form_validation_errors)) {
            return true;
        } else {
            // $this->logger->info("stb\\stbActionValidateFormData: data validation failed. dump action_form_validation_errors: ".var_export($this->action_form_validation_errors, true));
            return false;
        }

    }

    public function stbActionSaveIntoDatabase($data)
    {
        if($data['update_id']) {

            $update_id = $data['update_id'];

            // mutate columns names back
            $data['sw_port'] = $data['port_id'];
            $data['mac_adresa'] = $data['mac'];
            $data['ip_adresa'] = $data['ip'];
            unset($data['update_id'], $data['nod_find'], $data['port_id'], $data['mac'], $data['ip']);
            unset($data['g1'], $data['g2'], $data['odeslano'], $data['formrid']);

            $data['upravil_kdo'] = $this->loggedUserEmail;

            // save orig data for diff ArchivZmen
            $this->id_stb = $update_id;
            $this->generate_sql_query();

            $rs = $this->conn_mysql->query($this->sql_query);
            $dataOrigDb = $rs->fetch_assoc();

            unset($dataOrigDb["id_stb"], $dataOrigDb["id_cloveka"], $dataOrigDb["datum_vytvoreni"]);

            // db call
            $affected = Model::where('id_stb', $update_id)
                        ->update($data);

            if($affected == 1) {
                $res = true;
            }

            if($res) {
                $output .= "<H3><div style=\"color: green;\" >Data úspěšně uloženy.</div></H3>\n";
            } else {
                $output .= "<H3><div style=\"color: red;\" >Chyba! Data do databáze nelze uložit ci úprava selhala.</div></H3>\n";
                $output .= "res: $res \n";
            }

            $params = array(
                "itemId" => $update_id,
                "actionResult" => $affected,
                "loggedUserEmail" => $this->loggedUserEmail
            );

            $az = new ArchivZmen($this->container, $this->smarty);
            $azRes = $az->insertItemDiff(3, $dataOrigDb, $data, $params);

            if(is_object($azRes)) {
                $output .= "<br><H3><div style=\"color: green;\" >Změna byla úspěšně zaznamenána do archivu změn.</div></H3>\n";
            } else {
                $output .= "<br><H3><div style=\"color: red;\" >Chyba! Změnu do archivu změn se nepodařilo přidat.</div></H3>\n";
            }

        } else {
            // rezim pridani
            //

            // TODO: refaktor DB insert to ORM based way

            // $form_data = array_merge($form_data, array("vlozil_kdo" => $this->loggedUserEmail));

            // $item = Model::create([
            //     'akce' => $actionBody,
            //     'vysledek' => $actionResult,
            //     'provedeno_kym' => $loggedUserEmail
            // ]);

            $sql = "INSERT INTO objekty_stb "
            . " (mac_adresa, ip_adresa, puk, popis, id_nodu, sw_port, pozn, vlozil_kdo, id_tarifu)"
            . " VALUES ('" . $data['mac'] ."','" . $data['ip'] . "','" . $data['puk'] . "','"
            . $data['popis'] . "','" . $data['id_nodu'] . "','" . $data['port_id'] . "','" . $data['pozn'] . "','"
            . $this->loggedUserEmail . "', '" . $data['id_tarifu'] . "') ";

            $this->logger->debug("stb\\stbActionSaveIntoDatabase: sql dump: ".var_export($sql, true));

            $res = $this->conn_mysql->query($sql);

            $id_stb = $this->conn_mysql->insert_id;

            if($res) {
                $output .= "<H3><div style=\"color: green;\" >Data úspěšně uloženy.</div></H3>\n";
            } else {
                $output .= "<H3><div style=\"color: red;\" >Chyba! Data do databáze nelze uložit.</div></H3>\n";
                $output .= "res: $res \n";
            }

            // pridame to do archivu zmen

            // TODO: refactor this to ORM way
            // $az = new ArchivZmen($this->container, $this->smarty);
            // $azRes = $az->insertItem(1, $form_data, $vysledek_write, $this->loggedUserEmail);

            $pole = "<b> akce: pridani stb objektu ; </b><br>";

            $pole .= "[id_stb]=> ".$id_stb.", ";
            $pole .= "[mac_adresa]=> ".$data['mac'].", [ip_adresa]=> ".$data['ip'].", [puk]=> ".$data['puk'].", [popis]=> ".$data['popis'];
            $pole .= ", [id_nodu]=> ".$data['id_nodu'].", [sw_port]=> ".$data['port_id']." [pozn]=> ".$data['pozn'].", [id_tarifu]=> ".$data['id_tarifu'];

            if($res == 1) {
                $vysledek_write = 1;
            }

            $this->conn_mysql->query(
                "INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) ".
                "VALUES ('".$this->conn_mysql->real_escape_string($pole)."',".
                "'".$this->conn_mysql->real_escape_string($this->loggedUserEmail)."',".
                "'" . $vysledek_write . "')"
            );
        }

        return $output;
    }

    public function stbAction(ServerRequestInterface $request, ResponseInterface $response, $csrf)
    {
        // 0 field -> html code for smarty
        // 1 field -> name (and path) of smarty template
        $ret = array();

        $this->logger->info("stb\\stbAction called");

        $a = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);

        $this->action_form = $this->formInit();

        // fill $_POST into array for reusing in the form and etc
        $data = $this->action_form->validate('popis, ip, mac, id_nodu, nod_find, puk, pin1, pin2, port_id, id_tarifu, pozn, odeslano, FormrID, g1, g2, update_id');

        // required intentionaly setted after validate, because in first render we dont see error "card"
        $this->action_form->required = 'popis,ip,mac,id_nodu,port_id,id_tarifu';

        if(!empty($this->action_form->post('odeslano'))) {
            // go for final, but validate data first
            $valRes = $this->stbActionValidateFormData($data);
            // $this->logger->info("stb\\stbAction validateFromData result: " . var_export($valRes, true));

            // if form is OK, go to saving data, otherwise "continue" rendering form (not saving)
            if($this->action_form->ok() and empty($this->action_form_validation_errors)) {
                // go for save into databze
                //
                $rs .= $this->stbActionSaveIntoDatabase($data);
                $rs .= $this->stbActionRenderResults($data);

                $ret[0] = $rs;
                return $ret;
            } else {
                $this->logger->warning("stb\\stbAction: mode \"odeslano\", but some errors found, still render form. ");
                $this->logger->warning(
                    "stb\\stbAction: --> form OK result: " . var_export($this->action_form->ok(), true)
                    //  . ", form messages: " . $this->action_form->messages()
                    // . ", form val. errors: " . var_export($this->action_form_validation_errors, true)
                );
            }
        }

        // prepare data for form
        //
        if(isset($_POST['update_id']) and empty($this->action_form->post('odeslano'))) {
            // update mode
            $this->id_stb = intval($_POST['update_id']);
            $this->generate_sql_query();

            $rs = $this->conn_mysql->query($this->sql_query);
            $data = $rs->fetch_assoc();

            unset($data["id_stb"], $data["id_cloveka"]);
            unset($data["upravil_kdo"], $data["datum_vytvoreni"]);

            // fix columns names
            $data['ip'] = $data['ip_adresa'];
            unset($data['ip_adresa']);
            $data['port_id'] = $data['sw_port'];
            unset($data['sw_port']);

            $data["update_id"] = $this->id_stb;

            // echo "<pre>".var_export($data, true)."</pre>";

            $this->logger->info("stb\\stbAction: update: fetch data: update_id: ".$this->id_stb.", rs_rows: ".$rs->num_rows);
        }

        $topology = new \App\Core\Topology($this->conn_mysql, $this->smarty, $this->logger);

        $node_list = $topology->getNodeListForForm($data['nod_find']);
        $this->logger->debug("stb\\stbAction: node_list data: " . var_export($node_list, true));

        $tarifs_iptv = $a->getTarifIptvListForForm();
        $this->logger->debug("stb\\stbAction: tarifs iptv list data: " . var_export($tarifs_iptv, true));

        // render form
        //
        $form_data = $this->stbActionRenderForm($request, $response, $csrf, $data, $node_list, $tarifs_iptv);
        // $this->logger->debug("stb\\stbAction: form_data: " . var_export($form_data, true));

        $ret[0] = $form_data;
        $ret[1] = "objekty/stb-action-form.tpl";

        return $ret;

    }

    public function stbActionRenderResults($data)
    {
        $rs .= "<br>
        STB Objekt byl přidán/upraven, zadané údaje:<br><br>
        <b>Popis objektu</b>: " . $data['popis'] . "<br>
        <b>IP adresa</b>: " . $data['ip'] . "<br>
        <b>MAC adresa</b>: " . $data['mac'] . "<br><br>
        
        <b>Puk</b>: " . $data['puk'] . "<br>
        <b>Pin1</b>: " . $data['pin1'] . "<br>
        <b>Pin2</b>: " . $data['pin2'] . "<br>

        <b>Číslo portu switche</b>: " . $data['$port_id'] . "<br>
        
        <b>Přípojný bod</b>: ";

        $vysledek3 = $this->conn_mysql->query("select jmeno, id from nod_list WHERE id='".intval($data['id_nodu'])."' ");
        $radku3 = $vysledek3->num_rows;

        if($radku3 == 0) {
            $rs .= " Nelze zjistit ";
        } else {
            while($zaznam3 = $vysledek3->fetch_array()) {
                $rs .= $zaznam3["jmeno"]." (id: ".$zaznam3["id"].") ".'';
            }
        }

        $rs .= "<br><br>";

        $rs .= "<b>Poznámka</b>:".htmlspecialchars($data['pozn'])."<br>";

        $ms_tarif = $this->conn_mysql->query("SELECT jmeno_tarifu FROM tarify_iptv WHERE id_tarifu = '".intval($data['id_tarifu'])."'");

        $ms_tarif->data_seek(0);
        $ms_tarif_r = $ms_tarif->fetch_row();

        $rs .= "<b>Tarif</b>: ".$ms_tarif_r[0]."<br><br>";

        return $rs;
    }

    public function stbActionRenderForm(ServerRequestInterface $request, ResponseInterface $response, $csrf, $data, $node_list, $tarifs_iptv_list)
    {
        $form_csrf = array(
            $csrf[1] => $csrf[3],
            $csrf[2] => $csrf[4],
        );

        for ($x = 1; $x <= 48; $x++) {
            $form_port_id[$x] = $x;
        }

        $uri = $request->getUri();

        $form_id = "stb-action-add";

        if(intval($data['update_id']) > 0) {
            $form_data['f_input_update_id'] = $this->action_form->hidden('update_id', $data['update_id']);
        }

        $form_data['f_open'] = $this->action_form->open($form_id, $form_id, $uri->getPath(), '', '', $form_csrf);
        $form_data['f_close'] = $this->action_form->close();
        $form_data['f_submit_button'] = $this->action_form->input_submit('odeslano', '', 'OK / Odeslat / Uložit');

        $form_data['f_input_popis'] = $this->action_form->text('popis', 'Popis objektu', $data['popis']);

        $form_data['f_input_nod_find'] = $this->action_form->text('nod_find', 'Přípojný bod - filtr', $data['nod_find']);

        $form_data['f_input_nod_find_button'] = $this->action_form->input_submit(
            'g1',
            '',
            'Hledat (nody)',
            '',
            'class="btn btn-secondary" '
        );

        $form_data['f_input_ip'] = $this->action_form->text('ip', 'IP adresa', $data['ip']);
        $form_data['f_input_id_nodu'] = $this->action_form->select('id_nodu', '', $data['id_nodu'], '', 'class="form-select orm-select-inline form-select-sm"', '', '', $node_list);

        $form_data['f_input_mac'] = $this->action_form->text('mac', 'mac adresa', $data['mac_adresa']);
        $form_data['f_input_gen_button'] = $this->action_form->input_submit(
            'g2',
            '',
            'Generovat údaje',
            '',
            'class="btn btn-secondary" '
        );

        $form_data['f_input_puk'] = $this->action_form->text('puk', 'puk', $data['puk']);
        $form_data['f_input_pin1'] = $this->action_form->text('pin1', 'pin1', $data['pin1']);
        $form_data['f_input_pin2'] = $this->action_form->text('pin2', 'pin2', $data['pin2']);


        $form_data['f_input_port_id'] = $this->action_form->select('port_id', 'Číslo portu (ve switchi)', $data['port_id'], '', 'class="form-select form-select-sm"', '', '', $form_port_id);

        $form_data['f_input_pozn'] = $this->action_form->textarea('pozn', 'poznámka', $data['pozn'], 'rows="5" wrap="soft"');

        $form_data['f_input_id_tarifu'] = $this->action_form->select('id_tarifu', 'Tarif', $data['id_tarifu'], '', 'class="form-select form-select-sm"', '', '', $tarifs_iptv_list);


        // print messages, formatted using Bootstrap alerts
        $form_data['f_messages'] = $this->action_form->messages();
        $form_data['f_messages_validation'] = $this->action_form_validation_errors;

        return $form_data;
    }
    public function generujdata()
    {

        // promenne ktere potrebujem, a ktere budeme ovlivnovat
        global $ip;

        //skusime ip vygenerovat
        $vysl_nod = $this->conn_mysql->query("SELECT * FROM nod_list WHERE id = '370' ");
        $radku_nod = $vysl_nod->num_rows;

        if($radku_nod <> 1) {
            $gen_ip = "E1"; //echo "chybnej vyber nodu";
        } else {

            while ($data_nod = $vysl_nod->fetch_array()) {
                $ip_rozsah = $data_nod["ip_rozsah"];
            }

            list($a, $b, $c, $d) = preg_split("/[.]/", $ip_rozsah);

            // c-ckova ip
            $gen_ip_find = $a.".".$b.".".$c.".".$d."/24";

            $msq_check_ip = $this->conn_mysql->query("SELECT * FROM objekty_stb ORDER BY ip_adresa ASC");
            $msq_check_ip_radku = $msq_check_ip->num_rows;

            if($msq_check_ip_radku == 0) { //nic v db, takze prvni adresa ...
                $d = 16;
                $gen_ip = $a.".".$b.".".$c.".".$d;
            } else {
                while($data_check_ip = $msq_check_ip->fetch_array()) {
                    $gen_ip = $data_check_ip["ip_adresa"];
                }

                list($a, $b, $c, $d) = preg_split("/[.]/", $gen_ip);

                if($d >= "250") { //jsme u stropu, vracime rozsah ...
                    $gen_ip = $a.".".$b.".".$c.".0";
                } else {
                    $d = $d + 2;
                    $gen_ip = $a.".".$b.".".$c.".".$d;
                }
            } // konec else radku == 0


            // vysledek predame
            if((strlen($ip) <= 0)) {
                $ip = $gen_ip;
            }

        }

    } //konec funkce generujdata




    public function zjistipocetobj($id_cloveka)
    {
        $sql_sloupce = " id_stb, id_cloveka, mac_adresa, puk, ip_adresa, popis, id_nodu, sw_port, pozn, datum_vytvoreni ";

        $dotaz = $this->conn_mysql->query("SELECT ".$sql_sloupce." FROM objekty_stb WHERE id_cloveka = '".intval($id_cloveka)."' ORDER BY id_stb");
        $dotaz_radku = $dotaz->num_rows;

        return $dotaz_radku;
    }

    public function generate_sql_query()
    {

        /*
        novej sql doraz

        SELECT id_stb, id_cloveka, mac_adresa, puk, ip_adresa, popis, id_nodu, sw_port, objekty_stb.pozn,
           datum_vytvoreni, DATE_FORMAT(datum_vytvoreni, '%d.%m.%Y %H:%i:%s') as datum_vytvoreni_f, nod_list.jmeno
        FROM objekty_stb, nod_list
        WHERE ( (objekty_stb.id_nodu = nod_list.id) )
        GROUP BY objekty_stb.id_stb
        ORDER BY id_stb
        */

        $sql_rows_basic = " id_stb, id_cloveka, mac_adresa, puk, ip_adresa, popis, id_nodu, sw_port, objekty_stb.pozn, datum_vytvoreni ";
        $sql_rows_extra = $sql_rows_basic . ", pin1, pin2, id_tarifu, upravil_kdo ";

        $sql_rows = $sql_rows_basic .
               ", DATE_FORMAT(datum_vytvoreni, '%d.%m.%Y %H:%i:%s') as datum_vytvoreni_f, nod_list.jmeno AS nod_jmeno ".
               ", jmeno_tarifu ";

        if(is_object($this->action_form)) {
            $this->sql_query = "SELECT ".$sql_rows_extra." FROM objekty_stb WHERE id_stb = '".intval($this->id_stb)."'";
        } elseif($this->listing_mod == 1) {

            $this->sql_query = "SELECT ".$sql_rows." FROM objekty_stb, nod_list, tarify_iptv ".
                        " WHERE ( (objekty_stb.id_nodu = nod_list.id) ".
                           " AND (objekty_stb.id_tarifu = tarify_iptv.id_tarifu) ".
                           " AND (id_cloveka = '".intval($this->id_cloveka)."') ) ".
                        " GROUP BY objekty_stb.id_stb ".
                        " ORDER BY id_stb";
        } else {

            $sql_where = "";

            if($this->find_id_nodu > 0) {
                $sql_where .= " AND (id_nodu = '".intval($this->find_id_nodu)."') ";
            }

            if(isset($this->find_par_vlastnik)) {

                if($this->find_par_vlastnik == 1) {
                    $sql_where .= " AND (id_cloveka > 0) ";
                } elseif($this->find_par_vlastnik == 2) {
                    $sql_where .= " AND (id_cloveka is NULL) ";
                } else {
                    //chyba :)
                }

            }

            if((strlen($this->find_search_string) > 0)) {

                $sql_where .= " AND (  ";

                if(preg_match("/^[0-9]+$/", $this->find_search_string)) {
                    $this->logger->debug("stb\GenerateSqlQuery: search_string is numeric");

                    $find_search_string = "".$this->conn_mysql->real_escape_string($this->find_search_string)."";

                    $sql_where .= " (id_stb = '$find_search_string') OR ".
                                " (id_cloveka = '$find_search_string') OR ".
                                " ";
                }

                $find_search_string = "%".$this->conn_mysql->real_escape_string($this->find_search_string)."%";

                $sql_where .=
                        " (mac_adresa LIKE '$find_search_string' ) OR ".
                        " (ip_adresa LIKE '$find_search_string') OR ".
                        " (puk LIKE '$find_search_string') OR ".
                        " (pin1 LIKE '$find_search_string') OR ".
                        " (pin2 LIKE '$find_search_string') OR ".
                        " (popis LIKE '$find_search_string') OR ".
                        " (objekty_stb.pozn LIKE '$find_search_string') OR ".
                        " (jmeno_tarifu LIKE '$find_search_string') OR ".
                        " (nod_list.jmeno LIKE '$find_search_string') ".
                        " ) ";
            }

            if(isset($this->id_stb)) {

                $sql_where .= " AND (id_stb = '".intval($this->id_stb)."') ";
            }

            if($this->order == 1) {
                $sql_order = " ORDER BY popis ASC ";
            } elseif($this->order == 2) {
                $sql_order = " ORDER BY popis DESC ";
            } elseif($this->order == 3) {
                $sql_order = " ORDER BY ip_adresa ASC ";
            } elseif($this->order == 4) {
                $sql_order = " ORDER BY ip_adresa DESC ";
            } elseif($this->order == 5) {
                $sql_order = " ORDER BY mac_adresa ASC ";
            } elseif($this->order == 6) {
                $sql_order = " ORDER BY mac_adresa DESC ";
            } elseif($this->order == 7) {
                $sql_order = " ORDER BY puk ASC ";
            } elseif($this->order == 8) {
                $sql_order = " ORDER BY puk DESC ";
            } elseif($this->order == 9) {
                $sql_order = " ORDER BY nod_list.jmeno ASC ";
            } elseif($this->order == 10) {
                $sql_order = " ORDER BY nod_list.jmeno DESC ";
            }

            $this->sql_query = "SELECT ".$sql_rows." FROM objekty_stb, nod_list, tarify_iptv ".
                           " WHERE ( (objekty_stb.id_nodu = nod_list.id) AND (objekty_stb.id_tarifu = tarify_iptv.id_tarifu) ".
                           $sql_where." ) "." GROUP BY objekty_stb.id_stb ".$sql_order;


        } //end of else if mod == 1

        $this->logger->debug("stb\GenerateSqlQuery: dump var this->sql_query: ".var_export($this->sql_query, true));

    } //end of function generate_sql_query

    public function vypis($mod = 0, $id_cloveka = 0)
    {

        $output = "";

        $this->listing_mod = $mod;
        $this->id_cloveka  = $id_cloveka;

        if(empty($this->sql_query)) {
            $this->generate_sql_query();
        }

        $this->logger->debug("stb\vypis: dump var this->sql_query: ".var_export($this->sql_query, true));

        $dotaz_vypis = $this->conn_mysql->query($this->sql_query);
        $dotaz_vypis_radku = $dotaz_vypis->num_rows;

        if($this->debug == 1) {

            $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\" >
                    <div style=\"color: red; font-weight: bold; \" >debug sql: ".$this->sql_query.

                    "<br>var search: ".$this->find_search_string.
                    "</div>
                    </td></tr>\n";

            $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\"><br></td></tr>\n";

        }

        if(!$dotaz_vypis) {

            $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\" >
                    <div style=\"color: red; font-weight: bold; \" >error in function \"vypis\": mysql: "
                    //    . mysql_errno().": ".mysql_error()
                    ."</div>"
                    ."</td></tr>";

            $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\"><br></td></tr>";

        }

        if(($dotaz_vypis_radku == 0) and ($mod != 1)) {

            $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\" >
                    <div style=\"color: red; font-weight: bold; \" >Žádný set-top-box nenalezen.</div>
                    </td></tr>";

            $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\"><br></td></tr>";
        } else {
            $class_stb_liche = "border-bottom: 1px dashed gray; font-size: 15px; ";
            $class_stb_sude = "border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px; ";

            while($data_vypis = $dotaz_vypis->fetch_array()) {
                $output .= "
           <tr>
           <td style=\"".$class_stb_liche."\" >".$data_vypis["popis"]."&nbsp;</td>
           <td style=\"".$class_stb_liche."\" >".$data_vypis["ip_adresa"]."&nbsp;</td>\n";

                //pozn
                $output .= "<td style=\"".$class_stb_liche."\" ><span class=\"pozn\"><img title=\"poznamka\" src=\"/img2/poznamka3.png\" alt=\"poznamka\" ";
                $output .= " onclick=\"window.alert(' poznámka: ".htmlspecialchars($data_vypis["pozn"])." , Vytvořeno: ".$data_vypis["pridano"]." ');\" ></span>\n</td>\n";

                //mac adresa
                $output .= "<td style=\"".$class_stb_liche."\" >\n";

                $output .= "<div style=\"float: left; width: 135px; padding-top: 2px;\" >".htmlspecialchars($data_vypis["mac_adresa"])."</div>";

                $p_link1 = "http://app01.cho01.iptv.local:9080/admin/admin/provisioning/stb-search.html?".
                "searchText=".urlencode($data_vypis["mac_adresa"])."&amp;type=".urlencode("MAC_ADDRESS")."&amp;submit=OK";

                $output .= "<div style=\"float: left;\" >".
                "<a href=\"".$p_link1."\" target=\"_new\" >".
                   "<img src=\"/img2/Letter-P-icon-small.png\" alt=\"letter-p-small\" width=\"20px\" >".
                "</a>".
                 "</div>";

                $output .= "<div style=\"clear: both;\" ></div>";

                //$output .= "</div>";

                $output .= "</td>\n";

                //uprava
                $output .= "<td style=\"".$class_stb_liche."\" >";

                // if( !( check_level($this->level,137) ) )
                if($this->enable_modify_action === true) {
                    $output .=
                    "<form method=\"POST\" action=\"/objekty/stb/action\" >\n"
                    . "<input type=\"hidden\" name=\"update_id\" value=\"".intval($data_vypis["id_stb"])."\" >\n"
                    . $this->csrf_html ."\n"
                    . "<input class=\"\" type=\"submit\" value=\"update\" >\n"
                    . "</form>\n";
                } else {
                    $output .= "<div style=\"\" style=\"".$class_stb_liche."\" >úprava</div>\n";
                }

                $output .= "</td>\n";

                //smazani
                $output .= "<td style=\"".$class_stb_liche."\" >\n";

                $output .= "<div style=\"\" ><a href=\"" . fix_link_to_another_adminator(
                    "/objekty-stb-erase.php?".
                    urlencode("id_stb")."=".intval($data_vypis["id_stb"])
                )."\" >smazání</a>".
                     "</div>";

                $output .= "</td>\n";

                //test
                $output .= "<td style=\"".$class_stb_liche."\" >
            <a href=\"" . fix_link_to_another_adminator("/objekty-test.php?".urlencode("id_stb")."=".intval($data_vypis["id_stb"]))."\" >test</a>
           </td>\n";

                //tarif
                $output .= "<td style=\"".$class_stb_liche."\" >".htmlspecialchars($data_vypis["jmeno_tarifu"])."</td>\n";

                //druhej radek
                $output .= "</tr>\n".
                "<tr>\n";

                //pripojny bod / nod
                $output .= "<td style=\"".$class_stb_sude."\" >\n";

                $output .= "<span class=\"objekty-2radka objekty-odkaz\">".
                          "<a href=\"/topology/node-list?".urlencode("typ_nodu")."=2&".urlencode("find")."=".urlencode($data_vypis["nod_jmeno"]) . "\" >".
                          $data_vypis["nod_jmeno"]."</a>".
                    "</span>";
                $output .= "</td>\n";

                //puk
                $output .= "<td style=\"".$class_stb_sude."\" >".$data_vypis["puk"]."&nbsp;</td>\n";

                //id stb (historie)
                $output .= "<td style=\"".$class_stb_sude."\" >H: \n";
                $output .= "<a href=\"/archiv-zmen?".urlencode("id_stb")."=".intval($data_vypis["id_stb"]) ."\" >".$data_vypis["id_stb"]."</a>\n";
                $output .= "</td>\n";

                //vlastnik - id cloveka
                $id_cloveka = $data_vypis["id_cloveka"];

                $rs_create_link = ($id_cloveka > 0 ? \Aglobal::create_link_to_owner($id_cloveka, $this->conn_pgsql) : "");

                $odkaz_data = ($rs_create_link === false ? "E_1" : $rs_create_link);

                $output .= "<td style=\"".$class_stb_sude."\" >V: ".$rs_create_link."&nbsp;</td>";

                $output .= "<td style=\"".$class_stb_sude."\" >".$data_vypis["sw_port"]."&nbsp;</td>";

                $output .= "<td colspan=\"2\" style=\"".$class_stb_sude."\" >";

                $output .= ($data_vypis["datum_vytvoreni_f"] == 0 ? "nelze zjistit " : $data_vypis["datum_vytvoreni_f"]);

                $output .= "</td>";

                //generovani Reg. Formu
                if((intval($data_vypis["id_cloveka"]) > 0)) {

                    $rs_rf = pg_query("SELECT id_komplu FROM objekty WHERE id_cloveka = '".intval($data_vypis["id_cloveka"])."'");

                    while($data_rf = pg_fetch_array($rs_rf)) {
                        $id_komplu = $data_rf["id_komplu"];
                    }

                    if((intval($id_komplu) > 0)) {

                        $output .= "<td style=\"".$class_stb_sude."\" >".
                        "<a href=\"/print/reg-form-pdf.php?".urlencode("id_vlastnika")."=".intval($id_komplu)."\">R.F.</a>".
                        "</td>";

                    } else {
                        $output .= "<td style=\"".$class_stb_sude."\">E</td>";
                    }

                } else {
                    $output .= "<td style=\"".$class_stb_sude."\" >".
                    "<a href=\"/print/reg-form-pdf.php?".urlencode("id_stb")."=".intval($data_vypis["id_stb"])."\">R.F.</a>".
                    "</td>";
                }

                //zbytek
                if($mod == 1) {

                    // if( check_level($this->level, 152) )
                    if($this->enable_unpair_action === true) {
                        $output .= "<td style=\"".$class_stb_sude."\" ><a href=\"objekty-stb-unpairing.php?id=".intval($data_vypis["id_stb"])."\" >odendat</a></td>";
                    } else {
                        $output .= "<td style=\"".$class_stb_sude."\" ><div style=\"color: gray; \" >odendat</div></td>";
                    }
                } else {
                    //$output .= "<td style=\"".$class_stb_sude."\" >&nbsp;</td>";
                }

                $output .= "</tr>\n";

            } //konec while

        } //konec else if $dotaz_vypis_radku == 0

        return $output;

    } //konec funkce vypis

}

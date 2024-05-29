<?php

namespace App\Core\Topology;

use Exception;
use App\Core\adminator;
use Symfony\Component\HttpFoundation\Request;
use Psr\Container\ContainerInterface;

class Topology extends adminator
{
    public $container;

    public ?\PDO $pdoMysql;

    public \mysqli|\PDO $conn_mysql;

    public $smarty;

    public \Monolog\Logger $logger;

    protected $settings;

    public $csrf_html;

    private $requestData;

    public function __construct(ContainerInterface $container)
    {
        $this->conn_mysql = $container->get('connMysql');
        $this->pdoMysql = $container->get('pdoMysql');

        $this->smarty = $container->get('smarty');
        $this->logger = $container->get('logger');
        $this->settings = $container->get('settings');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->requestData = Request::createFromGlobals();
    }

    public function getNodeListForForm($search_string, int $typ_nodu = 2, $show_zero_value = true)
    {
        $this->logger->info("topology\getNodesFiltered called");

        if($show_zero_value === true) {
            $nodes[0] = "Není vybráno";
        }

        $search_string = $this->conn_mysql->real_escape_string($search_string);

        $sql = "SELECT id, jmeno, ip_rozsah from nod_list WHERE ( jmeno LIKE '%$search_string%' ";
        $sql .= " OR ip_rozsah LIKE '%$search_string%' OR adresa LIKE '%$search_string%' ";
        $sql .= " OR pozn LIKE '%$search_string%' ) AND ( typ_nodu = '" . $typ_nodu . "' ) ORDER BY jmeno ASC ";

        $rs = $this->conn_mysql->query($sql);
        $num_rows = $rs->num_rows;

        if($num_rows < 1) {
            $nodes[0] = "nelze zjistit / žádný nod nenalezen";
            return $nodes;
        } else {
            while ($data = $rs->fetch_array()) {
                $nodes[$data['id']] = $data["jmeno"] . " (".$data["ip_rozsah"].")";
            }

            return $nodes;
        }
    }

    public function getNodeList(): string
    {
        $output = "";

        // prepare vars
        //
        $list = $this->requestData->query->get('list');
        $typ_nodu = $this->requestData->query->get('typ_nodu');
        $ping = $this->requestData->query->get('ping');
        $find = $this->requestData->query->get('find');
        $razeni = $this->requestData->query->get('razeni');
        $typ_vysilace = $this->requestData->query->get('typ_vysilace');
        $stav = $this->requestData->query->get('stav');

        // $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": query param typ_nodu: " . var_export($typ_nodu, true));

        if(is_null($typ_nodu) or $typ_nodu < 0) {
            $typ_nodu = 0;
        }

        if((strlen($find) < 1)) {
            $find = "%";
            $find_orez = "";
        } else {
            if(!(preg_match("/%.*%/", $find))) {
                $find = "%".$find."%";
            }
            $find_orez = str_replace("%", "", $find);
        }

        // "list" header
        $output .= "<div style=\"padding-top: 10px; padding-bottom: 20px;\" >
            <span style=\"padding-left: 20px; font-size: 20px; font-weight: bold; \">
            Výpis lokalit / přípojných bodů
            </span>
            <span style=\"padding-left: 80px; \" ><!--<a href=\"include/export-topology.php\" >-->export lokalit/nodů<!--</a>--></span>  
        
            <span style=\"padding-left: 80px; \" >
            Výpis lokalit/nodů s latencemi ";

        if($ping == 1) {
            $output .= "<a href=\"/topology/node-list?razeni=".$razeni."&ping=&find=".$find_orez;
            $output .= "&list=".$list."&typ_nodu=".$typ_nodu."\">vypnout</a>";
        } else {
            $output .= "<a href=\"/topology/node-list?razeni=".$razeni."&ping=1&find=".$find_orez;
            $output .= "&list=".$list."&typ_nodu=".$typ_nodu."\">zapnout</a>";
        }

        $output .= "</span>
        </div>";

        // filter/search
        //
        $output .= "<div style=\"padding-left: 20px; padding-bottom: 10px;\" >
            <form action=\"/topology/node-list\" method=\"GET\" >
                        
                <input type=\"hidden\" name=\"razeni\" value=\"".$razeni."\" >
                <input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >
                    
                <span style=\"font-weight: bold; \" >Hledání:</span>
                
                <span style=\"padding-left: 10px; \" >
                    <input type=\"text\" name=\"find\" size=\"15\" value=\"".$find_orez."\" style=\"font-size: 12px; \" >
                </span>
            
            
            <span style=\"padding-left: 10px; \" ><span style=\"color: grey; font-weight: bold; \">filtr:</span></span>
            
            <span style=\"padding-left: 10px; \" >typ:</span>
            
            <span style=\"padding-left: 10px; \" >
                <select name=\"typ_vysilace\" size=\"1\">
                <option value=\"0\" class=\"select-nevybrano\" >Nevybráno</option>    
                <option value=\"1\" ";
        if($typ_vysilace == 1) {
            $output .= " selected ";
        } $output .= ">Metallic</option>    
                    
                <option value=\"2\" ";
        if($typ_vysilace == 2) {
            $output .= " selected ";
        } $output .= ">ap-2,4GHz-OMNI</option>    
                    <option value=\"3\" ";
        if($typ_vysilace == 3) {
            $output .= " selected ";
        } $output .= ">ap-2,4GHz-sektor</option>    
                    <option value=\"4\" ";
        if($typ_vysilace == 4) {
            $output .= " selected ";
        } $output .= ">ap-2.4GHz-smerovka</option>    
                    <option value=\"5\" ";
        if($typ_vysilace == 5) {
            $output .= " selected ";
        } $output .= ">ap-5.8Ghz-OMNI</option>    
                    <option value=\"6\" ";
        if($typ_vysilace == 6) {
            $output .= " selected ";
        } $output .= ">ap-5.8Ghz-sektor</option>
                    <option value=\"7\" ";
        if($typ_vysilace == 7) {
            $output .= " selected ";
        } $output .= ">ap-5.8Ghz-smerovka</option>
                <option value=\"8\" ";
        if($typ_vysilace == 8) {
            $output .= " selected ";
        } $output .= ">jiné</option>
                    
                </select>
            </span>

            <span style=\"padding-left: 20px; \" >stav: </span>

            <span style=\"padding-left: 10px; \" >
                <select name=\"stav\" size=\"1\" >
                <option value=\"0\" class=\"select-nevybrano\">Nevybráno</option>
                <option value=\"1\" ";
        if($stav == 1) {
            $output .= " selected ";
        } $output .= ">V pořádku</option>
                <option value=\"2\" ";
        if($stav == 2) {
            $output .= " selected ";
        } $output .= ">Vytížen</option>
                <option value=\"3\" ";
        if($stav == 3) {
            $output .= " selected ";
        } $output .= ">Přetížen</option>
                </select>
            </span>
            
            <span style=\"padding-left: 10px; \" >mód:</span>
                <select name=\"typ_nodu\" size=\"1\" >
                <option value=\"0\" class=\"select-nevybrano\">Nevybráno</option>
                <option value=\"1\" style=\"color: #CC0033; \" ";
        if($typ_nodu == 1) {
            $output .= " selected ";
        }
        $output .= ">bezdrátová síť</option>
                
                <option value=\"2\" style=\"color: #e37d2b; font-weight: bold;\" ";
        if($typ_nodu == 2) {
            $output .= " selected ";
        }
        $output .= ">optická síť</option>
                </select>
            
            <span style=\"padding-left: 30px; \" ><input type=\"submit\" name=\"odeslat\" value=\"OK\" ></span>
            
            </form>
        </div>
        
        <div style=\"padding-left: 20px; padding-bottom: 10px; \" >
            <span style=\"font-weight: bold; padding-right: 10px; \">Hledaný výraz:</span> ".$find."
        </div>";

        //aby se stihli pingy
        // set_time_limit(0);

        // tvoreni dotazu
        //
        if ($razeni == 1) {
            $order = " order by id asc";
        } elseif ($razeni == 2) {
            $order = " order by id desc";
        } elseif ($razeni == 3) {
            $order = " order by jmeno asc";
        } elseif ($razeni == 4) {
            $order = " order by jmeno desc";
        } elseif ($razeni == 5) {
            $order = " order by adresa asc";
        } elseif ($razeni == 6) {
            $order = " order by adresa desc";
        } elseif ($razeni == 7) {
            $order = " order by pozn asc";
        } elseif ($razeni == 8) {
            $order = " order by pozn desc";
        } elseif ($razeni == 9) {
            $order = " order by ip_rozsah asc";
        } elseif ($razeni == 10) {
            $order = " order by ip_rozsah desc";
        } elseif ($razeni == 11) {
            $order = " order by umisteni_aliasu asc";
        } elseif ($razeni == 12) {
            $order = " order by umisteni_aliasu desc";
        } elseif ($razeni == 13) {
            $order = " order by mac asc";
        } elseif ($razeni == 14) {
            $order = " order by mac desc";
        } else {
            $order = "";
        }

        $where = " WHERE ( id = '$find' OR jmeno LIKE '$find' OR adresa LIKE '$find' ";
        $where .= "OR pozn LIKE '$find' OR ip_rozsah LIKE '$find' ) ";

        if($typ_vysilace > 0) {
            $where .= "AND ( typ_vysilace = '$typ_vysilace' ) ";
        }

        if($stav > 0) {
            $where .= "AND ( stav = '$stav' ) ";
        }

        if($typ_nodu > 0) {
            $where .= " AND ( typ_nodu = '$typ_nodu' ) ";
        }

        $sql = "select * from nod_list ".$where." ".$order;

        $url_listing = "/topology/node-list?razeni=".$razeni."&ping=".$ping;
        $url_listing .= "&typ_vysilace=".$typ_vysilace."&stav=".$stav."&find=".$find_orez;
        $url_listing .= "&typ_nodu=".$typ_nodu;

        $paging = new c_listing_topology(
            $this->pdoMysql,
            $url_listing,
            $this->settings['app']['core']['topology']['node']['listing_interval'],
            $list,
            "<center><div class=\"text-listing\">\n",
            "</div></center>\n",
            $sql." ; "
        );

        if (($list == "") || ($list == "1")) {    //pokud není list zadán nebo je první
            $bude_chybet = 0; //bude ve výběru sql dotazem chybet 0 záznamů
        } else {
            $bude_chybet = (($list - 1) * $paging->interval);    //jinak jich bude chybet podle závislosti na listu a intervalu
        }

        // $vysledek = $this->conn_mysql->query($sql . " LIMIT ".$bude_chybet.",".$paging->interval." ");
        try {
            $rs = $this->pdoMysql->query($sql . " LIMIT ".$bude_chybet.",".$paging->interval." ");
            $rs_data = $rs->fetchAll();
        } catch (Exception $e) {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": Database query failed! Caught exception: " . $e->getMessage());
        }

        $output .= "<div style=\"padding-top: 10px; padding-bottom: 10px; \" >".$paging->listInterval()."</div>";    //zobrazení stránkovače

        $radku = count($rs_data);

        if ($radku == 0) {
            $output .= "<div style=\"padding-top: 15px; padding-left: 15px;\" class=\"alert alert-warning\" role=\"alert\">"
                        . "Žadné lokality/nody dle hladeného výrazu ( ".$find." ) v databázi neuloženy."
                        . "</div>";
            // $output .= "<div >debug: sql: ".$sql." </div>";
        } else {
            // $output .= '<br>Výpis lokalit/nodů: <span style="color: silver">řazeno dle id: '.$_POST["razeni"].'</span><BR><BR>';

            $colspan_id = "1";
            $colspan_jmeno = "3";
            $colspan_adresa = "3";
            $colspan_pozn = "2";
            $colspan_rozsah_ip = "1";
            $colspan_typ_nodu = "1";
            $colspan_umisteni = "2";

            $colspan_celkem = $colspan_id + $colspan_jmeno + $colspan_adresa + $colspan_pozn + $colspan_rozsah_ip + $colspan_typ_nodu + $colspan_umisteni;

            $output .= "<table border=\"0\" >";

            // $output .= "<tr><td colspan=\"".$colspan_celkem."\"><hr></td></tr>";

            $output .= "\n<tr>
                <td width=\"5%\" colspan=\"".$colspan_id."\"  class=\"tab-topology2 tab-topology-dolni2\" >
                
                <table border=\"0\" width=\"\" >
                <tr>
                    <td><b>id:</b></td>";

            $output .= "<td>";
            $output .= "<form name=\"form1\" method=\"GET\" action=\"\" > ";
            $output .= "<input type=\"hidden\" name=\"razeni\" value=\"1\" >";

            $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
            $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
            $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";

            $output .= "<a href=\"javascript:self.document.forms.form1.submit()\">
                <img src=\"/img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";

            $output .= "<form  name=\"form2\" method=\"GET\" action=\"\" > ";
            $output .= "<input type=\"hidden\" name=\"razeni\" value=\"2\">";

            $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
            $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
            $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";

            $output .= "<a href=\"javascript:self.document.forms.form2.submit()\">";
            $output .= "<img src=\"/img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";

            $output .= "</td></tr></table>";

            $output .= "</td>";

            $output .= "<td width=\"20%\" colspan=\"".$colspan_jmeno."\" class=\"tab-topology2 tab-topology-dolni2\" >
            
            <table border=\"0\" width=\"100%\" >
            <tr>
                <td><b>Jméno: </b></td>";

            $output .= "<td >";
            $output .= "<form  name=\"form3\" method=\"GET\" action=\"\" >";
            $output .= "<input type=\"hidden\" name=\"razeni\" value=\"3\" >";

            $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
            $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
            $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";

            $output .= "<a href=\"javascript:self.document.forms.form3.submit()\">
                <img src=\"/img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";

            $output .= "<form  name=\"form4\" method=\"GET\" action=\"\" >";
            $output .= "<input type=\"hidden\" name=\"razeni\" value=\"4\">";

            $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
            $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
            $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";

            $output .= "<a href=\"javascript:self.document.forms.form4.submit()\">";
            $output .= "<img src=\"/img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";

            $output .= "</td></tr></table>";

            $output .= "</td>";


            $output .= "<td colspan=\"".$colspan_adresa."\" class=\"tab-topology2 tab-topology-dolni2\" >
            
            <table border=\"0\" width=\"100%\" >
            <tr>
                <td><b>Adresa: </b></td>";

            $output .= "<td>";
            $output .= "<form  name=\"form5\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
            $output .= "<input type=\"hidden\" name=\"razeni\" value=\"5\" >";

            $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
            $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
            $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";

            $output .= "<a href=\"javascript:self.document.forms.form5.submit()\">
                <img src=\"/img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";

            $output .= "<form  name=\"form6\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
            $output .= "<input type=\"hidden\" name=\"razeni\" value=\"6\">";

            $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
            $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
            $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";

            $output .= "<a href=\"javascript:self.document.forms.form6.submit()\">";
            $output .= "<img src=\"/img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";

            $output .= "</td></tr></table>";

            $output .= "</td>";

            $output .= "<td colspan=\"1\" class=\"tab-topology2 tab-topology-dolni2\" >
            
            <table border=\"0\" width=\"100%\" >
            <tr>
                <td><b>Poznámka: </b></td>";

            $output .= "<td>";
            $output .= "<form  name=\"form7\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
            $output .= "<input type=\"hidden\" name=\"razeni\" value=\"7\" >";

            $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
            $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
            $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";

            $output .= "<a href=\"javascript:self.document.forms.form7.submit()\">
                <img src=\"/img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";

            $output .= "<form  name=\"form8\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
            $output .= "<input type=\"hidden\" name=\"razeni\" value=\"8\">";

            $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
            $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
            $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";

            $output .= "<a href=\"javascript:self.document.forms.form8.submit()\">";
            $output .= "<img src=\"/img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";

            $output .= "</td></tr></table>";

            $output .= "</td>";

            $output .= "<td colspan=\"1\" width=\"15%\" class=\"tab-topology2 tab-topology-dolni2\" align=\"center\" >
            <b>Vlan ID</b><br></td>";


            $output .= "<td width=\"10%\" colspan=\"".$colspan_rozsah_ip."\" class=\"tab-topology2 tab-topology-dolni2\" >
            
            <table border=\"0\" width=\"100%\" >
            <tr>
                <td><b>Rozsah ip adres: </b></td>";

            $output .= "<td>";
            $output .= "<form  name=\"form9\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
            $output .= "<input type=\"hidden\" name=\"razeni\" value=\"9\" >";

            $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
            $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
            $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";

            $output .= "<a href=\"javascript:self.document.forms.form9.submit()\">
                <img src=\"/img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";

            $output .= "<form  name=\"form10\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
            $output .= "<input type=\"hidden\" name=\"razeni\" value=\"10\">";

            $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
            $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
            $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";

            $output .= "<a href=\"javascript:self.document.forms.form10.submit()\">";
            $output .= "<img src=\"/img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";

            $output .= "</td></tr></table>";

            $output .= "</td>";

            $output .= "<td width=\"10%\" colspan=\"".$colspan_typ_nodu."\" class=\"tab-topology2 tab-topology-dolni2\" >
                <b>Mód nodu</b></td>";

            // bunky druhej radek
            $colspan_filtrace = "1";

            // $colspan_mac = "3";

            $colspan_typ_vysilace = "3";
            $colspan_aktivni = "1";
            $colspan_stav = "1";

            $colspan_uprava = "2";
            $colspan_smazani = "2";

            $output .= "
            
            </tr>
            
            <tr>
            <td colspan=\"1\" class=\"tab-topology2\" ><br></td>
            <td colspan=\"3\" class=\"tab-topology2\" >
                <span style=\"color: #666666; font-weight: bold; \">Umístění aliasu (název routeru): </span></td>
            
            <td colspan=\"".$colspan_typ_vysilace."\" class=\"tab-topology2\" ><span style=\"color: #666666; font-weight: bold; \">Typ vysílače: </span></td>
            
            <td colspan=\"".$colspan_aktivni."\" class=\"tab-topology2\" align=\"center\" >
            <span style=\"color: #666666; font-weight: bold; \">Aktivní: </span></td>
            <td colspan=\"".$colspan_stav."\" class=\"tab-topology2\" align=\"center\" ><span style=\"color: #666666; font-weight: bold; \">Stav: </span></td>
                    
            <td colspan=\"".$colspan_uprava."\" class=\"tab-topology2\" ><span style=\"color: #666666; font-weight: bold; \">Úprava / Smazání: </span></td>
            
                </tr>\n";

            //treti radek
            $output .= "<tr><td colspan=\"".$colspan_celkem."\"><hr></td></tr>";

            //vnejsi tabulka
            $output .= "</tr>";

            $output .= "<tr>";

            $output .= "\n";

            foreach ($rs_data as $row => $zaznam) {
                $id = $zaznam["id"];

                // prvni radek
                $output .= "<tr>";
                $output .= "<td colspan=\"".$colspan_id."\"><span style=\"font-size: 12px; padding-right: 5px;\" >";
                $output .= $id."</span><a name=\"".$id."\" ></a>";
                $output .= "</td>\n";

                $output .= "<td colspan=\"".$colspan_jmeno."\">
                        <span style=\"font-weight: bold; font-size: 14px; \">".
                        "<a href=\"/topology/user-list?vysilac=".intval($zaznam["id"])."\" >".$zaznam["jmeno"]."</a>".
                        "</span>\n".
                    "</td>\n";
                $output .= "<td colspan=\"".$colspan_adresa."\" >".
                        "<span style=\"font-size: 13px; padding-right: 10px; \">".$zaznam["adresa"]."</span>".
                        "<a href=\"\"><a href=\"http://www.mapy.cz?query=".$zaznam["adresa"]."\" target=\"_blank\" >na mapě</a>".
                    "</td>\n";

                {
                    $output .= "<td colspan=\"1\" ><span style=\"font-size: 13px; \">".$zaznam["pozn"]."</span></td>\n";
                    $output .= "<td colspan=\"1\" align=\"center\">
                        <span style=\"font-size: 13px; \">".$zaznam["vlan_id"]."</span>
                    </td>\n";

                }
                //else{ $output .= "<td colspan=\"".$colspan_pozn."\" ><span style=\"font-size: 13px; \">".$zaznam["pozn"]."</span></td>\n";  }

                $output .= "<td colspan=\"".$colspan_rozsah_ip."\" ><span style=\"font-size: 13px; \">".$zaznam["ip_rozsah"]."</span></td>\n";
                $output .= "<td colspan=\"".$colspan_typ_nodu."\" ><span style=\"font-size: 13px; \">";
                if($zaznam["typ_nodu"] == 0) {
                    $output .= "Nezvoleno";
                } elseif($zaznam["typ_nodu"] == 1) {
                    $output .= "<span style=\"color: #CC0033; \">bezdrátová síť</span>";
                } elseif($zaznam["typ_nodu"] == 2) {
                    $output .= "<span style=\"color: #e37d2b; font-weight: bold; \" >optická síť</span>";
                }

                $output .= "</span></td>\n";

                $output .= "<td colspan=\"".$colspan_umisteni."\" rowspan=\"2\" class=\"tab-topology\"><span style=\"font-size: 13px; \">";

                $output .= "</span></td>\n";

                $output .= "</tr>";

                // druhej radek
                $output .= "<tr>";

                $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_filtrace."\" >
                <a href=\"/archiv-zmen?id_nodu=".intval($id). "\" style=\"font-size: 12px; \">H: ".$id."</a>".
                "</td>\n";

                $output .= "<td class=\"tab-topology\" colspan=\"3\">
                <span style=\"color: #666666; font-size: 13px; padding-right: 10px; \" >";

                $router_id = $zaznam["router_id"];

                if ($router_id <= 0) {
                    $router_nazev = "<span style=\"color: red\">nelze zjistit </span>";
                    $router_ip = "";
                } else {
                    $vysledek_router = $this->conn_mysql->query("SELECT nazev, ip_adresa FROM router_list where id = ".intval($router_id)." ");
                    while($data_router = $vysledek_router->fetch_array()) {
                        $router_nazev = $data_router["nazev"];
                        $router_ip = $data_router["ip_adresa"];
                    }
                }

                $output .= "<span style=\"color: teal; \">".$router_nazev."</span> ".$router_ip."</span>";
                $output .= "<a href=\"/topology/router-list?odeslano=OK&f_search=".$router_ip."&\">link</a>";

                $output .= "</td>\n";

                $typ_vysilace = $zaznam["typ_vysilace"];

                if ($typ_vysilace == 1) {
                    $typ_vysilace2 = "Metallic";
                } elseif ($typ_vysilace == 2) {
                    $typ_vysilace2 = "ap-2,4GHz-OMNI";
                } elseif ($typ_vysilace == 3) {
                    $typ_vysilace2 = "ap-2,4Ghz-sektor";
                } elseif ($typ_vysilace == 4) {
                    $typ_vysilace2 = "ap-2.4Ghz-smerovka";
                } elseif ($typ_vysilace == 5) {
                    $typ_vysilace2 = "ap-5.8Ghz-OMNI";
                } elseif ($typ_vysilace == 6) {
                    $typ_vysilace2 = "ap-5.8Ghz-sektor";
                } elseif ($typ_vysilace == 7) {
                    $typ_vysilace2 = "ap-5.8Ghz-smerovka";
                } elseif ($typ_vysilace == 8) {
                    $typ_vysilace2 = "jiné";
                } else {
                    $typ_vysilace2 = $typ_vysilace;
                }

                $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_typ_vysilace."\" ><span style=\"color: #666666; font-size: 13px; \">".$typ_vysilace2."</span> </td>\n";

                list($a, $b, $c, $d) = preg_split("/[.]/", $zaznam["ip_rozsah"]);

                if ($c == 0) {
                    $c = 1;
                }

                $d = 1;
                $ip_akt = $a.".".$b.".".$c.".".$d;

                $akt_par = "class=\"tab-topology\" colspan=\"".$colspan_aktivni."\" ";

                if (($ping == 1)) {
                    $aktivni = exec("scripts/ping.sh $ip_akt");

                    if (($aktivni > 0 and $aktivni < 50)) {
                        $output .= "<td ".$akt_par." align=\"center\" bgcolor=\"green\"><span style=\"color: white; font-size: 13px; \">".$aktivni."</span>";
                    } elseif ($aktivni > 0) {
                        $output .= "<td ".$akt_par." align=\"center\" bgcolor=\"orange\"><span style=\"color: white; font-size: 13px; \">".$aktivni."</span>";
                    } else {
                        $output .= "<td ".$akt_par." align=\"center\" bgcolor=\"red\">";
                        $output .= "<br>";
                    }
                } else {
                    $output .= "<td ".$akt_par." align=\"center\" ><span style=\"color: #666666; font-size: 13px; \">N/A</span>";
                }

                $output .= "</td>";

                if ($zaznam["stav"] == 1) {
                    $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_stav."\" bgcolor=\"green\" align=\"center\" >
                        <span style=\"color: white; font-size: 13px; \"> v pořádku </span></td>";
                } elseif ($zaznam["stav"] == 2) {
                    $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_stav."\" bgcolor=\"orange\" align=\"center\" >
                        <span style=\"color: white; font-size: 13px; \"> vytížen </span></td>";
                } elseif($zaznam["stav"] == 3) {
                    $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_stav."\" bgcolor=\"red\" align=\"center\" >
                        <span style=\"color: white; font-size: 13px; \"> přetížen </span></td>";
                } else {
                    $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_stav."\" bgcolor=\"silver\" align=\"center\" >
                        <span style=\"color: black; font-size: 13px; \"> nezvoleno </span></td>";
                }

                $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_uprava."\" >";

                //vnitrni tabulka
                $output .= "<table width=\"100%\" border=\"0\"><tr>";

                // upraveni
                $output .= "<td><form method=\"POST\" action=\"/topology/nod-update\">
                <input type=\"hidden\" name=\"update_id\" value=\"".$id."\">
                <input type=\"submit\" value=\"update\">
                </form>
                </td>";

                //smazani
                //$output .= "<td class=\"tab-topology\" colspan=\"\" >";

                $output .= "<td><form action=\"/topology/nod-erase\" method=\"POST\" >";
                $output .= "<input type=\"hidden\" name=\"erase_id\" value=\"".$id."\">";
                $output .= "<input type=\"submit\" value=\"Smazat\">
                    </form>
                    </td>";

                //konec vnirni tabulky
                $output .= "</tr></table>";

                $output .= "</td>";

                $output .= "</tr>";

            }
        }

        $output .= "</table>";

        $output .= "<div style=\"padding-top: 20px; margin-bottom: 20px; \" >"
                    . "<span style=\"margin-top: 5px; margin-bottom: 15px; \">".$paging->listInterval()
                  . "</div>";

        // TODO: show printing error from paging class
        // echo $paging->msqError;

        return $output;
    }

    public function getRouterList()
    {
        $output = "";

        // prepare vars
        //
        $typ = $_GET["typ"];

        $arr_sql_where = array();

        if((strlen($_GET["filtrace"]) > 0)) {
            $filtrace = intval($_GET["filtrace"]);
        } else {
            $filtrace = 99;
        }

        if((strlen($_GET["f_monitoring"]) > 0)) {
            $f_monitoring = intval($_GET["f_monitoring"]);
        } else {
            $f_monitoring = 99;
        }

        if((strlen($_GET["f_alarm"]) > 0)) {
            $f_alarm = intval($_GET["f_alarm"]);
        } else {
            $f_alarm = 99;
        }

        if((strlen($_GET["f_alarm_stav"]) > 0)) {
            $f_alarm_stav = intval($_GET["f_alarm_stav"]);

            if($f_alarm_stav == 0 or $f_alarm_stav == 1 or $f_alarm_stav == 2) {
                $f_alarm = 1;
            }
        } else {
            $f_alarm_stav = 99;
        }

        if((strlen($_GET["f_id_routeru"]) > 0)) {
            $f_id_routeru = intval($_GET["f_id_routeru"]);
        }

        if((strlen($_GET["f_search"]) > 0)) {
            $f_search = $_GET["f_search"];
        }

        if((strlen($_GET["list"]) > 0)) {
            $list = intval($_GET["list"]);
        }

        if((strlen($_GET["odeslano"]) > 0)) {
            $odeslano = $_GET["odeslano"];
        }

        if($_GET["odeslano"] == "OK") {
            $display = "visible";
        } else {
            $display = "none";
        }

        $get_odkazy = "".
              urlencode("f_monitoring")."=".urlencode($f_monitoring).
                  "&".urlencode("filtrace")."=".urlencode($filtrace).
                  "&".urlencode("f_alarm")."=".urlencode($f_alarm).
                  "&".urlencode("f_alarm_stav")."=".urlencode($f_alarm_stav).
                  "&".urlencode("odeslano")."=".urlencode($odeslano).
                  "&".urlencode("f_search")."=".urlencode($f_search).
                  "&".urlencode("f_id_routeru")."=".urlencode($f_id_routeru).
              "";

        //priprava filtracnich podminek do pole

        if($filtrace == 0 or $filtrace == 1) {
            $arr_sql_where[] = "router_list.filtrace = '".$filtrace."'";
        }

        if($f_monitoring == 0 or $f_monitoring == 1) {
            $arr_sql_where[] = "router_list.monitoring = '".$f_monitoring."'";
        }

        if($f_alarm == 0 or $f_alarm == 1) {
            $arr_sql_where[] = "router_list.alarm = '".$f_alarm."'";
        }

        if($f_alarm_stav == 0 or $f_alarm_stav == 1 or $f_alarm_stav == 2) {
            $arr_sql_where[] = " router_list.alarm_stav = '".$f_alarm_stav."'  ";
        }

        if($f_id_routeru > 0) {
            $arr_sql_where[] = "router_list.id = '".$f_id_routeru."'";
        }

        if(isset($f_search)) {
            $f_search_safe = $this->conn_mysql->real_escape_string($f_search);

            $arr_sql_where[] = "( router_list.nazev LIKE '%".$f_search_safe."%' OR ".
                    " router_list.ip_adresa LIKE '%".$f_search_safe."%' OR ".
                    " router_list.mac LIKE '%".$f_search_safe."%' OR ".
                    " router_list2.nazev LIKE '%".$f_search_safe."%' OR ".
                    " kategorie.jmeno LIKE '%".$f_search_safe."%' ".
                    " ) ";
        }

        if((count($arr_sql_where) == 1)) {

            foreach ($arr_sql_where as $key => $val) {
                $sql_where2 = " WHERE ( ".$val." ) ";
            }
        } elseif((count($arr_sql_where) > 1)) {

            $sql_where2 = " WHERE ( ";

            $i = 0;

            foreach ($arr_sql_where as $key => $val) {

                if($i == 0) {
                    $sql_where2 .= $val;
                } else {
                    $sql_where2 .= " AND ".$val." ";
                }

                $i++;
            }

            $sql_where2 .= " ) ";

        }

        $output .= "<div style=\"padding-top: 15px; padding-bottom: 25px; \" >\n";

        $output .= "<span style=\" padding-left: 5px; font-size: 16px; font-weight: bold; \" >\n".
              ".:: Výpis routerů ::. </span>\n";
        $output .= "<span style=\"padding-left: 25px; \" >
            <a href=\"/topology/router/action\" >přidání nového routeru</a>
              </span>\n";

        $output .= "<span style=\"padding-left: 25px; \" >
                <a href=\"#\" onclick=\"visible_change(routers_filter)\" >filtr/hledání</a>
              </span>\n";

        $output .= "<span style=\"padding-left: 25px; \" >
                <a href=\"?typ=1\" >hierarchický výpis</a>
              </span>\n";

        $output .= "</div>\n";


        $output .= "<form method=\"GET\" action=\"\" >";

        //filtr - hlavni okno
        $output .= "<div id=\"routers_filter\" style=\"display: ".$display.";\" >";

        //Monitorováno
        $output .= "<div style=\"width: 150px; float: left;\">\n".
                "Monitorováno: </div>\n";

        $output .= "<div style=\"float: left; \">\n".
                "<select size=\"1\" name=\"f_monitoring\" >\n".
                    "<option value=\"99\" style=\"color: gray;\" >Nevybráno</option>\n".
                    "<option value=\"0\" ";
        if($f_monitoring == 0) {
            $output .= " selected ";
        } $output .= ">Ne</option>\n".
        "<option value=\"1\" ";
        if($f_monitoring == 1) {
            $output .= " selected ";
        } $output .= ">Ano</option>\n".
                "</select>\n".
            "</div>\n";

        //filtrace
        $output .= "<div style=\"width: 100px; float: left; padding-left: 10px; \" >\n".
                "Filtrace: </div>\n";

        $output .= "<div style=\"float: left; padding-left: 10px; \">\n".
                "<select size=\"1\" name=\"filtrace\" >\n".
                    "<option value=\"99\" style=\"color: gray;\" >nevybráno</option>\n".
                    "<option value=\"0\" ";
        if($filtrace == 0) {
            $output .= " selected ";
        } $output .= " >Ne</option>\n".
        "<option value=\"1\" ";
        if($filtrace == 1) {
            $output .= " selected ";
        } $output .= " >Ano</option>\n";
        $output .= "</select>\n".
        "</div>\n";

        //alarm
        $output .= "<div style=\"width: 100px; float: left; padding-left: 10px; \" >\n".
                "Alarm: </div>\n";

        $output .= "<div style=\"float: left; padding-left: 10px; \">\n".
                "<select size=\"1\" name=\"f_alarm\" >\n".
                    "<option value=\"99\" style=\"color: gray;\" >nevybráno</option>\n".
                    "<option value=\"0\" ";
        if($f_alarm == 0) {
            $output .= " selected ";
        } $output .= " >Ne</option>\n".
        "<option value=\"1\" ";
        if($f_alarm == 1) {
            $output .= " selected ";
        } $output .= " >Ano</option>\n";
        $output .= "</select>\n".
        "</div>\n";

        //alarm stav
        $output .= "<div style=\"width: 100px; float: left; padding-left: 10px; \" >\n".
                "Stav alarmu: </div>\n";

        $output .= "<div style=\"float: left; padding-left: 10px; \">\n".
                "<select size=\"1\" name=\"f_alarm_stav\" >\n".
                    "<option value=\"99\" style=\"color: gray;\" >nevybráno</option>\n".
                    "<option value=\"0\" ";
        if($f_alarm_stav == 0) {
            $output .= " selected ";
        } $output .= " >klid</option>\n".
        "<option value=\"1\" ";
        if($f_alarm_stav == 1) {
            $output .= " selected ";
        } $output .= " >warning</option>\n".
        "<option value=\"2\" ";
        if($f_alarm_stav == 2) {
            $output .= " selected ";
        } $output .= " >poplach</option>\n".
        "";
        $output .= "</select>\n".
        "</div>\n";

        //tlacitko
        $output .= "<div style=\"float: left; text-align: right; padding-left: 50px; \" >\n".
                "<input type=\"submit\" name=\"odeslano\" value=\"OK\" ></div>\n";

        //oddelovac
        $output .= "<div style=\"clear: both; height: 5px; \"></div>\n";

        //druha radka
        $output .= "<div style=\"float: left; \" >Hledání: </div>\n";

        $output .= "<div style=\"float: left; padding-left: 20px; \" >".
        "<input type=\"text\" name=\"f_search\" value=\"".htmlspecialchars($f_search)."\" ></div>\n";

        $output .= "<div style=\"float: left; padding-left: 20px; \" >ID routeru: </div>\n";

        $output .= "<div style=\"float: left; padding-left: 20px; \" >".
        "<input type=\"text\" name=\"f_id_routeru\" size=\"3\" value=\"".htmlspecialchars($f_id_routeru)."\" ></div>\n";

        //tlacitko
        $output .= "<div style=\"float: left; padding-left: 40px; \" >".
                "<input type=\"submit\" name=\"odeslano\" value=\"OK\" >".
                "<intpu type=\"hidden\" name=\"list\" value=\"".$list."\" ></div>\n";

        //oddelovac
        $output .= "<div style=\"clear: both; \"></div>\n";

        $output .= "</div>\n";

        $output .= "</form>\n";

        if($typ == 1) {

            $dotaz_router_main = $this->conn_mysql->query("SELECT * FROM router_list WHERE id = 1 order by id");
            $dotaz_router_main_radku = $dotaz_router_main->num_rows;

            if($dotaz_router_main_radku <> 1) {
                $output .= "<div style=\"font-size: 16px; font-weight: bold; color: red; \">Nelze vybrat hlavní router</div>\n";
                exit;
            }

            while($data_main = $dotaz_router_main->fetch_array()) {
                //pouze erik

                global $uroven_max;

                $uroven_max = 1;

                $output .= "<table border=\"1\" width=\"1000px\" >\n";
                $output .= "<tr>\n";

                $output .= "<td> [".$data_main["id"]."] ".$data_main["nazev"];

                $output .= " <span style=\"color:grey; \">( ".$data_main["ip_adresa"]." ) </span>";

                $output .= "</td>\n</tr>\n";

                $dotaz_router_1 = $this->conn_mysql->query("SELECT * FROM router_list WHERE parent_router = 1 order by id");
                $dotaz_router_radku_1 = $dotaz_router_1->num_rows;

                if($dotaz_router_radku_1 > 0) {
                    //prvni uroven
                    while($data_router_1 = $dotaz_router_1->fetch_array()) {
                        global $uroven;

                        $id = $data_router_1["id"];

                        $rs_hierarchy = hierarchy_vypis_router($id, "0");
                        if($rs_hierarchy === false) {
                            $output .= "<div class=\"alert alert-danger\" role=\"alert\">chyba hiearcheckeho vypisu routeru (no routers found in database)</div>";
                        } else {
                            $output .= $rs_hierarchy;
                        }
                    } // while dotaz_router
                } // konec if dotaz_router_radku > 0

                // $output .= "pokracujem ...";

            } // konec while

            //neprirazene rb
            $uroven_max = $uroven_max + 2;

            $output .= "<tr><td><br></td></tr>";

            $output .= "<tr><td colspan=\"".$uroven_max."\" ><hr></td></tr>";

            $output .= "<tr><td colspan=\"".$uroven_max."\" ><br></td></tr>";

            $output .= "<tr><td colspan=\"".$uroven_max."\" >Nepřiřazené routery:  </td></tr>";

            $dotaz_routery = $this->conn_mysql->query("SELECT * FROM router_list WHERE ( parent_router = 0 and id != 1) order by id");
            $dotaz_routery_radku = $dotaz_routery->num_rows;

            if ($dotaz_routery_radku < 1) {
                $output .= "<tr><td colspan=\"5\" > Žádné routery v databázi. </td></tr>";
            } else {
                while($data = $dotaz_routery->fetch_array()):

                    $output .= "<tr>";

                    $output .= "<td>".$data["id"]."</td>";
                    $output .= "<td>".$data["nazev"]."</td>";
                    $output .= "<td>".$data["ip_adresa"]."</td>";

                    // parent router
                    $output .= "<td>";

                    $output .=  $data["parent_router"];

                    $parent_router = $data["parent_router"];
                    $dotaz_sec = $this->conn_mysql->query("SELECT * FROM router_list WHERE id = '".intval($parent_router)."' ");

                    while($data_sec = $dotaz_sec->fetch_array()) {
                        $output .= "<span style=\"color: grey; font-weight: bold;\"> ( ".htmlspecialchars($data_sec["nazev"])." ) </span>";
                    }

                    $output .= "</td>";
                    //konec parent router

                    $output .= "<td>".$data["mac"]."</td>";

                endwhile;

            }

            $output .= "</table>";


        } // konec if typ == 1
        else {

            //vypis routeru normal

            $sql_base_old = "SELECT router_list.id, nazev, ip_adresa, parent_router, mac, monitoring, monitoring_cat, alarm, alarm_stav, filtrace, ".
                "kategorie.jmeno as kategorie_jmeno FROM `router_list` LEFT JOIN kategorie ON router_list.monitoring_cat = kategorie.id ";

            $sql_rows = "router_list.id, router_list.nazev, router_list.ip_adresa, router_list.parent_router, ".
                "router_list.mac, router_list.monitoring, router_list.monitoring_cat, router_list.alarm, ".
                " router_list.alarm_stav, router_list.filtrace, router_list.warn, router_list.mail, ".
                " kategorie.jmeno AS kategorie_jmeno, router_list2.nazev AS parent_router_nazev";

            $sql_base = "SELECT ".$sql_rows." FROM router_list ".
                " LEFT JOIN kategorie ON router_list.monitoring_cat = kategorie.id ".
                " LEFT JOIN router_list AS router_list2 ON router_list.parent_router = router_list2.id ";

            $sql_final = $sql_base." ".$sql_where2." ORDER BY id";

            $dotaz_routery = $this->conn_mysql->query($sql_final);
            $dotaz_routery_radku = $dotaz_routery->num_rows;

            if(!$dotaz_routery) {

                $output .= "<div style=\"font-weight: bold; color: red; \" >Chyba SQL příkazu.</div>";
                $output .= "<div style=\"padding: 5px; color: gray; \" >SQL DEBUG: ".$sql_final."</div>";
                // $output .= "<div style=\"\" >".mysql_error()."</div>";

            } elseif($dotaz_routery_radku < 1) {
                $output .= "<div style=\"margin-left: 10px; padding-left: 10px; padding-right: 10px; ".
                    "background-color: #ff8c00; height: 30px; width: 980px; \" >".
                    "<div style=\"padding-top: 5px;\" > Žádné záznamy dle hledaného kritéria. </div>".
                    "</div>";

                /*
                    //debug radka
                    $output .= "<div style=\"padding-top: 15px; padding-bottom: 25px; color: gray; \" >\n";
                    $output .=  $sql_final;
                    $output .= "</div>\n";
                    //konec debug
                */
            } else {
                /*
                    //debug radka
                    $output .= "<div style=\"padding-top: 15px; padding-bottom: 25px; color: gray; \" >\n";
                    $output .=  $sql_final;
                    $output .= "</div>\n";
                    //konec debug
                */

                //prvky pro listovaci odkazy
                $paging_url = "?".$get_odkazy;

                $paging = new c_listing_topology(
                    $this->conn_mysql,
                    $paging_url,
                    $this->settings['app']['core']['topology']['router']['listing_interval'],
                    $list,
                    "<div class=\"text-listing2\" style=\"width: 1000px; text-align: center; padding-top: 10px; padding-bottom: 10px;\">",
                    "</div>\n",
                    $sql_final
                );

                $bude_chybet = ((($list == "") || ($list == "1")) ? 0 : ((($list - 1) * $paging->interval)));

                $interval = $paging->interval;

                //uprava sql
                $sql_final = $sql_final . " LIMIT ".$interval." OFFSET ".$bude_chybet." ";

                // $output .= "<div>SQL DUMP: ".$sql_final . "</div>";
                $dotaz_routery = $this->conn_mysql->query($sql_final);

                $dotaz_routery_radku = $dotaz_routery->num_rows;

                //listovani
                $output .= $paging->listInterval();

                //hlavní tabulka
                $output .= "<table border=\"0\" style=\"width: 1000px; margin-left: 10px; \" >";

                $pocet_sloupcu = "8";

                $output .= "<tr>\n".
                        "<td style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"30px\" >id: </td>\n".
                        "<td style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"250px\" >název: </td>\n".
                        "<td style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"120px\" >IP adresa: </td>\n".
                        "<td style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"140px\">mac adresa: </td>\n".

                        "<td style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"60px\" >alarm: </td>\n".
                        "<td style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"40px\" >filtrace: </td>\n".

                        "<td colspan=\"2\" style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"40px\" >detailní výpis</td>\n".

                      "</tr>\n";

                //kategorie - druhy radek
                $output .= "<tr>\n".
                        "<td style=\"border-bottom: 1px solid black; font-weight: bold;\" width=\"30px\" >&nbsp;</td>\n".
                        "<td style=\"border-bottom: 1px solid black; font-weight: bold;\" width=\"250px\" >nadřazený router: </td>\n".
                        "<td colspan=\"2\" style=\"border-bottom: 1px solid black; font-weight: bold;\" >monitorování (kategorie): </td>\n".

                        "<td style=\"border-bottom: 1px solid black; font-weight: bold;\" width=\"40px\" >&nbsp;</td>\n".

                        "<td style=\"border-bottom: 1px solid black; font-weight: bold;\" width=\"40px\" >soubory: </td>\n".

                        "<td style=\"border-bottom: 1px solid black; font-weight: bold;\" width=\"40px\" >úprava: </td>\n".
                        "<td style=\"border-bottom: 1px solid black; font-weight: bold;\" width=\"40px\" >smazání: </td>\n".

                      "</tr>\n";

                $output .= "<tr>\n<td colspan=\"".$pocet_sloupcu."\" >&nbsp;\n</td>\n</tr>\n";


                while($data = $dotaz_routery->fetch_array()):

                    $alarm = $data["alarm"];

                    //1.radek
                    $output .= "<tr>";

                    $output .= "<td style=\"border-bottom: 1px dashed gray; font-size: 15px; \" >".htmlspecialchars($data["id"])."</td>\n";
                    $output .= "<td style=\"border-bottom: 1px dashed gray; font-size: 15px; \" >".htmlspecialchars($data["nazev"])."</td>\n";
                    $output .= "<td style=\"border-bottom: 1px dashed gray; font-size: 15px; \">".htmlspecialchars($data["ip_adresa"])."</td>\n";
                    $output .= "<td style=\"border-bottom: 1px dashed gray; font-size: 15px; \">".htmlspecialchars($data["mac"])."</td>";


                    //alarm
                    $output .= "<td style=\"border-bottom: 1px dashed gray; font-size: 15px; \">";

                    if ($alarm == 1) {
                        $output .= "<span style=\"font-weight: bold; \">Ano</span>";
                    } elseif ($alarm == 0) {
                        $output .= "Ne";
                    } else {
                        $output .= "N/A";
                    }

                    if ($alarm == 1) {
                        if ($data["alarm_stav"] == 2) {
                            $output .= "<span style=\"color: red; \"> (poplach) </span>";
                        } elseif ($data["alarm_stav"] == 1) {
                            $output .= "<span style=\"color: orange;\"> (warning) </span>";
                        } elseif ($data["alarm_stav"] == 0) {
                            $output .= "<span style=\"color: green; \"> (klid) </span>";
                        } else {
                            $output .= " (N/A) ";
                        }
                    }

                    $output .= "</td>\n";

                    //konec alarmu

                    //filtrace
                    $output .= "<td style=\"border-bottom: 1px dashed gray; font-size: 15px; \">\n";
                    if ($data["filtrace"] == 1) {
                        $output .= "<span style=\"color: green; font-weight: bold; \">Ano</span>";
                    } else {
                        $output .= "<span style=\"color: orange;\">Ne</span>";
                    }
                    $output .= "</td>\n";

                    //detail vypis
                    $output .= "<td colspan=\"2\" style=\"border-bottom: 1px dashed gray; font-size: 15px; \">\n".
                             "<a href=\"?f_id_routeru=".intval($data["id"])."&list_nodes=yes\">vypsat vysílače/nody</a></td>\n";

                    $output .= "</tr>";

                    //2.radek
                    $output .= "<tr>";

                    //2.1 - id
                    $output .= "<td style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\" >";
                    $output .= "<a href=\"/archiv-zmen?id_routeru=".intval($data["id"])."\" >H</a>";
                    $output .= "</td>";

                    //2.2 - parent router
                    $output .= "<td style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\" >";
                    $output .=  $data["parent_router_nazev"].
                    " <span style=\"color: grey; font-weight: bold;\">(".$data["parent_router"].")</span>\n".
                    "</td>\n";

                    //2.3-4 - monitoring
                    $output .= "<td colspan=\"2\" style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\" >";

                    if($data["monitoring"] == 1) {
                        $output .= "<span style=\"font-weight: bold; \">";
                        $output .= "<a href=\"https://monitoring.local.net/mon/www-generated/rb_all_".$data["ip_adresa"].".php\" target=\"_blank\" >Ano</a></span>";
                    } elseif ($data["monitoring"] == 0) {
                        $output .= "Ne";
                    } else {
                        $output .= "N/A";
                    }

                    $output .= "<span style=\"color: grey; \"> ( ";
                    if ($data["monitoring_cat"] > 0) {
                        $output .= "<a href=\"https://monitoring.local.net/mon/www/rb_all.php\" target=\"_blank\" >";
                    }

                    $output .=  htmlspecialchars($data["kategorie_jmeno"]." / ".$data["monitoring_cat"]);

                    if ($data["monitoring_cat"] > 0) {
                        $output .= "</a>";
                    }
                    $output .= " ) </span></td>";

                    //2.5 - alarm, 2cast
                    $output .= "<td style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\">\n";

                    if ($alarm == 1) {
                        $output .= "( CW: ".$data["warn"]." CM: ".$data["mail"]." )";
                    } else {
                        $output .= "&nbsp;";
                    }

                    $output .= "</td>\n";

                    //2.6. - soubory
                    $output .= "<td style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\" >\n".
                            "<a href=\"topology-router-mail.php?id=".$data["id"]."\">";
                    $output .= "<img src=\"/img2/icon_files.jpg\" border=\"0\" height=\"20px\" ></a>\n</td>\n";

                    //uprava
                    $output .= "<td style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\" >";
                    $output .= '<form method="POST" action="/topology/router/action">
                                <input type="hidden" name="update_id" value="'.intval($data["id"]).'">';
                    $output .= $this->csrf_html;
                    $output .= '<input type="submit" value="update">
                                </form></span>';
                    $output .= "</td>\n";

                    //smazat
                    $output .= "<td style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\" >\n";
                    $output .=  '<form method="POST" action="topology-router-erase.php">
                          <input type="hidden" name="erase_id" value="'.intval($data["id"]).'">
                          <input type="submit" name="smazat" value="smazat" >
                          </form></span>';
                    $output .= "</td>\n";

                    $output .= "</tr>\n";

                    //pokud s kliklo na vypis subnetu
                    if(($_GET["list_nodes"] == "yes" and $f_id_routeru == $data["id"])) {

                        $output .= "<tr><td colspan=\"11\" >";

                        $id_routeru = $data["id"];
                        $colspan_stav = "1";

                        $dotaz_top = $this->conn_mysql->query("SELECT * FROM nod_list WHERE router_id = '".intval($f_id_routeru)."' ");
                        $dotaz_top_radku = $dotaz_top->num_rows;

                        if ($dotaz_top_radku < 1) {
                            $output .= "<span style=\"color: teal; font-size: 16px; font-weight: bold;\">
                                <p> Žádné aliasy/nody v databázi. </p></span>";
                        } else {

                            $output .= "<table border=\"0\" width=\"100%\" >";

                            while($data_top = $dotaz_top->fetch_array()):

                                $output .= "<tr>";

                                $output .= "<td class=\"top-router-dolni1\"><span style=\"color: #777777; \">";
                                $output .=  $data_top["jmeno"]."</span></td>";

                                $output .= "<td class=\"top-router-dolni1\"><span style=\"color: #777777; \">".$data_top["adresa"]."</span></td>";

                                $output .= "<td class=\"top-router-dolni1\"><span style=\"color: #777777; \">".$data_top["ip_rozsah"]."</span></td>";

                                $output .= "<td class=\"top-router-dolni1\"><span style=\"color: #777777; \">".$data_top["mac"]."</span></td>";

                                if ($data_top["stav"] == 1) {
                                    $output .= "<td class=\"top-router-dolni1\" colspan=\"".$colspan_stav."\" bgcolor=\"green\" align=\"center\" >
                                    <span style=\"color: white; font-size: 13px; \"> v pořádku </span></td>";
                                } elseif ($data_top["stav"] == 2) {
                                    $output .= "<td class=\"top-router-dolni1\" colspan=\"".$colspan_stav."\" bgcolor=\"orange\" align=\"center\" >
                                    <span style=\"color: white; font-size: 13px; \"> vytížen </span></td>";
                                } elseif($data_top["stav"] == 3) {
                                    $output .= "<td class=\"top-router-dolni1\" colspan=\"".$colspan_stav."\" bgcolor=\"red\" align=\"center\" >
                                <span style=\"color: white; font-size: 13px; \"> přetížen </span></td>";
                                } else {
                                    $output .= "<td class=\"top-router-dolni1\" colspan=\"".$colspan_stav."\" >
                                <span style=\"color: #666666; font-size: 13px; \">".$data_top["stav"]."</span></td>";
                                }

                                $typ_vysilace = $data_top["typ_vysilace"];

                                if ($typ_vysilace == 1) {
                                    $typ_vysilace2 = "Metallic";
                                } elseif ($typ_vysilace == 2) {
                                    $typ_vysilace2 = "ap-2,4GHz-OMNI";
                                } elseif ($typ_vysilace == 3) {
                                    $typ_vysilace2 = "ap-2,4Ghz-sektor";
                                } elseif ($typ_vysilace == 4) {
                                    $typ_vysilace2 = "ap-2.4Ghz-smerovka";
                                } elseif ($typ_vysilace == 5) {
                                    $typ_vysilace2 = "ap-5.8Ghz-OMNI";
                                } elseif ($typ_vysilace == 6) {
                                    $typ_vysilace2 = "ap-5.8Ghz-sektor";
                                } elseif ($typ_vysilace == 7) {
                                    $typ_vysilace2 = "ap-5.8Ghz-smerovka";
                                } elseif ($typ_vysilace == 8) {
                                    $typ_vysilace2 = "jiné";
                                } else {
                                    $typ_vysilace2 = $typ_vysilace;
                                }

                                $output .= "<td class=\"top-router-dolni1\"><span style=\"color: grey; font-size: 12px; \">".$typ_vysilace2."</span></td>";
                                $output .= "<td class=\"top-router-dolni1\">";
                                $output .= "<a href=\"/topology/node-list?find=".$data_top["jmeno"]."\">detail nodu </a>";
                                $output .= "</td>";

                                $output .= "</tr>";

                            endwhile;

                            $output .= "</table>";

                        } // konec else dotaz_top_radku < 1

                        $output .= "</td></tr>";

                    } // konec if get id == data id

                endwhile;

                $output .= "</table>";

                //listovani
                $output .=  $paging->listInterval();

            }

        } // konec else typ == 1

        return $output;
    }

    public function filter_select_nods($typ_nodu = '')
    {

        $ret = array();

        if(empty($typ_nodu)) {
            $sql_filter_nod = " typ_nodu = 2 ";
        } else {
            $sql_filter_nod = " typ_nodu = ".intval($typ_nodu)." ";
        }

        $sql = "SELECT id, jmeno FROM nod_list " .
                " WHERE " . $sql_filter_nod .
             " ORDER BY id";

        try {
            $rs = $this->conn_mysql->query($sql);
        } catch (Exception $e) {
            $text = htmlspecialchars("Error message: ". $e->getMessage());
        }

        if(!$rs) {
            $ret["error"] = array("2" => $text);
            return $ret;
        }

        $rs_num = $rs->num_rows;

        if($rs_num == 0) {

            $text = htmlspecialchars("Žádné nody nenalezeny");
            $ret["error"] = array("1" => $text);

            return $ret;
        }

        while($data = $rs->fetch_array()) {

            $id = intval($data["id"]);
            $val = htmlspecialchars($data["jmeno"]);

            $ret["data"][$id] = $val;
        }

        return $ret;

    } //end of function filter_select_nods
}

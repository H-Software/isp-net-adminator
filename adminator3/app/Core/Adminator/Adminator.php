<?php

namespace App\Core;

use App\Models\User;
use App\Models\PageLevel;

use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Exception;

class adminator
{
    public $conn_mysql;

    public $conn_pgsql;

    public $smarty;
    public $logger;

    public $pdoMysql;

    protected $cache;

    public $settings;

    public $userIdentityUsername;

    public $userIPAddress;

    public $page_level_id;

    public $userIdentityLevel;

    public $loggedUserEmail;

    public function __construct($conn_mysql, $smarty, $logger, $userIPAddress = null, $pdoMysql = null, $settings = null, $conn_pgsql = null)
    {
        $this->logger = $logger;
        $this->logger->info("adminator\__construct called");

        $this->conn_mysql = $conn_mysql;
        $this->conn_pgsql = $conn_pgsql;
        $this->pdoMysql = $pdoMysql;
        $this->smarty = $smarty;
        $this->settings = $settings;

        if($userIPAddress == null) {
            $this->userIPAddress = $_SERVER['REMOTE_ADDR'];
        }
    }

    public function formInit()
    {
        // bootstrap5 -> framework for CSS/JS formatting
        // hush -> no echoing stuff -> https://github.com/formr/formr/issues/87#issuecomment-769374921
        return new \Formr\Formr('bootstrap5', 'hush');
    }

    public function objectToArray($data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[$key] = (is_array($value) || is_object($value)) ? $this->objectToArray($value) : $value;
        }
        return $result;
    }

    /**
     * Get bytes of using random_bytes or openssl_random_pseudo_bytes
     * then using bin2hex to get a random string.
     *
     * @param int $length
     * @return string
     */
    public function getRandomStringBin2hex($length = 32)
    {
        if (function_exists('random_bytes')) {
            $bytes = random_bytes($length / 2);
        } else {
            $bytes = openssl_random_pseudo_bytes($length / 2);
        }
        $randomString = bin2hex($bytes);
        return $randomString;
    }

    public function fillEmptyVarsInArray(array $a, array $exclude = [])
    {
        foreach($a as $key => $val) {
            if(empty($val) and !in_array($key, $exclude)) {
                $a[$key] = 0;
            }
        }
        return $a;
    }

    public function getUserLevel()
    {
        $level = 0;

        $rs = User::where(
            "email",
            isset($this->userIdentityUsername) ? $this->userIdentityUsername : 0
        )->first(['level']);

        if(is_object($rs)) {
            // $this->logger->info("adminator\getUserLevel dump db: " . var_export($rs, true));
            $a = $rs->toArray();
            $level = $a['level'];
        }

        if($level > 0) {
            return $level;
        } else {
            return false;
        }
    }

    public function getUserToken(): false|string
    {
        $rs = User::where(
            "email",
            isset($this->userIdentityUsername) ? $this->userIdentityUsername : 0
        )->first(['token']);

        if(is_object($rs)) {
            $a = $rs->toArray();
            $token = $a['token'];
        } else {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": failed to load data from database (result is not object)");
            return false;
        }

        if($token == null or $token == 0 or strlen($token) < 2) {
            $rs = $this->setuserToken();
            if($rs === false) {
                $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": setuserToken failed.");
                return false;
            } else {
                $token = $rs;
            }
        }

        return $token;
    }

    public function setUserToken(): false|string
    {
        $token = $this->getRandomStringBin2hex();

        $affRows = User::where(
            "email",
            isset($this->userIdentityUsername) ? $this->userIdentityUsername : 0
        )
        ->update(['token' => $token]);

        if($affRows <> 1) {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": update data in database failed (affRows ". var_export($affRows, true) .")");
            return false;
        } else {
            $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": UserToken updated.");
        }

        return $token;
    }

    public function verifyUserToken(ServerRequestInterface $request): bool
    {
        $token = $request->getQueryParams()['token'] ?? '';

        if (empty($token)) {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": empty request param");
            return false;
        }

        $rs = User::where(
            "email",
            isset($this->userIdentityUsername) ? $this->userIdentityUsername : 0
        )->where('token', $token)
        ->first(['id']);

        if(is_object($rs)) {
            $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": verifyUserToken: \"OK\" for " . var_export($this->userIdentityUsername, true));
            return true;
        } else {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": verifyUserToken: \"FAIL\" for " . var_export($this->userIdentityUsername, true));
            return false;
        }
    }

    public function checkLevel($page_level_id_custom = 0, $display_no_level_page = true)
    {

        // co mame
        // v promeny level mame level prihlaseneho uzivatele
        // databazi levelu pro jednotlivy stranky

        // co chceme
        // porovnat level uzivatele s prislusnym levelem
        // stranky podle jejiho id

        $this->userIdentityLevel = $this->getUserLevel();

        $this->logger->info(
            "adminator\check_level: called with
                                    [page_level_id_custom => " . $page_level_id_custom
                                    . ", page_level_id => " . $this->page_level_id
                                    . ", user_name => " . $this->userIdentityUsername
                                    . ", user_level => " . $this->userIdentityLevel
            . "]"
        );

        if(intval($page_level_id_custom) > 0) {
            $pl = $page_level_id_custom;
        } else {
            $pl = $this->page_level_id;
        }

        $page_level_rs = $this->find_page_level($this->logger, $pl);
        if($page_level_rs === false or !is_int($page_level_rs)) {
            $rs = false;
        } elseif($this->userIdentityLevel >= $page_level_rs) {
            $rs = true;
        } else {
            $rs = false;
        }

        $this->logger->info("adminator\check_level: find_page_level: pl_id: " . $pl . ", level: " . var_export($page_level_rs, true));
        $this->logger->info("adminator\check_level: result: " . var_export($rs, true));

        if($rs === false) {
            // user nema potrebny level
            return false;
        } else {
            return true;
        }
    }

    public function find_page_level($logger, $page_id)
    {

        $page_level = 0;

        $rs = PageLevel::find(isset($page_id) ? $page_id : 0, ['level']);
        if(is_object($rs)) {
            $a = $rs->toArray();
            $page_level = $a['level'];
        }

        $this->logger->info("adminator\\find_page_level: find result: " . var_export($page_level, true));

        if($page_level > 0) {
            return $page_level;
        } else {
            return false;
        }
    }

    public function getServerUri()
    {
        return $_SERVER["REQUEST_URI"];
    }

    public function getSqlDateFormat($column, $format = "%d.%m.%Y")
    {
        $formatedDate = $this->settings['db']['driver'] === 'sqlite' ?
            'strftime("' . $format .'", '. $column .')' :
            'date_format(' . $column . ', "'.$format.'")';

        return $formatedDate;
    }

    public function getSqlTimestampFormat($column, $format = "%d.%m.%Y")
    {
        $formatedDate = $this->settings['db']['driver'] === 'sqlite' ?
            'strftime("' . $format .'", datetime('. $column .', \'unixepoch\'))' :
            'date_format(' . $column . ', "'.$format.'")';

        return $formatedDate;
    }

    public function getTarifIptvListForForm($show_zero_value = true)
    {

        $this->logger->info("adminator\getTarifIptvListForForm called");

        if($show_zero_value === true) {
            $tarifs[0] = "Není vybráno";
        }

        $q = $this->conn_mysql->query("SELECT id_tarifu, jmeno_tarifu FROM tarify_iptv ORDER by jmeno_tarifu ASC");

        $num_rows = $q->num_rows;

        if($num_rows < 1) {
            $tarifs[0] =  "nelze zjistit / žádný tarif nenalezen";
            return $tarifs;
        }

        while($data = $q->fetch_array()) {
            $tarifs[$data['id_tarifu']] = $data["jmeno_tarifu"];
        }

        return $tarifs;
    }

    public function zobraz_kategorie($uri, $uri_replace = null)
    {

        $kategorie = array();

        $kategorie[0] = array( "nazev" => "Zákazníci", "url" => "/vlastnici/cat", "align" => "center", "width" => "18%" );

        if(preg_match("/^\/vlastnici.*/", $uri) or preg_match("/^\/vypovedi.*/", $uri)) {
            $kategorie[0]["barva"] = "silver";
        }

        $kategorie[1] = array( "nazev" => "Služby", "url" => "/objekty/cat", "align" => "center", "width" => "18%" );

        if(preg_match("/^\/objekty.*/", $uri)) {
            $kategorie[1]["barva"] = "silver";
        }

        $kategorie[2] = array( "nazev" => "Platby", "url" => "/platby/cat", "align" => "center", "width" => "18%" );

        // if( ereg("^.+platby.+$",$uri) )
        // { $kategorie[2]["barva"] = "silver"; }

        $kategorie[3] = array( "nazev" => "Topologie", "url" => "/topology", "align" => "center", "width" => "" );

        // if( ereg("^.+topology",$uri) )
        // { $kategorie[3]["barva"] = "silver"; }

        $kategorie[4] = array( "nazev" => "Nastavení", "url" => "/admin", "align" => "center", "width" => "" );

        // if( ereg("^.+admin.+$",$uri_replace ) )
        // {  $kategorie[4]["barva"] = "silver"; }

        $kategorie[5] = array( "nazev" => "Úvodní strana", "url" => "/home", "align" => "center", "width" => "" );

        // if( ereg("^.+home.php$",$uri) )
        // { $kategorie[5]["barva"] = "silver"; }

        $kat_2radka = array();

        $kat_2radka[0] = array( "nazev" => "Partner program", "url" => "/partner/cat", "width" => "", "align" => "center" );

        // if( (ereg("partner",$uri_replace) and !ereg("admin",$uri_replace)) )
        // { $kat_2radka[0]["barva"] = "silver"; }

        $kat_2radka[1] = array( "nazev" => "Změny", "url" => "/archiv-zmen/cat", "width" => "", "align" => "center" );

        // if( ereg("^.+archiv-zmen.+$",$uri) )
        // { $kat_2radka[1]["barva"] = "silver"; }

        $kat_2radka[2] = array( "nazev" => "Work", "url" => "/work", "width" => "", "align" => "center" );

        // if( ereg("^.+work.+$",$uri) )
        // { $kat_2radka[2]["barva"] = "silver"; }

        $kat_2radka[3] = array( "nazev" => "Ostatní", "url" => "/others", "width" => "", "align" => "center" );

        // if( ereg("^.+others.+$",$uri) or ereg("^.+syslog.+$",$uri) or ereg("^.+/mail.php$",$uri) or ereg("^.+opravy.+$",$uri) )
        // { $kat_2radka[3]["barva"] = "silver"; }

        $kat_2radka[4] = array( "nazev" => "O programu", "url" => "/about", "width" => "", "align" => "center" );

        // if( ereg("^.+about.+$",$uri) )
        // { $kat_2radka[4]["barva"] = "silver"; }

        $ret = array( $kategorie, $kat_2radka);

        return $ret;
    }

    //
    // vypis neuhrazenych faktur
    //
    // return hodnoty
    //
    // 0. neuhr. faktur celkem
    // 1. nf ignorovane
    // 2. nf nesparovane
    // 3. datum posl. importu
    // 4. chybove hlasky
    public function show_stats_faktury_neuhr()
    {
        $error_messages = "";
        $ret = array();
        $dotaz_fn = "";

        for ($i = 0; $i < 4; $i++) {
            if($i == 0) {
                $sql = "SELECT * FROM faktury_neuhrazene";
            } elseif($i == 1) {
                $sql = "SELECT * FROM faktury_neuhrazene WHERE ( ignorovat = '1' ) order by id";
            } elseif($i == 2) {
                $sql = "SELECT * FROM faktury_neuhrazene WHERE par_id_vlastnika = '0' ";
            } elseif($i == 3) {
                // $sql = "SELECT datum,DATE_FORMAT(datum, '%d.%m.%Y %H:%i:%s') as datum FROM fn_import_log order by id";
                $sql = "SELECT datum, " . $this->getSqlDateFormat('datum'). " as datum FROM fn_import_log order by id";
            }

            try {
                $dotaz_fn = $this->pdoMysql->query($sql);
                $dotaz_fn_radku = count($dotaz_fn->fetchAll());
                $ret[$i] = $dotaz_fn_radku;
            } catch (Exception $e) {
                $error_message = "PDO query failed! Catched Error: " . var_export($e->getMessage(), true);
                $error_messages .= "<div>" . $error_message . "</div>\n";
                $this->logger->error(__CLASS__ . '\\' .__FUNCTION__ . ": " . $error_message);

                $ret[$i] = 0;
            }

            if($i == 3 and is_object($dotaz_fn)) {
                $data3 = $dotaz_fn->fetchAll();

                $datum_fn3 = (isset($data3[0])) ? $data3[0]["datum"] : "";

                if(strlen($datum_fn3) > 0) {
                    $ret[3] = $datum_fn3;
                } else {
                    $ret[3] = "Unknown";
                }
            }
        }

        $ret[4] = $error_messages;

        return $ret;
    }

    public function list_logged_users(): void
    {
        $data = array();
        $rs = "";

        $sql = "SELECT email, ". $this->getSqlTimestampFormat("last_login") . " as date
                    FROM users
                    ORDER BY last_login DESC 
                    LIMIT 5
            ";

        try {
            $this->logger->debug(__CLASS__ . '\\' .__FUNCTION__
                                    . ": SQL dump: "
                                    . var_export($sql, true));

            $rs = $this->pdoMysql->query($sql);
        } catch (Exception $e) {
            $error_message = "PDO query failed! Catched Error: " . var_export($e->getMessage(), true);

            $this->logger->error(__CLASS__ . '\\' .__FUNCTION__ . ": " . $error_message);

            $this->smarty->assign("logged_users_error_message", $error_message);
        }

        if(is_object($rs)) {
            $data = $rs->fetchAll();
        }

        $this->smarty->assign("logged_users", $data);
    }

    public function get_opravy_a_zavady($opravy): void
    {
        //opravy a zavady vypis
        $pocet_bunek = 11;

        $this->logger->info("adminator\get_opravy_a_zavady called");

        $v_reseni_filtr = $_GET["v_reseni_filtr"];
        $vyreseno_filtr = $_GET["vyreseno_filtr"];
        $limit = $_GET["limit"];

        if(!isset($v_reseni_filtr)) {
            $v_reseni_filtr = "99";
        }
        if(!isset($vyreseno_filtr)) {
            $vyreseno_filtr = "0";
        }

        if(!isset($limit)) {
            $limit = "10";
        }

        // vypis
        $this->smarty->assign("opravy_povoleno", 1);

        $this->smarty->assign("pocet_bunek", $pocet_bunek);

        $this->smarty->assign("vyreseno_filtr", $vyreseno_filtr);
        $this->smarty->assign("v_reseni_filtr", $v_reseni_filtr);
        $this->smarty->assign("limit", $limit);

        $this->smarty->assign("action", $_SERVER['SCRIPT_URL']);

        $rs_vypis = $opravy->vypis_opravy($pocet_bunek);
        // $this->logger->debug("homeController\opravy_a_zavady list: result: " . var_export($rs_vypis, true));

        if($rs_vypis) {
            if (strlen($rs_vypis[0]) > 0) {
                // no records in DB
                $this->logger->info("homeController\opravy_a_zavady list: no records found in database.");
                $content_opravy_a_zavady = $rs_vypis[0];
            } elseif(strlen($rs_vypis[1]) > 0) {
                // raw html
                $content_opravy_a_zavady = $rs_vypis[1];
            } else {
                // ??
                $this->logger->error("homeController\opravy_a_zavady unexpected return value");
            }
        } else {
            $this->logger->error("homeController\opravy_a_zavady no return value from vypis_opravy call");
        }

        $this->smarty->assign("content_opravy_a_zavady", $content_opravy_a_zavady);
    }

    public static function convertIntToBoolTextCs($v)
    {
        if ($v == 1) {
            return "Ano";
        } elseif ($v == 0) {
            return "Ne";
        } else {
            return $v;
        }
    }

    public static function convertIntToTextPrioCs($v)
    {
        if ($v == 0) {
            return "Nízká";
        } elseif ($v == 1) {
            return "Normální";
        } elseif ($v == 2) {
            return "Vysoká";
        } else {
            return $v;
        }
    }

    /**
     * paginate collection
     *
     * base is stolen from: https://stackoverflow.com/a/75755710/19497107
     * appends for queryString is here: https://stackoverflow.com/questions/24891276/how-to-automatically-append-query-string-to-laravel-pagination-links
     */
    public function collectionPaginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $paginator = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        $paginator->appends($_GET);
        return $paginator;
    }

    public function paginateGetLinks($data)
    {
        $linkCurrentPage = $data['current_page'];

        foreach ($data['links'] as $key => $value) {
            if ($value['label'] == "Previous") {
                $linkPreviousPage = $value['url'];
            }
            if ($value['label'] == "Next") {
                $linkNextPage = $value['url'];
            }
        }

        return array($linkPreviousPage, $linkCurrentPage, $linkNextPage);
    }

    public function paginateRenderLinks($linkPreviousPage, $linkCurrentPage, $linkNextPage)
    {
        $output = "<div align=\"center\" style=\"font-size: 0.8rem; padding-top: 5px; padding-bottom: 5px;\">";

        if($linkPreviousPage != null) {
            $output .= "<span><a href=\"".$linkPreviousPage."\" >previous</a></span> | ";
        }
        if($linkCurrentPage != null) {
            $output .= "<span style=\"margin-left: 10px: margin-right: 10px;\">" . $linkCurrentPage . "</span>";
        }
        if($linkNextPage != null) {
            $output .= " | <span><a href=\"".$linkNextPage."\" >next</a></span>";
        }

        $output .= "</div>";

        return $output;
    }

    public function create_link_to_owner($owner_id)
    {
        $owner_id = intval($owner_id);

        $sql = "SELECT firma, archiv FROM vlastnici WHERE id_cloveka = '".$owner_id."' ";

        if($this->conn_pgsql != null) {
            $vlastnik_dotaz = pg_query($this->conn_pgsql, $sql);
        } else {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": missing pgsql handler");
            return false;
        }

        $vlastnik_radku = pg_num_rows($vlastnik_dotaz);

        while($data_vlastnik = pg_fetch_array($vlastnik_dotaz)) {
            $firma_vlastnik = $data_vlastnik["firma"];
            $archiv_vlastnik = $data_vlastnik["archiv"];
        }

        if($vlastnik_radku <= 0) {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": missing database data");
            return false;
        }
        
        if ($archiv_vlastnik == 1) {
            $odkaz = "<a href=\"/vlastnici/archiv?".urlencode("find_id")."=".urlencode(strval($owner_id))."\" >".$owner_id."</a>\n";
        } elseif ($firma_vlastnik == 1) {
            $odkaz = "<a href=\"/vlastnici2?".urlencode("find_id")."=".urlencode(strval($owner_id))."\" >".$owner_id."</a>\n";
        } else {
            $odkaz = "<a href=\"/vlastnici?".urlencode("find_id")."=".urlencode(strval($owner_id))."\" >".$owner_id."</a>\n";
        }

        return $odkaz;
    }
}

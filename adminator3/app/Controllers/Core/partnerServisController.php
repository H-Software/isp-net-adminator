<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// use App\Partner\partner;

class partnerServisController extends adminatorController
{
    public \Smarty $smarty;

    public \Monolog\Logger $logger;

    protected $adminator;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    private $psi;

    public function __construct(ContainerInterface $container, $adminatorInstance = null)
    {
        $this->smarty = $container->get('smarty');
        $this->logger = $container->get('logger');
        $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        parent::__construct($container, $adminatorInstance);

        $this->psi = new \partner_servis($this->conn_mysql, $this->conn_pgsql, $this->sentinel);
    }

    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(305)) {
            return $this->response;
        };

        $bodyContent = "";

        //priprava form. promennych
        $filtr = "";
        // $list = intval($_GET["list"]);
        $filtr_akceptovano = intval($_GET["filtr_akceptovano"]);
        $filtr_prio = intval($_GET["filtr_prio"]);

        //priprava dotazu

        if ($filtr_akceptovano > 0) {
            $filtr .= " AND akceptovano = ".$filtr_akceptovano." ";
        }
        if ($filtr_prio > 0) {
            $filtr .= " AND prio = ".$filtr_prio." ";
        }

        $dotaz_sql = "SELECT tel, jmeno, adresa, email, poznamky, prio, vlozil, akceptovano, ".
            "akceptovano_kym, akceptovano_pozn, DATE_FORMAT(datum_vlozeni, '%d.%m.%Y %H:%i:%s') ".
            "as datum_vlozeni2 FROM partner_klienti_servis ";

        // if( isset($user) )
        // { $dotaz_sql .= " WHERE ( vlozil = '".$this->conn_mysql->real_escape_string($user_plaint)."' ".$filtr." ) "; }
        // else
        { $dotaz_sql .= " WHERE ( id > 0 ".$filtr." ) "; }

        $dotaz_sql .= " ORDER BY id DESC ";

        // $poradek = "filtr_akceptovano=".$filtr_akceptovano."&filtr_prio=".$filtr_prio;

        // $format_css = "font-size: 13px; padding-top: 5px; padding-bottom: 15px; ";

        //vytvoreni objektu
        // $listovani = new c_listing_partner_servis($conn_mysql, "./partner-servis-list.php?".$poradek, 30, $list, "<center><div style=\"".$format_css."\">\n", "</div></center>\n", $dotaz_sql);

        // if(($list == "")||($list == "1")){ $bude_chybet = 0; }
        // else{ $bude_chybet = (($list-1) * $listovani->interval); }

        // $interval = $listovani->interval;

        // $dotaz_limit = " LIMIT ".$interval." OFFSET ".$bude_chybet." ";

        // $dotaz_sql .= $dotaz_limit;

        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": final SQL: " . var_export($dotaz_sql, true));

        // $listovani->listInterval();

        $bodyContent .= $this->psi->list_show_legend(); // promena vyrizeni a update asi zde prazdne

        $bodyContent .= $this->psi->list_show_items($filtr_akceptovano, $filtr_prio, $dotaz_sql);

        // $listovani->listInterval();

        $assignData = [
            "page_title" => "Adminator3 :: Partner program :: Servis List",
            "body" => $bodyContent
        ];

        return $this->renderer->template($request, $response, 'partner/servis-list.tpl', $assignData);
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(304)) {
            return $this->response;
        };

        $bodyContent = "";

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);

        $this->psi->klient_hledat = $this->conn_mysql->real_escape_string($_POST["klient_hledat"]);
        $this->psi->klient_id = intval($_POST["klient_id"]);

        $this->psi->fill_form = $this->conn_mysql->real_escape_string($_POST["fill_form"]);

        if ((strlen($this->psi->fill_form) > 4)) {
            $this->psi->form_copy_values();
        } else {
            $this->psi->jmeno_klienta = $this->conn_mysql->real_escape_string($_POST["jmeno_klienta"]);
            $this->psi->bydliste      = $this->conn_mysql->real_escape_string($_POST["bydliste"]);
            $this->psi->email 	       = $this->conn_mysql->real_escape_string($_POST["email"]);
            $this->psi->tel 	       = $this->conn_mysql->real_escape_string($_POST["tel"]);
        }

        $this->psi->pozn = $this->conn_mysql->real_escape_string($_POST["pozn"]);
        $this->psi->prio = intval($_POST["prio"]);

        $this->psi->odeslat = $this->conn_mysql->real_escape_string($_POST["odeslat"]);

        //kontrola promennych
        $this->psi->check_insert_value();

        if ((($this->psi->odeslat == "ULOŽIT") and ($this->psi->fail == false))) { // mod ukladani
            $bodyContent .= $this->psi->save_form();
        } else { // zobrazime formular
            $bodyContent .= "<form action=\"\" method=\"post\" class=\"form-partner-servis-insert\" >";
            $bodyContent .= $csrf_html;

            if (isset($this->psi->odeslat) and isset($this->psi->error)) {
                $bodyContent .= $this->psi->error;
            }

            $bodyContent .= $this->psi->show_insert_form();

            $bodyContent .= "</form>";
        }

        $assignData = [
            "page_title" => "Adminator3 :: Partner program :: Servis Add",
            "body" => $bodyContent
        ];

        return $this->renderer->template($request, $response, 'partner/servis-list.tpl', $assignData);
    }

    public function servisAccept(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(306)) {
            return $this->response;
        };

        $bodyContent = "";

        $assignData = [
            "page_title" => "Adminator3 :: Partner program :: Servis Accept",
        ];

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);

        if ($_GET["accept"] <> 1) {
            $filtr = "";

            // $list = intval($_GET["list"]);
            $filtr_akceptovano = 2;
            $filtr_prio = intval($_GET["filtr_prio"]);

            //priprava dotazu

            // if($filtr_akceptovano > 0)
            {
                $filtr .= " AND akceptovano = ".$filtr_akceptovano." ";
            }

            if ($filtr_prio > 0) {
                $filtr .= " AND prio = ".$filtr_prio." ";
            }

            $dotaz_sql = "SELECT id, tel, jmeno, adresa, email, poznamky, prio, vlozil, akceptovano, ".
              "akceptovano_kym, akceptovano_pozn, DATE_FORMAT(datum_vlozeni, '%d.%m.%Y %H:%i:%s') ".
              "as datum_vlozeni2 FROM partner_klienti_servis ";

            // if(isset($user)) {
            //     $dotaz_sql .= " WHERE ( vlozil = '".$this->conn_mysql->real_escape_string($user_plaint)."' ".$filtr." ) ";
            // } else
            {
                $dotaz_sql .= " WHERE ( id > 0 ".$filtr." ) ";
            }

            $dotaz_sql .= " ORDER BY id DESC ";

            // $poradek = "filtr_akceptovano=".$filtr_akceptovano."&filtr_prio=".$filtr_prio;

            // $format_css = "font-size: 13px; padding-top: 5px; padding-bottom: 15px; ";

            // //vytvoreni objektu
            // $listovani = new c_listing_partner_servis($conn_mysql, "./partner-servis-list.php?".$poradek, 30, $list, "<center><div style=\"".$format_css."\">\n", "</div></center>\n", $dotaz_sql);

            // if(($list == "")||($list == "1")){ $bude_chybet = 0; }
            // else{ $bude_chybet = (($list-1) * $listovani->interval); }

            // $interval = $listovani->interval;

            // $dotaz_limit = " LIMIT ".$interval." OFFSET ".$bude_chybet." ";

            // $dotaz_sql .= $dotaz_limit;

            // $listovani->listInterval();

            $this->psi->vyrizeni = true;

            $bodyContent .= $this->psi->list_show_legend();

            $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": List SQL: " . var_export($dotaz_sql, true));

            $bodyContent .= $this->psi->list_show_items("2", $filtr_prio, $dotaz_sql);

            // $listovani->listInterval();

        } //konec if update_id > 0
        else {

            $bodyContent .= "<div style=\"padding-left: 40px; padding-bottom: 20px; padding-top: 20px; font-weight: bold; font-size: 18px; \">
                    <span style=\"border-bottom: 1px solid grey; \" >Akceptování žádosti o servis</span>
             </div>";

            if (preg_match('/^([[:digit:]])+$/', $_GET["id"]) == false) {
                $bodyContent .= "<div style=\"color: red; font-weight: bold; padding-left: 20px; padding-bottom: 20px; \">
                    Chyba! Zákazníka nelze akceptovat! Vstupní data jsou ve špatném formátu! </div> ";
                $assignData["body"] = $bodyContent;
                return $this->renderer->template($request, $response, 'partner/servis-accept.tpl', $assignData, 500);
            }

            if ($_GET["odeslat"] == "OK") {
                //budem ukladat

                $pozn = $this->conn_mysql->real_escape_string($_GET["pozn"]);
                $id = intval($_GET["id"]);

                $sql = "UPDATE partner_klienti_servis "
                        . " SET akceptovano='1', "
                        . " akceptovano_kym='" . $this->adminator->userIdentityUsername . "', "
                        . " akceptovano_pozn = '$pozn' "
                        . " WHERE id=".$id." Limit 1 ";

                $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": Update SQL: " . var_export($sql, true));

                $uprava = $this->conn_mysql->query($sql);

                if ($uprava) {
                    $bodyContent .= "<br><H3><div style=\"color: green; padding-left: 20px;\" >Zákazník úspěšně akceptován.</div></H3><br>\n";
                } else {
                    $bodyContent .= "<div style=\"color: red; \">Chyba! Zákazníka nelze akceptovat. Data nelze uložit do databáze. </div><br>\n";
                }

                // TODO: add action into ArchivZmen

            } // konec if odeslat == "OK"
            else { //zobrazime form pro poznamku

                $bodyContent .= "<form action=\"\" method=\"GET\" >";
                $bodyContent .= $csrf_html;

                $bodyContent .= "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >Pokud je třeba vložte poznámku: </div>";

                $bodyContent .= "<div style=\"padding-left: 40px; padding-bottom: 20px;\" >
                 <textarea name=\"pozn\" cols=\"50\" rows=\"6\"></textarea>
             </div>";

                $bodyContent .= "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >
                 <input type=\"submit\" name=\"odeslat\" value=\"OK\" >
             </div>";

                $bodyContent .= "  <input type=\"hidden\" name=\"accept\" value=\"1\">
             <input type=\"hidden\" name=\"id\" value=\"".intval($_GET["id"])."\" >";
                $bodyContent .= "</form>";

            } // konec else odeslat == OK

        } //konec else get <> 1

        $assignData["body"] = $bodyContent;
        return $this->renderer->template($request, $response, 'partner/servis-accept.tpl', $assignData);
    }

    public function changeDesc(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(307)) {
            return $this->response;
        };

        $bodyContent = "";

        $assignData = [
            "page_title" => "Adminator3 :: Partner program :: Servis pozn-update",
        ];

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);

        if ($_GET["edit"] <> 1) {
            $filtr = "";

            //  $list = intval($_GET["list"]);
            $filtr_prio = intval($_GET["filtr_prio"]);
            $filtr_akceptovano = 1;

            //priprava dotazu
            // if($filtr_akceptovano > 0)
            {
                $filtr .= " AND akceptovano = ".$filtr_akceptovano." ";
            }
            if ($filtr_prio > 0) {
                $filtr .= " AND prio = ".$filtr_prio." ";
            }

            $dotaz_sql = "SELECT id, tel, jmeno, adresa, email, poznamky, prio, vlozil, akceptovano, ".
              "akceptovano_kym, akceptovano_pozn, DATE_FORMAT(datum_vlozeni, '%d.%m.%Y %H:%i:%s') ".
              "as datum_vlozeni2 FROM partner_klienti_servis ";

            // if(isset($user)) {
            //     $dotaz_sql .= " WHERE ( vlozil = '".$this->conn_mysql->real_escape_string($user_plaint)."' ".$filtr." ) ";
            // } else
            {
                $dotaz_sql .= " WHERE ( id > 0 ".$filtr." ) ";
            }

            $dotaz_sql .= " ORDER BY id DESC ";

            $poradek = "filtr_akceptovano=".$filtr_akceptovano."&filtr_prio=".$filtr_prio;

            // $format_css = "font-size: 13px; padding-top: 5px; padding-bottom: 15px; ";

            // //vytvoreni objektu
            // $listovani = new c_listing_partner_servis($conn_mysql, "./partner-servis-list.php?".$poradek, 30, $list, "<center><div style=\"".$format_css."\">\n", "</div></center>\n", $dotaz_sql);

            // if(($list == "")||($list == "1")){ $bude_chybet = 0; }
            // else{ $bude_chybet = (($list-1) * $listovani->interval); }

            // $interval = $listovani->interval;

            // $dotaz_limit = " LIMIT ".intval($interval)." OFFSET ".intval($bude_chybet)." ";

            // $dotaz_sql .= $dotaz_limit;

            // $listovani->listInterval();

            $this->psi->update = true;

            $bodyContent .= $this->psi->list_show_legend();

            $bodyContent .= $this->psi->list_show_items($filtr_akceptovano, $filtr_prio, $dotaz_sql);

            // $listovani->listInterval();

        } //konec if update_id > 0
        else {

            $bodyContent .= "<div style=\"padding-left: 40px; padding-bottom: 20px; padding-top: 20px; font-weight: bold; font-size: 18px; \">
                    <span style=\"border-bottom: 1px solid grey; \" >Změna poznámky technika</span>
             </div>";

            if (!(preg_match('/^([[:digit:]])+$/', $_GET["id"]))) {
                $bodyContent .= "<div style=\"color: red; font-weight: bold; padding-left: 20px; padding-bottom: 20px; \">
                                    Chyba! Zákazníka nelze akceptovat! Vstupní data jsou ve špatném formátu! </div> ";

                $assignData["body"] = $bodyContent;
                return $this->renderer->template($request, $response, 'partner/servis-accept.tpl', $assignData, 500);
            }

            if ($_GET["odeslat"] == "OK") {
                //budem ukladat

                $pozn = $this->conn_mysql->real_escape_string($_GET["pozn"]);
                $id = intval($_GET["id"]);

                $uprava = $this->conn_mysql->query("UPDATE partner_klienti_servis SET akceptovano_pozn = '".$pozn."' WHERE id=".$id." Limit 1 ");

                if ($uprava) {
                    $bodyContent .= "<br><H3><div style=\"color: green; padding-left: 20px;\" >Poznámka u zákazníka úspěšně aktualizována.</div></H3><br>\n";
                } else {
                    $bodyContent .= "<div style=\"color: red; \">Chyba! Poznámku nelze upravit.</div><br>\n";
                }

            } // konec if odeslat == "OK"
            else { //zobrazime form pro poznamku

                $id = intval($_GET["id"]);

                //nacteme predchozi data
                $dotaz = $this->conn_mysql->query("SELECT id, akceptovano_pozn FROM partner_klienti_servis WHERE id = '$id' ");

                while ($data = $dotaz->fetch_array()) {
                    $pozn = $data["akceptovano_pozn"];
                }

                $bodyContent .= "<form action=\"\" method=\"GET\" >";
                $bodyContent .= $csrf_html;
                $bodyContent .= "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >Upravte poznámku: </div>";

                $bodyContent .= "<div style=\"padding-left: 40px; padding-bottom: 20px;\" >
                                    <textarea name=\"pozn\" cols=\"50\" rows=\"6\">".htmlspecialchars($pozn)."</textarea>
                                </div>";

                $bodyContent .= "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >
                                    <input type=\"submit\" name=\"odeslat\" value=\"OK\" >
                                </div>";

                $bodyContent .= "<input type=\"hidden\" name=\"edit\" value=\"1\">
                                <input type=\"hidden\" name=\"id\" value=\"".$id."\" >";
                $bodyContent .= "</form>";

            } // konec else odeslat == OK

        } //konec else get == 1

        $assignData["body"] = $bodyContent;
        return $this->renderer->template($request, $response, 'partner/servis-change-desc.tpl', $assignData);
    }
}

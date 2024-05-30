<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
// use App\Partner\partner;

class partnerServisController extends adminatorController
{
    public $smarty;
    public \Monolog\Logger $logger;

    protected $adminator;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    private $psi;

    public function __construct(ContainerInterface $container)
    {
        $this->smarty = $container->get('smarty');
        $this->logger = $container->get('logger');
        $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        parent::__construct($container);

        $this->psi = new \partner_servis($this->conn_mysql, $this->conn_pgsql);
    }

    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(305)) {
            return $this->response;
        };

        $bodyContent = "";

        //priprava form. promennych
        $filtr = "";
        // $list = intval($_GET["list"]);
        $filtr_akceptovano = intval($_GET["filtr_akceptovano"]);
        $filtr_prio = intval($_GET["filtr_prio"]);

        //priprava dotazu

        if($filtr_akceptovano > 0){ $filtr .= " AND akceptovano = ".$filtr_akceptovano." "; }
        if($filtr_prio > 0){ $filtr .= " AND prio = ".$filtr_prio." "; }

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

        $bodyContent .= $this->psi->list_show_items($filtr_akceptovano,$filtr_prio,$dotaz_sql);

        // $listovani->listInterval();

        $assignData = [
            "page_title" => "Adminator3 :: Partner program :: Servis List",
            "body" => $bodyContent
        ];

        return $this->renderer->template($request, $response, 'partner/servis-list.tpl', $assignData);
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(304)) {
            return $this->response;
        };

        $bodyContent = "";

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);

        $this->psi->klient_hledat = $this->conn_mysql->real_escape_string($_POST["klient_hledat"]);
        $this->psi->klient_id = intval($_POST["klient_id"]);

        $this->psi->fill_form = $this->conn_mysql->real_escape_string($_POST["fill_form"]);

        if( (strlen($this->psi->fill_form) > 4 ) ){
           $this->psi->form_copy_values();
        }
        else {
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

        if( ( ($this->psi->odeslat == "ULOŽIT") and ($this->psi->fail == false) ) )
        { // mod ukladani
            $bodyContent .= $this->psi->save_form();
        }
        else
        { // zobrazime formular
            $bodyContent .= "<form action=\"\" method=\"post\" class=\"form-partner-servis-insert\" >";
            $bodyContent .= $csrf_html;
            
            if( isset($this->psi->odeslat) ){
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
}

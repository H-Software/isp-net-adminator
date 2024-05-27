<?php

namespace App\Controllers;

use App\Core\ArchivZmen;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class archivZmenController extends adminatorController
{
    public $conn_mysql;
    public $smarty;
    public $logger;

    protected $container;
    
    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        parent::__construct($container);
    }

    public function archivZmenCat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("archivZmenController\archivZmenCat called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(30)) {
            return $this->response;
        };

        $assignData = array(
            "page_title" => "Adminator3 :: Změny :: kategorie",
            "body" => "Prosím vyberte z podkategorie výše...."
        );

        return $this->renderer->template($request, $response, 'archiv-zmen/archiv-zmen-cat.tpl', $assignData);
    }

    public function archivZmenWork(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("archivZmenController\archivZmenWork called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(30)) {
            return $this->response;
        };

        $az = new ArchivZmen($this->container, $this->smarty);
        $body = $az->archivZmenWork();

        $assignData = array(
            "page_title" => "Adminator3 :: Změny :: Archiv změn Work",
            "body" => $body
        );

        return $this->renderer->template($request, $response, 'archiv-zmen/work.tpl', $assignData);
    }

    public function archivZmenList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("archivZmenController\archivZmenList called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(30)) {
            return $this->response;
        }

        $az = new ArchivZmen($this->container, $this->smarty);
        $body = $az->archivZmenList();

        $assignData = array(
            "page_title" => "Adminator3 :: Změny :: Archiv změn",
            "body" => $body
        );

        return $this->renderer->template($request, $response, 'archiv-zmen/list.tpl', $assignData);
    }

    public function archivZmenUcetni(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("archivZmenController\archivZmenUcetni called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(147)) {
            return $this->response;
        };

        //zacatek vlastniho obsahu
        $action = $_GET["action"];
        $zmena = new \zmeny_ucetni($this->container);

        if($action == "add") { //rezim pridani
            if(!$this->checkLevel(148)) {
                return $this->response;
            };

            $csrf = $this->generateCsrfToken($request, $response, true);
            // $this->logger->info("adminController\header: csrf generated: ".var_export($csrf, true));

            $update_id = $_POST['update_id'];

            if(($update_id > 0)) {
                $update_status = 1;
            }

            $zmena->send = $_POST["send"];
            $zmena->odeslano = $_POST["odeslano"];

            //nacitani promennych
            if(($update_status == 1 and !(isset($zmena->send)))) { //rezim upravy

            } else { //rezim pridani
                $zmena->typ = $_POST["typ"];
                $zmena->text = $_POST["text"];
            }

            //zde generovani a kontrola dat
            $zmena->check_inserted_vars();

            // jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
            if((($zmena->typ != "") and ($zmena->text != ""))) {
                //zde check duplicitnich hodnot ( uprava i pridani )

                //checkem jestli se macklo na tlacitko "OK" :)
                if(preg_match("/OK/", $zmena->odeslano)) { /* zde nic */
                } else {
                    $zmena->fail = "true";
                    $zmena->error .= "<div class=\"form-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>";
                }

                if (!(isset($zmena->fail))) { //ulozeni
                    if($update_status == 1) { //rezim upravy

                        //zde kontrola levelu pro update

                    } else { //rezim pridani
                        $rs = $zmena->save_vars_to_db();

                        if($rs == true) {
                            $db_result = "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze.</div></H3>\n";
                        } else {
                            $db_result = "<br><H3><div style=\"color: red; \">Chyba! Data do databáze nelze uložit. </div></H3>\n";
                        }

                        $this->smarty->assign("db_result", $db_result);
                    }
                } //konec if ! isset fail
                else {
                } // konec else ( !(isset(fail) ), musi tu musi bejt, pac jinak nefunguje nadrazeny if-elseif

            } elseif(isset($zmena->send)) {
                $zmena->error = "<h4 style=\"color: red;\" >Chybí povinné údaje !!! (aktuálně jsou povinné: typ, text)</H4>";
            }

            if((isset($zmena->error)) or (!isset($zmena->send))) { //zobrazeni formu

                $this->smarty->assign("action", "?action=add");
                $this->smarty->assign("csrf_html", $csrf[0]);

                $this->smarty->assign("error", $zmena->error);
                $this->smarty->assign("info", $zmena->info);

                $pole_typy = $zmena->get_types();
                $this->smarty->assign("typ", $pole_typy);

                $this->smarty->assign("typ_select", $zmena->typ);
                $this->smarty->assign("text", $zmena->text);

                $template = "az-ucetni-add-form.tpl";
            } elseif((isset($zmena->writed) or isset($updated))) { //vypis vlozenych udaju
                $template = "az-ucetni-add-list.tpl";
            }

        } //konec if action == add
        elseif($action == "accept") { //rezim akceptovani

        } elseif($action == "update") { //rezim úpravy


        } else {
            $vypis_rs = $zmena->load_sql_result();
            $this->smarty->assign("zmeny", $vypis_rs);

            $this->smarty->assign("link_accept", "/archiv-zmen/ucetni/action-accept/?id=");

            $template = "az-ucetni.tpl";
        }

        $assignData = array(
            "page_title" => "Adminator3 :: Změny pro účetní",
            "link_add" => "/archiv-zmen/ucetni?action=add",
        );

        return $this->renderer->template($request, $response, $template, $assignData);
    }
}

<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Board\boardRss;
use Exception;

class othersController extends adminatorController
{
    public $conn_mysql;
    public $smarty;
    public $logger;

    protected $sentinel;

    protected $adminator;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        $this->sentinel = $this->container->get('sentinel');

        $this->logger->info("othersController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
    }

    public function others(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("othersController\others called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(95)) {
            return $this->response;
        };

        $this->smarty->assign("page_title", "Adminator3 :: Ostatní");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body", "Prosím vyberte z podkategorie výše....");

        $this->smarty->display('others-cat.tpl');

        return $response;
    }

    public function companyWeb(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(151)) {
            return $this->response;
        };

        $this->smarty->assign("page_title", "Adminator3 :: Company Web");

        $this->header($request, $response, $this->adminator);

        try {
            $this->conn_mysql->select_db("company-web");
        } catch (Exception $e) {
            $content  = "Error: Database select failed!";
            $content .= '<div>(Caught exception: ' . $e->getMessage() . ")</div>";

            $this->smarty->assign("alert_type", "danger");
            $this->smarty->assign("alert_content", $content);

            $this->smarty->display("others/company-web-alert.tpl");
            return $response;
        }

        //tab qestions
        try {
            $dotaz_q = $this->conn_mysql->query("
            SELECT id_question, jmeno, prijmeni, telefon, email, vs, dotaz, text, datum_vlozeni
            FROM questions ORDER BY id_question
            ");
        } catch (Exception $e) {
            $content  = "Error: Database query failed (table questions)!";
            $content .= '<div>(Caught exception: ' . $e->getMessage() . ")</div>";

            $this->smarty->assign("alert_type", "danger");
            $this->smarty->assign("alert_content", $content);

            $this->smarty->display("others/company-web-alert.tpl");
            return $response;
        }

        $pole_q = array();

        while($data_q = $dotaz_q->fetch_array()) {
            $pole_q[] = array(
                "id_question" => $data_q["id_question"], "jmeno" => $data_q["jmeno"],
                "prijmeni" => $data_q["prijmeni"], "telefon" => $data_q["telefon"],
                "email" => $data_q["email"], "vs" => $data_q["vs"],
                "dotaz" => $data_q["dotaz"], "text" => $data_q["text"],
                "datum_vlozeni" => $data_q["datum_vlozeni"]
            );
        }

        $this->smarty->assign("data_q", $pole_q);

        //tab orders
        try {
            $dotaz_o = $this->conn_mysql->query("
            SELECT id_order, jmeno, prijmeni, adresa, telefon, email,
                internet, text_internet, iptv, balicek, text_iptv,
                voipcislo, voip, text_voip, poznamka, datum_vlozeni
            FROM orders ORDER BY id_order
            ");
        } catch (Exception $e) {
            $content  = "Error: Database query failed (table orders)!";
            $content .= '<div>(Caught exception: ' . $e->getMessage() . ")</div>";

            $this->smarty->assign("alert_type", "danger");
            $this->smarty->assign("alert_content", $content);

            $this->smarty->display("others/company-web-alert.tpl");
            return $response;
        }

        $pole_o = array();

        while($data_o = $dotaz_o->fetch_array()) {
            $pole_o[] = array(
                "id_order" => $data_o["id_order"], "jmeno" => $data_o["jmeno"],
                "prijmeni" => $data_o["prijmeni"], "adresa" => $data_o["adresa"],
                "telefon" => $data_o["telefon"], "email" => $data_o["email"],
                "internet" => $data_o["internet"], "text_internet" => $data_o["text_internet"],
                "iptv" => $data_o["iptv"], "balicek" => $data_o["balicek"],
                "text_iptv" => $data_o["text_iptv"], "voipcislo" => $data_o["voipcislo"],
                "voip" => $data_o["voip"], "text_voip" => $data_o["text_voip"],
                "poznamka" => $data_o["poznamka"], "datum_vlozeni" => $data_o["datum_vlozeni"]
            );
        }

        $this->smarty->assign("data_o", $pole_o);

        //print_r($pole_o);

        //zpatky default DB
        try {
            $count = $this->conn_mysql->select_db("adminator2");
        } catch (Exception $e) {
            die(init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
        }

        //finalni zobrazeni stranky
        $this->smarty->display("others/company-web.tpl");

        return $response;
    }

    public function board(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("othersController\board called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(87)) {
            return $this->response;
        };

        $this->smarty->assign("page_title", "Adminator3 :: Board");

        $this->header($request, $response, $this->adminator);

        $nastenka = new \board($this->container);

        $rss_token = $this->adminator->getUserToken();
        if($rss_token !== false) {
            $this->smarty->assign("token", $rss_token);
        } else {
            $this->logger->error("othersController\board: getUserToken failed");
        }
        $this->smarty->assign("datum", date("j. m. Y"));

        $nastenka->what = $_GET["what"];
        $nastenka->action = $_GET["action"];
        $nastenka->page = $_GET["page"];

        $nastenka->send = $_GET["send"];
        $nastenka->sent = $_POST["sent"];

        $nastenka->author = $_POST["author"];
        $nastenka->email = $_POST["email"];

        $nastenka->to_date = $_POST["to_date"];
        $nastenka->from_date = $_POST["from_date"];

        $nastenka->subject = $_POST["subject"];
        $nastenka->body = $_POST["body"];

        $nastenka->prepare_vars();

        if($nastenka->action == "view") :

            $this->smarty->assign("mod", 1);

            if($nastenka->what == "new") {
                $this->smarty->assign("mod_hlaska", "->> Aktuální zprávy");
            } else {
                $this->smarty->assign("mod_hlaska", "->> Staré zprávy");
            }

        $nastenka->view_number = 10; //zprávy budou zobrazeny po ...

        $zpravy = $nastenka->show_messages();

        $this->smarty->assign("zpravy", $zpravy);

        $page = $nastenka->show_pages();
        $this->smarty->assign("strany", $page);

        else:

            $this->smarty->assign("mod", 2);

            $nastenka->write = false; //prvne předpokládáme zobr. formuláře

            if(isset($nastenka->sent)) {
                $nastenka->check_vars();
            }

            if($nastenka->write) { //ulozeni dat

                $this->smarty->assign("mod", 3); //vysledny formular ulozeni

                $nastenka->convert_vars();
                $add = $nastenka->insert_into_db();

                $this->smarty->assign("rs", $add);
                $this->smarty->assign("body", $nastenka->error);

                // if($add){
                //     header("Location: others-board.php"); //přesuneme se na úvodní stránku
                // }
            } else { //zobrazujeme formulář

                $csrf = $this->generateCsrfToken($request, $response, true);
                // $this->logger->info("adminController\header: csrf generated: ".var_export($csrf, true));
                $this->smarty->assign("csrf_html", $csrf[0]);

                $this->smarty->assign("enable_calendar", 1);

                $this->smarty->assign("mod", 2); //zobrazujeme formular pro zadavani dat
                $this->smarty->assign("mod_hlaska", "->> Přidat zprávu");

                $this->smarty->assign("nick", \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email);

                $this->smarty->assign("email", $nastenka->email);
                $this->smarty->assign("subject", $nastenka->subject);

                $this->smarty->assign("from_date", $nastenka->from_date);
                $this->smarty->assign("to_date", $nastenka->to_date);

                $this->smarty->assign("body", $nastenka->body);

                $this->smarty->assign("error", $nastenka->error);
            }

        endif;

        $this->smarty->display('others/board.tpl');

        return $response;
    }

    public function boardRss(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");
        $data = "";

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(309)) {
            return $this->response;
        };

        $rss = new boardRss($this->container);

        $rs_check_login = $this->adminator->verifyUserToken($request);

        if($rs_check_login == false) {
            $data = "";

            $row = new \stdClass();
            $row->subject = "Unauthorized";
            $row->body = "Wrong use token, please check the URL of RSS.";
            $row->author = "System";

            $data .= $rss->putHeader();
            $data .= $rss->putItem($row);
            $data .= $rss->putEnd();

            $newResponse = $response
                                ->withStatus(401)
                                ->withHeader('Content-type', 'text/xml');
        } else {
            $rs = $rss->exportRSS();

            if($rs === false) {
                $newResponse = $response
                                ->withStatus(500)
                                ->withHeader('Content-type', 'text/xml');

                $row = new \stdClass();
                $row->subject = "Internal Server Error";
                $row->body = "Error! Unable to load data from database.";
                $row->author = "System";

                $data .= $rss->putHeader();
                $data .= $rss->putItem($row);
                $data .= $rss->putEnd();

            } else {
                $newResponse = $response->withHeader('Content-type', 'text/xml');
                $data = $rs;
            }
        }

        $newResponse->getBody()->write($data);

        return $newResponse;
    }
}

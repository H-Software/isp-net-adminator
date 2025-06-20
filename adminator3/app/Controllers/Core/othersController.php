<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Board\boardRss;
use Exception;

class othersController extends adminatorController
{
    public \mysqli|\PDO $conn_mysql;

    // public \Smarty $smarty;

    public \Monolog\Logger $logger;

    protected $adminator;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    public function __construct(ContainerInterface $container, $adminatorInstance = null)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        // $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        parent::__construct($container, $adminatorInstance);
    }

    public function others(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(95)) {
            return $this->response;
        };

        $assignData = array(
            "page_title" => "Adminator3 :: Ostatní",
            "body" => "Prosím vyberte z podkategorie výše...."
        );

        return $this->renderer->template($request, $response, 'others-cat.tpl', $assignData);
    }

    public function companyWeb(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(151)) {
            return $this->response;
        };

        $assignData = array(
            "page_title" => "Adminator3 :: Company Web",
        );

        try {
            $this->conn_mysql->select_db("company-web");
        } catch (Exception $e) {
            $content  = "Error: Database select failed!";
            $content .= '<div>(Caught exception: ' . $e->getMessage() . ")</div>";

            $assignData["alert_type"] = "danger";
            $assignData["alert_content"] = $content;

            return $this->renderer->template($request, $response, 'others/company-web-alert.tpl', $assignData, 500);
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

            $assignData["alert_type"] = "danger";
            $assignData["alert_content"] = $content;

            return $this->renderer->template($request, $response, 'others/company-web-alert.tpl', $assignData, 500);
        }

        $pole_q = array();

        while ($data_q = $dotaz_q->fetch_array()) {
            $pole_q[] = array(
                "id_question" => $data_q["id_question"], "jmeno" => $data_q["jmeno"],
                "prijmeni" => $data_q["prijmeni"], "telefon" => $data_q["telefon"],
                "email" => $data_q["email"], "vs" => $data_q["vs"],
                "dotaz" => $data_q["dotaz"], "text" => $data_q["text"],
                "datum_vlozeni" => $data_q["datum_vlozeni"]
            );
        }

        $assignData["data_q"] = $pole_q;

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

            $assignData["alert_type"] = "danger";
            $assignData["alert_content"] = $content;

            return $this->renderer->template($request, $response, 'others/company-web-alert.tpl', $assignData, 500);
        }

        $pole_o = array();

        while ($data_o = $dotaz_o->fetch_array()) {
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

        $assignData["data_o"] = $pole_o;

        //print_r($pole_o);

        //zpatky default DB
        try {
            $this->conn_mysql->select_db("adminator2");
        } catch (Exception $e) {
            $content  = "Error: Database select to adminator2 failed!";
            $content .= '<div>(Caught exception: ' . $e->getMessage() . ")</div>";

            $assignData["alert_type"] = "danger";
            $assignData["alert_content"] = $content;

            return $this->renderer->template($request, $response, 'others/company-web-alert.tpl', $assignData, 500);
        }

        //finalni zobrazeni stranky
        return $this->renderer->template($request, $response, 'others/company-web.tpl', $assignData, 500);
    }

    public function board(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(87)) {
            return $this->response;
        };

        $assignData = array(
            "page_title" => "Adminator3 :: Board",
        );

        $nastenka = new \board($this->container);

        $rss_token = $this->adminator->getUserToken();
        if ($rss_token !== false) {
            $assignData["token"] = $rss_token;
        } else {
            $this->logger->error("othersController\board: getUserToken failed");
        }
        $assignData["datum"] = date("j. m. Y");

        $nastenka->load_vars($request);
        $nastenka->prepare_vars();

        if ($nastenka->action == "view") {
            $assignData["mod"] = 1;

            if ($nastenka->what == "new") {
                $assignData["mod_hlaska"] = "->> Aktuální zprávy";
            } else {
                $assignData["mod_hlaska"] = "->> Staré zprávy";
            }

            $nastenka->view_number = 10; //zprávy budou zobrazeny po ...

            $assignData["zpravy"] = $nastenka->show_messages();
            $assignData["strany"] = $nastenka->show_pages();

        } else {
            $assignData["mod"] = 2;

            $nastenka->write = false; //prvne předpokládáme zobr. formuláře

            if (isset($nastenka->sent)) {
                $nastenka->check_vars();
            }

            if ($nastenka->write) { //ulozeni dat
                $assignData["mod"] = 3; //vysledny formular ulozeni

                $nastenka->convert_vars();
                $rs = $nastenka->insert_into_db();

                $assignData["mod_hlaska"] = "->> Přidat zprávu";
                if ($rs == false) {
                    $assignData["rs"] = $rs;
                    $assignData["body"] = $nastenka->error;
                } else {
                    $assignData["rs"] = true;
                }

                // if($add){
                //     header("Location: others-board.php"); //přesuneme se na úvodní stránku
                // }
            } else { //zobrazujeme formulář

                list($csrf_html) = $this->generateCsrfToken($request, $response, true);
                // $this->logger->info("adminController\header: csrf generated: ".var_export($csrf, true));
                $assignData["csrf_html"] = $csrf_html;

                $assignData["enable_calendar"] = 1;
                $assignData["mod"] = 2; //zobrazujeme formular pro zadavani dat
                $assignData["mod_hlaska"] = "->> Přidat zprávu";
                $assignData["nick"] = $this->adminator->userIdentityUsername;
                $assignData["email"] = $nastenka->email;
                $assignData["subject"] = $nastenka->subject;
                $assignData["from_date"] = $nastenka->from_date;
                $assignData["to_date"] = $nastenka->to_date;
                $assignData["body"] = $nastenka->body;
                $assignData["error"] = $nastenka->error;
            }
        }

        return $this->renderer->template($request, $response, 'others/board.tpl', $assignData);
    }

    public function boardRss(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");
        $data = "";

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(309)) {
            return $this->response;
        };

        $rss = new boardRss($this->container);

        $rs_check_login = $this->adminator->verifyUserToken($request);

        if ($rs_check_login == false) {
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

            if ($rs === false) {
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

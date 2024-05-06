<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController extends adminatorController
{
    public $conn_mysql;

    public $pdoMysql;

    public $conn_pgsql;

    public $settings;

    public $smarty;
    public $logger;

    public $adminator;

    public function __construct(ContainerInterface $container, $adminatorInstance = null)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->conn_pgsql = $this->container->get('connPgsql');
        $this->settings = $this->container->get('settings');
        $this->pdoMysql = $this->container->get('pdoMysql');

        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        $this->logger->info("homeController\__construct called");

        if(isset($adminatorInstance)) {
            $this->adminator = $adminatorInstance;
        } else {
            $this->adminator = new \App\Core\adminator(
                $this->conn_mysql,
                $this->smarty,
                $this->logger,
                null,
                $this->pdoMysql,
                $this->settings,
            );
        }
    }

    public function home(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->logger->info("homeController\home called");

        $this->checkLevel(38, $this->adminator);

        if ($request->getMethod() == "POST") {
            $data = $request->getParsedBody();
            $this->logger->debug("homeController\home post data: ".var_export($data, true));
        }

        $this->smarty->assign("page_title", "Adminator3 :: úvodní stránka");

        $this->header($request, $response, $this->adminator);

        // messages from change-password an etc
        $flashMessages = $this->container->get('flash')->getMessages();
        $this->smarty->assign("flash_messages", $flashMessages);

        //echo "<pre>" . var_export($flashMessages, true) ."</pre>";

        //vlozeni prihlasovaci historie
        $this->adminator->list_logged_users();

        //informace z modulu neuhrazené faktury
        //
        $neuhr_faktury_pole = $this->adminator->show_stats_faktury_neuhr();
        $this->logger->info("show_stats_faktury_neuhr: result: " . var_export($neuhr_faktury_pole, true));

        $this->smarty->assign("d", $neuhr_faktury_pole[0]);

        $this->smarty->assign("count_total", $neuhr_faktury_pole[0]);
        $this->smarty->assign("count_ignored", $neuhr_faktury_pole[1]);
        $this->smarty->assign("count_unknown", $neuhr_faktury_pole[2]);
        $this->smarty->assign("date_last_import", $neuhr_faktury_pole[3]);

        $this->smarty->assign("stats_faktury_neuhr_error_messages", $neuhr_faktury_pole[4]);

        $this->opravy_a_zavady();

        $this->board();

        $this->logger->info("homeController\home: end of rendering");
        $this->smarty->display('home.tpl');

        return $response;
    }

    public function board()
    {
        //generovani zprav z nastenky

        if ($this->adminator->checkLevel(87, false) === true) {
            $this->logger->info("homeController\board allowed");

            $this->smarty->assign("nastenka_povoleno", 1);
            $this->smarty->assign("datum", date("j. m. Y"));
            $this->smarty->assign("sid", "");

            $nastenka = new \board($this->container);

            $rs = $nastenka->prepare_vars();
            $this->logger->debug("homeController\board: prepare_vars result: " . var_export($rs, true));

            $nastenka->view_number = 10; //zprávy budou zobrazeny po ...

            $zpravy = $nastenka->show_messages();
            $this->logger->debug("homeController\board: show_messages result: " . var_export($zpravy, true));

            if(isset($nastenka->query_error)) {
                $this->smarty->assign("query_error", $nastenka->query_error);
            }

            $this->smarty->assign("zpravy", $zpravy);

            $page = $nastenka->show_pages();

            $this->smarty->assign("strany", $page);

        }
    }

    public function opravy_a_zavady()
    {
        //opravy a zavady vypis
        $pocet_bunek = 11;

        if ($this->adminator->checkLevel(101, false) === true) {
            $this->logger->info("homeController\opravy_a_zavady allowed");

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

            $opravy = new \opravy($this->container);

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
    }
}

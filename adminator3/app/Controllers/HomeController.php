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

    private $sentinel;

    private $adminator;

    private $opravyInstance;

    public function __construct(ContainerInterface $container, $adminatorInstance = null, $opravyInstance = null)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->conn_pgsql = $this->container->get('connPgsql');
        $this->settings = $this->container->get('settings');
        $this->pdoMysql = $this->container->get('pdoMysql');

        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        $this->sentinel = $this->container->get('sentinel');

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

        if(isset($opravyInstance)) {
            $this->opravyInstance = $opravyInstance;
        } else {
            $this->opravyInstance = new \opravy($this->container);
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


        if ($this->adminator->checkLevel(101, false) === true) {
            $this->logger->info("homeController\opravy_a_zavady allowed");
            $this->adminator->get_opravy_a_zavady($this->opravyInstance);
        } else {
            $this->logger->warning("homeController\opravy_a_zavady not allowed");
        }

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

            $nastenka = new \board($this->container);

            $rss_token = $this->adminator->getUserToken();
            if($rss_token !== false) {
                $this->smarty->assign("token", $rss_token);
            } else {
                $this->logger->error("othersController\board: getUserToken failed");
            }
            $this->smarty->assign("datum", date("j. m. Y"));

            $rs = $nastenka->prepare_vars();
            $this->logger->debug("homeController\board: prepare_vars result: " . var_export($rs, true));

            $nastenka->view_number = 10; //zprávy budou zobrazeny po ...

            $zpravy = $nastenka->show_messages();
            // $this->logger->debug("homeController\board: show_messages result: " . var_export($zpravy, true));

            if(isset($nastenka->query_error)) {
                $this->smarty->assign("query_error", $nastenka->query_error);
            }

            $this->smarty->assign("zpravy", $zpravy);

            $page = $nastenka->show_pages();

            $this->smarty->assign("strany", $page);

        }
    }


}

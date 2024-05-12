<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class objektyController extends adminatorController
{
    public $conn_mysql;
    public $smarty;
    public $logger;
    public $app;

    public $adminator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        $this->logger->info("objektyController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
    }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("objektyController\cat called");

        $this->checkLevel(93, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Objekty");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body", "Prosím vyberte z podkategorie výše....");

        $this->smarty->display('objekty/subcat.tpl');

        return $response;
    }

    public function stb(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $csrf_html = $this->generateCsrfToken($request, $response, true);
        $stb = new \App\Core\stb($this->container);
        $stb->csrf_html = $csrf_html[0];

        $this->logger->info("objektyController\\stb called");

        $this->checkLevel(135, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Objekty STB");

        $this->header($request, $response, $this->adminator);

        if ($this->adminator->checkLevel(137, false) === true) {
            $stb->enable_modify_action = true;
        }

        if ($this->adminator->checkLevel(152, false) === true) {
            $stb->enable_unpair_action = true;
        }

        $rs = $stb->stbListGetBodyContent();

        $this->smarty->assign("body", $rs[0]);

        $this->smarty->display('objekty/stb.tpl');

        return $response;
    }


    public function stbAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("objektyController\\stbAction called");

        $this->checkLevel(136, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: STB :: Actions");

        $this->header($request, $response, $this->adminator);

        $stb = new \App\Core\stb($this->container);

        $csrf = $this->generateCsrfToken($request, $response, false);

        $rs = $stb->stbAction($request, $response, $csrf);

        if (isset($rs[1])) {
            // view form
            $this->smarty->assign($rs[0]);

            $this->smarty->display($rs[1]);
        } else {
            // result view, ..
            $this->smarty->assign("body", $rs[0]);
            $this->smarty->display('objekty/stb-action.tpl');
        }

        return $response;
    }

    public function objekty(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("objektyController\objekty called");

        $this->checkLevel(1, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Objekty");

        $this->header($request, $response, $this->adminator);

        $dns_find = $_GET['dns_find'];
        $ip_find = $_GET['ip_find'];

        if((strlen($dns_find) == 0)) {
            $dns_find = "%";
        }

        $this->smarty->assign("es", $_GET['es']);
        $this->smarty->assign("mod_vypisu", $_GET['mod_vypisu']);

        $this->smarty->assign("dns_find", $dns_find);
        $this->smarty->assign("ip_find", $ip_find);

        $objekt = new \App\Core\objekt($this->container);
        $objekt->dns_find = $dns_find;
        $objekt->ip_find = $ip_find;

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $objekt->csrf_html = $csrf_html;

        list($output, $errors, $exportLink) = $objekt->listGetBodyContent();

        if(strlen($errors) > 0) {
            $this->smarty->assign("body", $errors);
        } else {
            $this->smarty->assign("export_link", $exportLink);
            $this->smarty->assign("body", $output);
        }

        $this->smarty->display('objekty/list.tpl');

        return $response;
    }

    public function objektyAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("objektyController\objektyAction called");

        $this->checkLevel(2, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Objekty :: Action");

        $this->header($request, $response, $this->adminator);

        $objekt = new \App\Core\objekt($this->container);
        $objekt->csrf_html = $this->generateCsrfToken($request, $response, true);
        $objekt->adminator = $this->adminator;

        $objekt->mod_objektu = intval($_POST["mod_objektu"]);

        // $objekt->dns_find = $dns_find;
        // $objekt->ip_find = $ip_find;

        $objekt->actionPrepareVars();

        if($objekt->mod_objektu == 2) {
            $output = $objekt->actionFiber();
        } else {
            $output = $objekt->actionWifi();
        }

        $this->smarty->assign("body", $output);
        $this->smarty->display('objekty/action.tpl');

        return $response;
    }
}

<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class objektyController extends adminatorController
{
    public \mysqli|\PDO $conn_mysql;
    public $smarty;

    public \Monolog\Logger $logger;
    public $app;

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

        $this->logger->info("objektyController\__construct called");

        parent::__construct($container);
    }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("objektyController\cat called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(93)) {
            return $this->response;
        };

        $assignData = [
            "page_title" => "Adminator3 :: Objekty",
            "body" => "Prosím vyberte z podkategorie výše...."
        ];

        return $this->renderer->template($request, $response, 'objekty/subcat.tpl', $assignData);
    }

    public function stb(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("objektyController\\stb called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(135)) {
            return $this->response;
        };

        $csrf_html = $this->generateCsrfToken($request, $response, true);
        $stb = new \App\Core\stb($this->container);
        $stb->csrf_html = $csrf_html[0];

        if ($this->adminator->checkLevel(137) === true) {
            $stb->enable_modify_action = true;
        }

        if ($this->adminator->checkLevel(152) === true) {
            $stb->enable_unpair_action = true;
        }

        if ($this->adminator->checkLevel(310) === true) {
            $stb->enable_delete_action = true;
        }

        list($content) = $stb->stbListGetBodyContent();

        $assignData = [
            "page_title" => "Adminator3 :: Objekty STB",
            "body" => $content
        ];

        return $this->renderer->template($request, $response, 'objekty/stb.tpl', $assignData);
    }


    public function stbAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("objektyController\\stbAction called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(136)) {
            return $this->response;
        };

        $assignData = [
            "page_title" => "Adminator3 :: STB :: Action",
        ];

        $stb = new \App\Core\stb($this->container);

        $csrf = $this->generateCsrfToken($request, $response, false);

        $rs = $stb->stbAction($request, $response, $csrf);

        if (isset($rs[1])) {
            // view form
            $this->smarty->assign($rs[0]);
            $templateName = $rs[1];
        } else {
            // result view, ..
            $this->smarty->assign("body", $rs[0]);
            $templateName = 'objekty/stb-action.tpl';
        }

        return $this->renderer->template($request, $response, $templateName, $assignData);
    }

    public function objekty(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("objektyController\objekty called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(1)) {
            return $this->response;
        };

        $assignData = [
            "page_title" => "Adminator3 :: Objekty",
        ];

        $dns_find = $_GET['dns_find'];
        $ip_find = $_GET['ip_find'];

        if((strlen($dns_find) == 0)) {
            $dns_find = "%";
        }

        $assignData["es"] = $_GET['es'];
        $assignData["mod_vypisu"] = $_GET['mod_vypisu'];

        $assignData["dns_find"] = $dns_find;
        $assignData["ip_find"] = $ip_find;

        $objekt = new \App\Core\objekt($this->container);
        $objekt->dns_find = $dns_find;
        $objekt->ip_find = $ip_find;
        $objekt->adminator = $this->adminator;

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $objekt->csrf_html = $csrf_html;

        list($output, $errors, $exportLink) = $objekt->listGetBodyContent();

        if(strlen($errors) > 0) {
            $assignData["body"] = $errors;
        } else {
            $assignData["export_link"] = $exportLink;
            $assignData["body"] = $output;
        }

        return $this->renderer->template($request, $response, 'objekty/list.tpl', $assignData);
    }

    public function objektyAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("objektyController\objektyAction called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(2)) {
            return $this->response;
        };

        $assignData = [
            "page_title" => "Adminator3 :: Objekty :: Action",
        ];

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

        $assignData["body"] = $output;
        $assignData["p_bs_alerts"] = $objekt->p_bs_alerts;

        return $this->renderer->template($request, $response, 'objekty/action.tpl', $assignData);
    }
}

<?php

namespace App\Controllers;

use PDO;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class objektyController extends adminatorController
{
    public \mysqli|\PDO $conn_mysql;
    public \Smarty $smarty;

    public \Monolog\Logger $logger;
    public $app;

    protected $sentinel;

    protected $adminator;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    public function __construct(ContainerInterface $container, $adminatorInstance = null)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        $this->sentinel = $this->container->get('sentinel');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        parent::__construct($container, $adminatorInstance);
    }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(93)) {
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
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(135)) {
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
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(136)) {
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
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(1)) {
            return $this->response;
        };

        $http_response_code = 200;
        $usePDO = false;
        if (array_key_exists("usePDO", $args)) {
            $usePDO = true;
        }

        $objekt = new \App\Core\objekt($this->container, $usePDO);
        $objekt->request = $request;

        $assignData = [
            "page_title" => "Adminator3 :: Objekty",
        ];

        foreach ($request->getQueryParams() as $i => $v) {
            if (preg_match('/^(dns_find|ip_find|es|mod_vypisu)$/', $i) and strlen($v) > 0) {
                $$i = $request->getQueryParams()[$i];
            }
        }

        if (empty($dns_find)) {
            $dns_find = "%";
        }
        $objekt->dns_find = $dns_find;
        $assignData["dns_find"] = $dns_find;

        if (isset($es)) {
            $assignData["es"] = $es;
        }

        if (isset($mod_vypisu)) {
            $assignData["mod_vypisu"] = $mod_vypisu;
        }

        if (isset($ip_find)) {
            $objekt->ip_find = $ip_find;
            $assignData["ip_find"] = $ip_find;
        }

        $objekt->adminator = $this->adminator;

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $objekt->csrf_html = $csrf_html;

        list($output, $errors, $exportLink) = $objekt->listGetBodyContent();

        if (strlen($errors) > 0) {
            $assignData["body"] = $errors;
            $http_response_code = 500;
        } else {
            $assignData["export_link"] = $exportLink;
            $assignData["body"] = $output;
        }

        $assignData["p_bs_alerts"] = $objekt->p_bs_alerts;

        return $this->renderer->template($request, $response, 'objekty/list.tpl', $assignData, $http_response_code);
    }

    public function objektyAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(2)) {
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

        if ($objekt->mod_objektu == 2) {
            $output = $objekt->actionFiber();
        } else {
            $output = $objekt->actionWifi();
        }

        $assignData["body"] = $output;
        $assignData["p_bs_alerts"] = $objekt->p_bs_alerts;

        return $this->renderer->template($request, $response, 'objekty/action.tpl', $assignData);
    }
}

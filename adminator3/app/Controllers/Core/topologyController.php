<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Core\Topology\RouterAction;
use App\Core\Topology\Topology;
use App\Core\Topology\nodeAction;

class topologyController extends adminatorController
{
    // public \mysqli|\PDO $conn_mysql;

    public \Smarty $smarty;

    public \Monolog\Logger $logger;

    protected $settings;

    // protected $sentinel;

    // protected $adminator;

    protected $container;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    public function __construct(ContainerInterface $container, $adminatorInstance = null)
    {
        $this->container = $container;
        // $this->conn_mysql = $container->get('connMysql');
        $this->smarty = $container->get('smarty');
        $this->logger = $container->get('logger');
        // $this->sentinel = $container->get('sentinel');
        $this->settings = $container->get('settings');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        parent::__construct($container, $adminatorInstance);
    }

    public function nodeList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(5)) {
            return $this->response;
        };

        $i = new Topology($this->container);
        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $i->csrf_html = $csrf_html;

        $output = $i->getNodeList();

        $assignData = [
            "page_title" => "Adminator3 :: Topologie :: Node list",
            "body" => $output
        ];

        return $this->renderer->template($request, $response, 'topology/node-list.tpl', $assignData);
    }

    public function nodeAdd(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(4)) {
            return $this->response;
        };

        $i = new nodeAction($this->container);
        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $i->csrf_html = $csrf_html;
        $output = $i->add($request);

        $assignData = [
            "page_title" => "Adminator3 :: Topologie :: Node Add",
            "body" => $output
        ];

        return $this->renderer->template($request, $response, 'topology/node-add.tpl', $assignData);
    }

    public function nodeUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(25)) {
            return $this->response;
        };

        $i = new nodeAction($this->container);
        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $i->csrf_html = $csrf_html;
        $output = $i->update($request);

        $assignData = [
            "page_title" => "Adminator3 :: Topologie :: Node Update",
            "body" => $output
        ];

        return $this->renderer->template($request, $response, 'topology/node-update.tpl', $assignData);
    }

    public function routerList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(85)) {
            return $this->response;
        };

        $i = new Topology($this->container);
        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $i->csrf_html = $csrf_html;

        $output = $i->getRouterList();

        $assignData = [
            "page_title" => "Adminator3 :: Topologie :: Router list",
            "body" => $output
        ];

        return $this->renderer->template($request, $response, 'topology/router-list.tpl', $assignData);
    }

    public function routerAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(86)) {
            return $this->response;
        };

        $assignData = [
            "page_title" => "Adminator3 :: Topologie :: Router Action",
        ];

        $i = new RouterAction($this->container);
        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $i->csrf_html = $csrf_html;

        list($content, $http_status_code) = $i->action();

        $assignData['body'] = $content;

        $assignData["p_bs_alerts"] = $i->p_bs_alerts;

        return $this->renderer->template($request, $response, 'topology/router-action.tpl', $assignData, $http_status_code);
    }
}

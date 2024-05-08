<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Partner\partner;

class partnerController extends adminatorController
{
    public $conn_mysql;
    public $smarty;
    public $logger;

    protected $adminator;

    private $partnerInstance;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        $this->logger->info("partnerController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);

        $this->partnerInstance = new partner($this->container);

    }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(75, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Partner program");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body", "Prosím vyberte z podkategorie výše....");

        $this->smarty->display('partner/partner-cat.tpl');

        return $response;
    }

    public function orderCat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(75, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Partner program :: Orders");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body", "Prosím vyberte z podkategorie výše....");

        $this->smarty->display('partner/order-cat.tpl');

        return $response;
    }

    public function orderList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(76, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Partner program :: Order List");

        $this->header($request, $response, $this->adminator);

        $listOutput = $this->partnerInstance->list();

        $this->smarty->assign("body", $listOutput[0]);

        $this->smarty->display('partner/order-list.tpl');

        return $response;
    }

    public function orderAdd(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(75, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Partner :: Order Add");

        $this->header($request, $response, $this->adminator);

        // CSRF token name and value for update form
        list($csrf_html) = $this->generateCsrfToken($request, $response, true);

        $this->logger->debug("partnerController\orderAdd: csrf generated: ".var_export($csrf_html, true));

        $this->partnerInstance->csrf_html = $csrf_html;
        $this->partnerInstance->form_uri = $request->getUri();

        $this->partnerInstance->add();

        return $response;
    }

    public function orderAccept(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(77, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Partner :: Order Accept");

        $this->header($request, $response, $this->adminator);

        $this->partnerInstance->accept();

        return $response;
    }

    public function orderChangeDesc(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(119, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Partner :: Order Update Desc");

        $this->header($request, $response, $this->adminator);

        $this->partnerInstance->updateDesc();

        return $response;
    }

    public function orderChangeStatus(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");


    
        return $response;

    }
}

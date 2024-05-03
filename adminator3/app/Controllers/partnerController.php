<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Illuminate\Database\Capsule\Manager as DB;

use App\Partner\partner;

class partnerController extends adminatorController
{
    public $conn_mysql;
    public $smarty;
    public $logger;

    protected $adminator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        $this->logger->info("partnerController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
    }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("partnerController\cat called");

        $this->checkLevel(75, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Partner program");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body", "Prosím vyberte z podkategorie výše....");

        $this->smarty->display('partner/partner-cat.tpl');

        return $response;
    }

    public function orderCat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("partnerController\orderCat called");

        $this->checkLevel(75, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Partner program :: Orders");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body", "Prosím vyberte z podkategorie výše....");

        $this->smarty->display('partner/order-cat.tpl');

        return $response;
    }

    public function orderList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("partnerController\orderList called");

        $this->checkLevel(76, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Partner program :: Order List");

        $this->header($request, $response, $this->adminator);

        $partner = new partner($this->container);
        $listOutput = $partner->list();

        $this->smarty->assign("body", $listOutput[0]);

        $this->smarty->display('partner/order-list.tpl');

        return $response;
    }

    public function orderAdd(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("partnerController\orderAdd called");

        $this->checkLevel(75, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Partner :: Order Add");

        $this->header($request, $response, $this->adminator);

        // CSRF token name and value for update form
        list($csrf_html_empty, $csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value) = $this->generateCsrfToken($request, $response);

        $this->logger->info("partnerController\orderAdd: csrf generated: ".var_export($csrf_name, true));

        $partner = new partner($this->container);
        $rs = $partner->add();


        // $rs = $this->admin->levelAction($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value);

        // $this->smarty->assign("body", $rs[0]);

        // $this->smarty->display('admin/level-action.tpl');

        return $response;
    }
}

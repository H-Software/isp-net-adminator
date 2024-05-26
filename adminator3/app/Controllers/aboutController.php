<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class aboutController extends adminatorController
{
    public $conn_mysql;
    public $smarty;
    public $logger;

    protected $sentinel;

    protected $adminator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        $this->sentinel = $this->container->get('sentinel');

        $this->logger->info("aboutController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
    }

    public function about(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("aboutController\about called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(142, true)){
            return $this->response;
        };

        $this->smarty->assign("page_title", "Adminator3 :: O programu");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body", "Prosím vyberte z podkategorie výše....");

        $this->smarty->display('about/about.tpl');

        return $response;
    }

    public function changesOld(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("aboutController\changesOld called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(144, true)){
            return $this->response;
        };

        $this->smarty->assign("page_title", "Adminator3 :: O programu :: Staré změny");

        $this->header($request, $response, $this->adminator);

        $this->smarty->display('about/about-changes-old.tpl');

        return $response;
    }

    public function changes(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("aboutController\changes called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(145, true)){
            return $this->response;
        };

        $this->smarty->assign("page_title", "Adminator3 :: O programu :: Změny");

        $this->header($request, $response, $this->adminator);

        $this->smarty->display('about/about-changes.tpl');

        return $response;
    }
}

<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class platbyController extends adminatorController
{
    // public \mysqli|\PDO $conn_mysql;

    // public \Smarty $smarty;

    public \Monolog\Logger $logger;

    // protected $sentinel;

    protected $adminator;

    protected $container;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get('logger');
        // $this->conn_mysql = $container->get('connMysql');
        // $this->smarty = $container->get('smarty');
        // $this->sentinel = $this->container->get('sentinel');

        $this->logger->info("platbyController\__construct called");

        parent::__construct($container);
    }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("platbyController\cat called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(92)) {
            return $this->response;
        };

        $assignData = array(
            "page_title" => "Adminator3 :: Platby",
            "body" => "Prosím vyberte z podkategorie výše....",
            "link_a2_platby" => fix_link_to_another_adminator("/platby.php"),
            "link_a2_faktury" => fix_link_to_another_adminator("/faktury.php")
        );

        return $this->renderer->template($request, $response, 'platby/platby-cat.tpl', $assignData);
    }

    public function platby(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // $this->logger->info("platbyController\\platby called");

        // $this->request = $request;
        // $this->response = $response;

        // if(!$this->checkLevel()) {
        //     return $this->response;
        // };

        // $assignData = array(
        //     "page_title" => "Adminator3 :: Platby",
        // );

        // return $this->renderer->template($request, $response, null, $assignData);
    }

    public function fn(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("platbyController\\fn called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(107)) {
            return $this->response;
        };

        $assignData = array(
            "page_title" => "page_title", "Adminator3 :: Faktury Neuhrazene",
            "body" => "Prosím vyberte z podkategorie výše...."
        );

        return $this->renderer->template($request, $response, 'platby/fn.tpl', $assignData);
    }

    public function fnKontrolaOmezeni(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("platbyController\\fn called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(149)) {
            return $this->response;
        };

        $platby = new \platby($this->container);

        $pocet_synchro_faktur = $platby->synchro_db_nf();

        $ret = $platby->fn_kontrola_omezeni();
        $dotaz_vlastnici_num = $ret[0];
        $zaznam = $ret[1];

        $assignData = [
            "page_title" => "Adminator3 :: N.F. :: Kontrola omezeni vs. platby",
            "nadpis" => "Kontrola omezení objektu vs. neuhr. fakturám",
            "faktury_pocet" => $pocet_synchro_faktur,
            "vlastnici_pocet" => $dotaz_vlastnici_num,
            "pole_data" => $zaznam
        ];

        return $this->renderer->template($request, $response, 'faktury/fn-kontrola-omezeni.tpl', $assignData);
    }
}

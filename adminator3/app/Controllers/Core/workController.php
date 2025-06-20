<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class workController extends adminatorController
{
    public \mysqli|\PDO $conn_mysql;

    public \Smarty $smarty;

    public \Monolog\Logger $logger;

    protected $sentinel;

    protected $adminator;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->conn_mysql = $container->get('connMysql');
        $this->smarty = $container->get('smarty');
        $this->logger = $container->get('logger');
        $this->sentinel = $container->get('sentinel');

        $this->logger->info("workController\__construct called");

        parent::__construct($container);
    }

    public function work(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("workController\work called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(16)) {
            return $this->response;
        };

        $this->smarty->assign("action", $_SERVER['SCRIPT_URL']);

        $assignData = [
            "page_title" => "Adminator3 :: Work",
        ];

        return $this->renderer->template($request, $response, 'work/work.tpl', $assignData);
    }
}

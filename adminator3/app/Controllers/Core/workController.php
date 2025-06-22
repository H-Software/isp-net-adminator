<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Core\work;

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

        $csrf = $this->generateCsrfToken($request, $response, true);
        // $this->logger->info("workController\work: csrf generated: ".var_export($csrf_name, true));

        $assignData = [
            "page_title" => "Adminator3 :: Work",
            "csrf_html" => $csrf[0],
        ];

        $work = new work($this->container);

        list($allItemsRs, $allItemsData) = $work->getAllItems();
        $assignData["items_list_select"] = $allItemsData;

        $rs = $work->taskGroupList();

        if ($rs[0] === true) {
            $assignData["work_list_groups_items"] = $rs[1];
        }

        $work->handleSingleActionForm();

        $assignData["p_bs_alerts"] = $work->p_bs_alerts;

        return $this->renderer->template($request, $response, 'work/work.tpl', $assignData);
    }
}

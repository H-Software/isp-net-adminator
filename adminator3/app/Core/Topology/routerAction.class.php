<?php

namespace App\Core\Topology;

use App\Core\adminator;
use Psr\Container\ContainerInterface;

class RouterAction extends adminator
{
    public \mysqli|\PDO $conn_mysql;

    public $smarty;

    public $logger;

    protected $settings;

    public function __construct(ContainerInterface $container)
    {
        $this->conn_mysql = $container->get('connMysql');
        // $this->conn_pgsql = $container->get('connPgsql');
        $this->logger = $container->get('logger');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");
    }

    public function action(): array
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $output = "";

        $output .= "<div style=\"padding-bottom: 10px; padding-top: 10px; font-size: 18px; \">Přidání/úprava routeru </div>";

        return [$output];
    }
}

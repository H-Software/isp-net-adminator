<?php

namespace App\Core\Topology;

use App\Core\adminator;
use Psr\Container\ContainerInterface;
use Exception;

class nodeAction extends adminator
{
    public \mysqli|\PDO $conn_mysql;

    // public \Smarty $smarty;

    public \Monolog\Logger $logger;

    protected $settings;

    protected $sentinel;

    protected $work;

    protected $loggedUserEmail;

    public $csrf_html;

    public function __construct(ContainerInterface $container)
    {
        $this->conn_mysql = $container->get('connMysql');
        // $this->conn_pgsql = $container->get('connPgsql');
        $this->logger = $container->get('logger');
        $this->sentinel = $container->get('sentinel');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->loggedUserEmail = $this->sentinel->getUser()->email;

        // $this->work = new \App\Core\work($container);
    }

    public function add()
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

    }

    public function update()
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

    }
}

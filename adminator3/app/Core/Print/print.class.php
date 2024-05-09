<?php

namespace App\Print;

use Exception;
use App\Core\adminator;
use Psr\Container\ContainerInterface;

class printClass extends adminator
{
    private $container;

    private $validator;

    public $conn_pgsql;
    public $conn_mysql;

    public $pdoMysql;

    public $logger;

    public $loggedUserEmail;

    public $adminator; // handler for instance of adminator class


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->validator = $container->get('validator');
        $this->conn_mysql = $container->get('connMysql');
        $this->pdoMysql = $container->get('pdoMysql');

        $this->logger = $container->get('logger');
        $this->smarty = $container->get('smarty');

        // $this->loggedUserEmail = \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email;

        // $this->adminator = new adminator($this->conn_mysql, $this->smarty, $this->logger);

    }

    public function printListAll()
    {

    }
}
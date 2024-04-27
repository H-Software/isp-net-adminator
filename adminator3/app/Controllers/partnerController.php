<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Illuminate\Database\Capsule\Manager as DB;

class partnerController extends adminatorController {
    public $conn_mysql;
    public $smarty;
    public $logger;

    protected $adminator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container; // for adminator class
		$this->conn_mysql = $container->connMysql;
        $this->smarty = $container->smarty;

        $this->logger = $container->logger;
        $this->logger->info("partnerController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
	  }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
      $this->logger->info("partnerController\cat called");

      $this->checkLevel(90, $this->adminator);

      $this->smarty->assign("page_title","Adminator3 :: Partner program");

      $this->header($request, $response, $this->adminator);
      
      $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

      $this->smarty->display('partner/partner-cat.tpl');
    }
}

<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Print\printClass;

class printController extends adminatorController
{
    public $logger;

    protected $adminator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $this->container->get('logger');
        $this->smarty = $this->container->get('smarty');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");
    }

    public function printListAll(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(146, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Print");

        $this->header($request, $response, $this->adminator);

        $this->printInstance = new printClass($this->container);

        $this->printInstance->printListAll();
        
        return $response;

    }
}

<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class runtimeController extends Controller
{
    public $logger;

    protected $adminator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $this->container->get('logger');
        $this->logger->info("runtimeController\__construct called");
    }

    public function opcacheGui(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        require_once __DIR__ . '/../../vendor/amnuts/opcache-gui/index.php';

        return $response;

    }
}

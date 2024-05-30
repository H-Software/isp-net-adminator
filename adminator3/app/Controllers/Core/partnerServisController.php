<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
// use App\Partner\partner;

class partnerServisController extends adminatorController
{
    public $smarty;
    public \Monolog\Logger $logger;

    protected $adminator;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    // private $partnerInstance;

    public function __construct(ContainerInterface $container)
    {
        $this->smarty = $container->get('smarty');
        $this->logger = $container->get('logger');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        parent::__construct($container);

        // $this->partnerInstance = new partner($container);

    }

    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(305)) {
            return $this->response;
        };

        // $listOutput = $this->partnerInstance->list();

        $assignData = [
            "page_title" => "Adminator3 :: Partner program :: Servis List",
            // "body" => $listOutput[0]
        ];

        // return $this->renderer->template($request, $response, 'partner/order-list.tpl', $assignData);
        return $response;
    }

}

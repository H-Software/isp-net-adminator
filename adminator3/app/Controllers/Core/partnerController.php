<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Partner\partner;

class partnerController extends adminatorController
{
    public \Smarty $smarty;

    public \Monolog\Logger $logger;

    protected $adminator;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    private $partnerInstance;

    public function __construct(ContainerInterface $container, $adminatorInstance = null)
    {
        $this->smarty = $container->get('smarty');
        $this->logger = $container->get('logger');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        parent::__construct($container, $adminatorInstance);

        $this->partnerInstance = new partner($container);

    }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(75)) {
            return $this->response;
        };

        $assignData = [
            "page_title" => "Adminator3 :: Partner program",
            "body" => "Prosím vyberte z podkategorie výše...."
        ];

        return $this->renderer->template($request, $response, 'partner/partner-cat.tpl', $assignData);
    }

    public function orderCat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(75)) {
            return $this->response;
        };

        $assignData = [
            "page_title" => "Adminator3 :: Partner program :: Orders",
            "body" => "Prosím vyberte z podkategorie výše...."
        ];

        return $this->renderer->template($request, $response, 'partner/order-cat.tpl', $assignData);
    }

    public function orderList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(76)) {
            return $this->response;
        };

        $listOutput = $this->partnerInstance->list();

        $assignData = [
            "page_title" => "Adminator3 :: Partner program :: Order List",
            "body" => $listOutput[0]
        ];

        return $this->renderer->template($request, $response, 'partner/order-list.tpl', $assignData);
    }

    public function orderAdd(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(75)) {
            return $this->response;
        };

        $assignData = [
            "page_title" => "Adminator3 :: Partner :: Order Add",
        ];

        // CSRF token name and value for update form
        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $this->logger->debug("partnerController\orderAdd: csrf generated: ".var_export($csrf_html, true));

        $this->partnerInstance->csrf_html = $csrf_html;
        $this->partnerInstance->form_uri = $request->getUri();

        $this->partnerInstance->add();

        return $this->renderer->template($request, $response, $this->partnerInstance->rendererTemplateName, $assignData);
    }

    public function orderAccept(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(77)) {
            return $this->response;
        };

        $assignData = [
            "page_title" => "Adminator3 :: Partner :: Order Accept",
        ];

        $this->partnerInstance->accept();

        return $this->renderer->template($request, $response, $this->partnerInstance->rendererTemplateName, $assignData);
    }

    public function orderChangeDesc(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(119)) {
            return $this->response;
        };

        $assignData = [
            "page_title" => "Adminator3 :: Partner :: Order Update Desc",
        ];

        $this->partnerInstance->updateDesc();

        return $this->renderer->template($request, $response, $this->partnerInstance->rendererTemplateName, $assignData);
    }

    public function orderChangeStatus(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(111)) {
            return $this->response;
        };

        $assignData = [
            "page_title" => "Adminator3 :: Partner :: Order Change Status",
        ];

        $this->partnerInstance->changeStatus();

        return $this->renderer->template($request, $response, $this->partnerInstance->rendererTemplateName, $assignData);
    }
}

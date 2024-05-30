<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class aboutController extends adminatorController
{
    public \Monolog\Logger $logger;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    public function __construct(ContainerInterface $container, $adminatorInstance = null)
    {
        $this->container = $container;
        $this->logger = $this->container->get('logger');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        parent::__construct($container, $adminatorInstance);
    }

    public function about(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("aboutController\about called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(142)) {
            return $this->response;
        };

        $assignData = array(
            "page_title" => "Adminator3 :: O programu",
            "body" => "Prosím vyberte z podkategorie výše...."
        );

        return $this->renderer->template($request, $response, 'about/about.tpl', $assignData);
    }

    public function changesOld(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("aboutController\changesOld called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(144)) {
            return $this->response;
        };

        $assignData = [
            "page_title" => "Adminator3 :: O programu :: Staré změny",
            "body" => "Prosím vyberte z podkategorie výše...."
        ];

        return $this->renderer->template($request, $response, 'about/about-changes-old.tpl', $assignData);
    }

    public function changes(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("aboutController\changes called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(145)) {
            return $this->response;
        };

        $assignData = array(
            "page_title" => "Adminator3 :: O programu :: Změny",
            "body" => "Prosím vyberte z podkategorie výše...."
        );

        return $this->renderer->template($request, $response, 'about/about-changes.tpl', $assignData);
    }
}

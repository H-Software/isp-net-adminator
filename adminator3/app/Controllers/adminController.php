<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class adminController extends adminatorController
{
    protected $container;

    public $logger;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    private $admin;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $this->container->get('logger');

        $this->logger->info("adminController\__construct called");

        parent::__construct($container);

        $this->admin = new \admin($this->container);
    }

    public function admin(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("adminController\admin called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(91)) {
            return $this->response;
        };

        $assignData = array(
            "page_title" => "Adminator3 :: admin",
            "body" => "Prosím vyberte z podkategorie výše...."
        );

        return $this->renderer->template($request, $response, 'admin/subcat.tpl', $assignData);
    }

    public function adminMain(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("adminController\adminMain called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(17)) {
            return $this->response;
        };

        $assignData = array(
            "page_title" => "Adminator3 :: admin :: subcat2",
            "body" => "Prosím vyberte z podkategorie výše...."
        );

        return $this->renderer->template($request, $response, 'admin/subcat2.tpl', $assignData);
    }

    public function adminLevelList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("adminController\adminLevelList called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(21)) {
            return $this->response;
        };

        // CSRF token name and value for update form
        list($csrf_html_empty, $csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value) = $this->generateCsrfToken($request, $response);
        $this->logger->info("adminController\adminLevelList: csrf generated: ".var_export($csrf_name, true));

        // render
        $assignData = array(
            "page_title" => "Adminator3 :: vypis levelu stranek",
            "body" => $this->admin->levelList($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value)
        );

        return $this->renderer->template($request, $response, 'admin/level-list.tpl', $assignData);
    }

    public function adminLevelListJson(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("adminController\adminLevelListJson called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(21)) {
            return $this->response;
        };

        list($data, $status, $msg) = $this->admin->levelListJson();

        // $this->logger->info("adminController\adminLevelListJson response: ". var_export(array($data, $status, $msg), true));

        $newResponse = $this->Jsonrender($request, $response, $data, $status, $msg);
        return $newResponse;
    }

    public function adminLevelAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("adminController\adminLevelAction called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(23)) {
            return $this->response;
        };

        // CSRF token name and value for update form
        list($csrf_html_empty, $csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value) = $this->generateCsrfToken($request, $response);
        $this->logger->debug("adminController\adminLevelAction: csrf generated: ".var_export($csrf_name, true));

        $rs = $this->admin->levelAction($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value);

        $assignData = array(
            "page_title" => "Adminator3 :: uprava levelu stranek",
            "body" => $rs[0]
        );

        return $this->renderer->template($request, $response, 'admin/level-action.tpl', $assignData);
    }

    public function adminTarify(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("adminController\adminTarify called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(131)) {
            return $this->response;
        };

        $rs = $this->admin->tarifList();

        $assignData = array(
            "page_title" => "Adminator3 :: Tarify",
            "body" => $rs[0]
        );

        return $this->renderer->template($request, $response, 'admin/tarify.tpl', $assignData);
    }

    public function adminTarifyAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("adminController\adminTarify called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(303)) {
            return $this->response;
        };

        list($rs, $rs_err) = $this->admin->tarifAction();

        //TODO: add showing errors in templates

        $assignData = array(
            "page_title" => "Adminator3 :: Tarify :: Action",
            "body" => $rs
        );

        return $this->renderer->template($request, $response, 'admin/tarify.tpl', $assignData);
    }
}

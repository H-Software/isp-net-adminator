<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class adminController extends adminatorController
{
    public $container;

    public $conn_mysql;
    public $smarty;
    public $logger;

    // protected $sentinel;

    // protected $adminator;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    private $admin;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        // $this->conn_mysql = $this->container->get('connMysql');
        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        // $this->sentinel = $this->container->get('sentinel');

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

        $this->smarty->assign("page_title", "Adminator3 :: admin :: subca2");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body", "Prosím vyberte z podkategorie výše....");

        $this->smarty->display('admin/subcat2.tpl');

        return $response;
    }

    public function adminLevelList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("adminController\adminLevelList called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(21)) {
            return $this->response;
        };

        $this->smarty->assign("page_title", "Adminator3 :: vypis levelu stranek");

        $this->header($request, $response, $this->adminator);

        // CSRF token name and value for update form
        list($csrf_html_empty, $csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value) = $this->generateCsrfToken($request, $response);

        $this->logger->info("adminController\adminLevelList: csrf generated: ".var_export($csrf_name, true));

        // render
        $this->smarty->assign("body", $this->admin->levelList($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value));

        $this->smarty->display('admin/level-list.tpl');

        return $response;
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

        $this->smarty->assign("page_title", "Adminator3 :: uprava levelu stranek");

        $this->header($request, $response, $this->adminator);

        // CSRF token name and value for update form
        list($csrf_html_empty, $csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value) = $this->generateCsrfToken($request, $response);

        $this->logger->debug("adminController\adminLevelAction: csrf generated: ".var_export($csrf_name, true));

        $rs = $this->admin->levelAction($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value);

        $this->smarty->assign("body", $rs[0]);

        $this->smarty->display('admin/level-action.tpl');

        return $response;
    }

    public function adminTarify(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("adminController\adminTarify called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(131)) {
            return $this->response;
        };

        $this->smarty->assign("page_title", "Adminator3 :: Tarify");

        $this->header($request, $response, $this->adminator);

        // $csrf_html = $this->generateCsrfToken($request, $response, true);

        $rs = $this->admin->tarifList();

        $this->smarty->assign("body", $rs[0]);

        $this->smarty->display('admin/tarify.tpl');

        return $response;
    }

    public function adminTarifyAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("adminController\adminTarify called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(303)) {
            return $this->response;
        };

        $this->smarty->assign("page_title", "Adminator3 :: Tarify :: Action");

        $this->header($request, $response, $this->adminator);


        list($rs, $rs_err) = $this->admin->tarifAction();

        //TODO: add showing errors in templates

        $this->smarty->assign("body", $rs[0]);

        $this->smarty->display('admin/tarify.tpl');

        return $response;
    }
}

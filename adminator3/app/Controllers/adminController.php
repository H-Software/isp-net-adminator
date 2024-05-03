<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class adminController extends adminatorController
{
    public $conn_mysql;
    public $smarty;
    public $logger;

    public $admin;

    public $adminator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        $this->logger->info("adminController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);

        $this->admin = new \admin($this->conn_mysql, $this->logger);

    }

    public function admin(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("adminController\admin called");

        $this->checkLevel(91, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: admin");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body", "Prosím vyberte z podkategorie výše....");

        $this->smarty->display('admin/subcat.tpl');

        return $response;
    }

    public function adminMain(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("adminController\adminMain called");

        $this->checkLevel(17, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: admin :: subca2");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body", "Prosím vyberte z podkategorie výše....");

        $this->smarty->display('admin/subcat2.tpl');

        return $response;
    }

    public function adminLevelList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("adminController\adminLevelList called");

        $this->checkLevel(21, $this->adminator);

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

        $this->checkLevel(21, $this->adminator);

        list($data, $status, $msg) = $this->admin->levelListJson();

        // $this->logger->info("adminController\adminLevelListJson response: ". var_export(array($data, $status, $msg), true));

        $newResponse = $this->Jsonrender($request, $response, $data, $status, $msg);
        return $newResponse;
    }

    public function adminLevelAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("adminController\adminLevelAction called");

        $this->checkLevel(23, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: uprava levelu stranek");

        $this->header($request, $response, $this->adminator);

        // CSRF token name and value for update form
        list($csrf_html_empty, $csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value) = $this->generateCsrfToken($request, $response);

        $this->logger->info("adminController\adminLevelAction: csrf generated: ".var_export($csrf_name, true));

        $rs = $this->admin->levelAction($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value);

        $this->smarty->assign("body", $rs[0]);

        $this->smarty->display('admin/level-action.tpl');

        return $response;
    }

    public function adminTarify(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("adminController\adminTarify called");

        $this->checkLevel(131, $this->adminator);

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

        $this->checkLevel(303, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Tarify :: Action");

        $this->header($request, $response, $this->adminator);


        list($rs, $rs_err) = $this->admin->tarifAction();

        //TODO: add showing errors in templates

        $this->smarty->assign("body", $rs[0]);

        $this->smarty->display('admin/tarify.tpl');

        return $response;
    }
}

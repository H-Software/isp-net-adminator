<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class adminController extends adminatorController {
    var $conn_mysql;
    var $smarty;
    var $logger;

    var $admin;

    var $adminator;

    public function __construct(ContainerInterface $container, $conn_mysql, $smarty, $logger, $auth, $app)
    {
        $this->container = $container;
		$this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        
        $this->logger = $this->container->logger;
        $this->logger->addInfo("adminController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);

        $this->admin = new \admin($this->conn_mysql, $this->logger);

	}

    public function admin(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("adminController\admin called");
        
        $this->checkLevel(91, $this->adminator);

        $this->smarty->assign("page_title","Adminator3 :: admin");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

        $this->smarty->display('admin/subcat.tpl');

        return $response;
    }

    public function adminMain(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("adminController\adminMain called");
        
        $this->checkLevel(17, $this->adminator);

        $this->smarty->assign("page_title","Adminator3 :: admin :: subca2");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

        $this->smarty->display('admin/subcat2.tpl');

        return $response;
    }

    public function adminLevelList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("adminController\adminLevelList called");
        
        $this->checkLevel(21, $this->adminator);

        // $this->smarty->assign("bs_layout_main_col_count", "8");

        $this->smarty->assign("page_title","Adminator3 :: vypis levelu stranek");

        $this->header($request, $response, $this->adminator);

        // CSRF token name and value for update form
        list($csrf_html_empty, $csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value) = $this->generateCsrfToken($request, $response);

        $this->logger->addInfo("adminController\adminLevelList: csrf generated: ".var_export($csrf_name, true));

        // render
        $this->smarty->assign("body",$this->admin->levelList($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value));

        $this->smarty->display('admin/level-list.tpl');

        return $response;
    }

    function adminLevelListJson(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->addInfo("adminController\adminLevelListJson called");
        
        $this->checkLevel(21, $this->adminator);

        list ($data, $status, $msg) = $this->admin->levelListJson();

        // $this->logger->addInfo("adminController\adminLevelListJson response: ". var_export(array($data, $status, $msg), true));

        $newResponse = $this->Jsonrender($request, $response, $data, $status, $msg);
        return $newResponse;
    }

    public function adminLevelAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->addInfo("adminController\adminLevelAction called");
          
        $this->checkLevel(23, $this->adminator);

        $this->smarty->assign("page_title","Adminator3 :: uprava levelu stranek");

        $this->header($request, $response, $this->adminator);

        // CSRF token name and value for update form
        list($csrf_html_empty, $csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value) = $this->generateCsrfToken($request, $response);

        $this->logger->addInfo("adminController\adminLevelAction: csrf generated: ".var_export($csrf_name, true));

        $rs = $this->admin->levelAction($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value);

        $this->smarty->assign("body",$rs[0]);

        $this->smarty->display('admin/level-action.tpl');

    }

    public function adminTarify(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->addInfo("adminController\adminTarify called");
          
        $this->checkLevel(131, $this->adminator);

        $this->smarty->assign("page_title","Adminator3 :: Admin :: Tarify");

        $this->header($request, $response, $this->adminator);

        // $csrf_html = $this->generateCsrfToken($request, $response, true);

        $rs = $this->admin->tarifList();

        $this->smarty->assign("body",$rs[0]);

        $this->smarty->display('admin/tarify.tpl');
    }
    
}
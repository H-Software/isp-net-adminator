<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class adminController extends adminatorController {
    var $conn_mysql;
    var $smarty;
    var $logger;
    var $auth;
    var $app;

    public function __construct(ContainerInterface $container, $conn_mysql, $smarty, $logger, $auth, $app)
    {
        $this->container = $container;
		    $this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
        $this->auth = $auth;
        $this->app = $app;
        
        $this->logger->addInfo("adminController\__construct called");
	}

    public function admin(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("adminController\admin called");
        
        $this->checkLevel(91);

        $this->smarty->assign("page_title","Adminator3 :: admin");

        $this->header($request, $response);

        $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

        $this->smarty->display('admin/subcat.tpl');

        return $response;
    }

    public function adminMain(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("adminController\adminMain called");
        
        $this->checkLevel(17);

        $this->smarty->assign("page_title","Adminator3 :: admin :: subca2");

        $this->header($request, $response);

        $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

        $this->smarty->display('admin/subcat2.tpl');

        return $response;
    }

    public function adminLevelList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("adminController\adminLevelList called");
        
        $this->checkLevel(21);

        $this->smarty->assign("page_title","Adminator3 :: vypis levelu stranek");

        $this->header($request, $response);

        // CSRF token name and value for update form
        $csrf = $this->container->get('csrf');
        $csrf_nameKey = $csrf->getTokenNameKey();
        $csrf_valueKey = $csrf->getTokenValueKey();
        $csrf_name = $request->getAttribute($csrf_nameKey);
        $csrf_value = $request->getAttribute($csrf_valueKey);

        $this->logger->addInfo("adminController\adminLevelList: csrf generated: ".var_export($csrf_name, true));

        // render
        $this->smarty->assign("body",admin::levelList($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value));

        $this->smarty->display('admin/level-list.tpl');

        return $response;
    }

    public function adminLevelAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->addInfo("adminController\adminLevelAction called");
          
        $this->checkLevel(23);

        $this->smarty->assign("page_title","Adminator3 :: uprava levelu stranek");

        $this->header($request, $response);

        // CSRF token name and value for update form
        $csrf = $this->container->get('csrf');
        $csrf_nameKey = $csrf->getTokenNameKey();
        $csrf_valueKey = $csrf->getTokenValueKey();
        $csrf_name = $request->getAttribute($csrf_nameKey);
        $csrf_value = $request->getAttribute($csrf_valueKey);

        $this->logger->addInfo("adminController\adminLevelAction: csrf generated: ".var_export($csrf_name, true));

        $rs = admin::levelAction($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value);

        $this->smarty->assign("body",$rs[0]);

        $this->smarty->display('admin/level-action.tpl');

    }
    
}
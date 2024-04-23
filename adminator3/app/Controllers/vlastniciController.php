<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Illuminate\Database\Capsule\Manager as DB;

class vlastniciController extends adminatorController {
    public $conn_mysql;
    public $smarty;
    public $logger;

    protected $adminator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container; // for adminator class
		$this->conn_mysql = $container->connMysql;
        $this->smarty = $container->smarty;

        $this->logger = $container->logger;
        $this->logger->info("vlastniciController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
	  }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
      $this->logger->info("vlastniciController\cat called");

      $this->checkLevel(90, $this->adminator);

      $this->smarty->assign("page_title","Adminator3 :: Zákazníci");

      $this->header($request, $response, $this->adminator);
      
      $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

      $this->smarty->display('vlastnici/vlastnici-cat.tpl');

    }

    public function vlastnici2(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("vlastniciController\\vlastnici2 called");
        
        $this->checkLevel(38, $this->adminator);

        $this->smarty->assign("page_title","Adminator3 :: Zákazníci");

        $this->header($request, $response, $this->adminator);

        $vlastnik2 = new \vlastnik2($this->container);
        
        // selectors form
        //
        $fs = new \App\Customer\fakturacniSkupiny($this->container);

        $select = $_GET["select"];

        if( $select == 2)
        { $fu_select = "2"; } //Pouze FU
        if( $select == 3 )
        { $fu_select = "1"; } //pouze DU

        $this->smarty->assign("select",$select);
        $fakt_skupiny = $fs->show_fakt_skupiny($fu_select);
        
        $this->smarty->assign("fakt_skupiny",$fakt_skupiny);
        
        $this->smarty->assign("fakt_skupiny_selected", $_GET['fakt_skupina']);

        $this->smarty->assign("razeni", $_GET['razeni']);
        $this->smarty->assign("razeni2", $_GET['razeni2']);

        // test capsule
        //
        // $objekty = DB::connection('pgsql')->select("select * from objekty");

        // main table
        $bodyContent = $vlastnik2->listItems();

        $this->smarty->assign("form_search_value", preg_replace('/^(%)(.*)(%)$/', '\2', $vlastnik2->listSql));

        $this->smarty->assign("body", $bodyContent);

        $this->smarty->display('vlastnici/vlastnici2.tpl');

        return $response;
    }

    public function fakturacniSkupiny(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("vlastniciController\\fakturacniSkupiny called");
        
        $this->checkLevel(99, $this->adminator);

        $this->smarty->assign("page_title","Adminator3 :: Zákazníci :: fakturační skupiny");

        $this->header($request, $response, $this->adminator);

        // list logic
        //
        $fs = new \App\Customer\fakturacniSkupiny($this->container);
        $fs_items = $fs->getItems();

        if(empty($fs_items))
        {
            $this->smarty->assign("message_no_items","Nebyly nalezeny žádné fakturační skupiny");
            $this->smarty->display('vlastnici/fakturacni-skupiny.tpl');
            return $response;
        }        

        $this->smarty->assign("fs_items",$fs_items);

        // debug
        // $this->smarty->assign("fs_items_debug","<pre>" . var_export($fs_items,true). "</pre>");
        
        $this->smarty->display('vlastnici/fakturacni-skupiny/list.tpl');

    }

    public function fakturacniSkupinyAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("vlastniciController\\fakturacniSkupinyAction called");
        
        $this->checkLevel(301, $this->adminator);

        $this->smarty->assign("page_title","Adminator3 :: . :: fakturační skupiny :: Action");

        $this->header($request, $response, $this->adminator);

        $fs = new \App\Customer\fakturacniSkupiny($this->container);
        $fs->csrf_html = $this->generateCsrfToken($request, $response, true);
        $fs->adminator_ctl = $this->adminator;

        $fs_action_body = $fs->Action();

        $this->smarty->assign("body",$fs_action_body);

        $this->smarty->display('vlastnici/fakturacni-skupiny/action.tpl');
    }

}
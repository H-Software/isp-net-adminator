<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class othersController extends adminatorController {
    var $conn_mysql;
    var $smarty;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
		$this->conn_mysql = $this->container->connMysql;
        $this->smarty = $this->container->smarty;
        $this->logger = $this->container->logger;
        $this->logger->info("othersController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
	}

    public function others(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("othersController\others called");
        
        $this->checkLevel(95, $this->adminator);

        $this->smarty->assign("page_title","Adminator3 :: Ostatní");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

        $this->smarty->display('others-cat.tpl');

        return $response;
    }

    public function board(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("othersController\board called");
        
        $this->checkLevel(87, $this->adminator);

        $this->smarty->assign("page_title","Adminator3 :: Board");

        $this->header($request, $response, $this->adminator);

        $nastenka = new \board($this->conn_mysql, $this->logger);

        $this->smarty->assign("datum",date("j. m. Y")); 
        $this->smarty->assign("sid",$sid); 

        $nastenka->what = $_GET["what"];
        $nastenka->action = $_GET["action"];
        $nastenka->page = $_GET["page"];

        $nastenka->send = $_GET["send"];
        $nastenka->sent = $_POST["sent"];

        $nastenka->author = $_POST["author"];
        $nastenka->email = $_POST["email"];

        $nastenka->to_date = $_POST["to_date"];
        $nastenka->from_date = $_POST["from_date"];

        $nastenka->subject = $_POST["subject"];
        $nastenka->body = $_POST["body"];

        $nastenka->prepare_vars($_SESSION['user']);

        if($nastenka->action == "view"):

            $this->smarty->assign("mod",1); 

            if($nastenka->what=="new")
            { $this->smarty->assign("mod_hlaska", "->> Aktuální zprávy"); }
            else
            { $this->smarty->assign("mod_hlaska","->> Staré zprávy"); }

            $nastenka->view_number = 10; //zprávy budou zobrazeny po ...

            $zpravy = $nastenka->show_messages();

            $this->smarty->assign("zpravy",$zpravy);

            $page = $nastenka->show_pages(); 
            $this->smarty->assign("strany",$page);

        else:

            $this->smarty->assign("mod",2); 

            $nastenka->write = false; //prvne předpokládáme zobr. formuláře

            if( isset($nastenka->sent) )
            { $nastenka->check_vars(); }

            if($nastenka->write)
            { //ulozeni dat

                $this->smarty->assign("mod",3); //vysledny formular ulozeni
                
                $nastenka->convert_vars();
                $add = $nastenka->insert_into_db();
                
                $this->smarty->assign("rs",$add); 
                $this->smarty->assign("body",$nastenka->error); 

                // if($add){ 
                //     header("Location: others-board.php"); //přesuneme se na úvodní stránku
                // }
            }
            else
            { //zobrazujeme formulář

                $csrf = $this->generateCsrfToken($request, $response, true);
                // $this->logger->info("adminController\header: csrf generated: ".var_export($csrf, true));
                $this->smarty->assign("csrf_html", $csrf[0]);

                $this->smarty->assign("enable_calendar",1); 

                $this->smarty->assign("mod",2); //zobrazujeme formular pro zadavani dat
                $this->smarty->assign("mod_hlaska", "->> Přidat zprávu");

                $this->smarty->assign("nick",$_SESSION['user']); 

                $this->smarty->assign("email",$nastenka->email); 
                $this->smarty->assign("subject",$nastenka->subject); 

                $this->smarty->assign("from_date",$nastenka->from_date);
                $this->smarty->assign("to_date",$nastenka->to_date);
                
                $this->smarty->assign("body",$nastenka->body); 

                $this->smarty->assign("error",$nastenka->error); 
            }

        endif;

        $this->smarty->display('others/board.tpl');

    }
}
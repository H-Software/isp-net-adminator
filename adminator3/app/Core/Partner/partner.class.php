<?php

namespace App\Partner;

use App\Core\adminator;
use Psr\Container\ContainerInterface;

class partner extends adminator
{

    var $conn_pqsql;
    var $conn_mysql;

    var $logger;

    var $loggedUserEmail;

    var $adminator; // handler for instance of adminator class

    var $url_params;

    var $list_dotaz_sql;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->validator = $container->validator;
        $this->conn_mysql = $container->connMysql;   
        $this->logger = $container->logger;

        $i = $container->auth->getIdentity();
        $this->loggedUserEmail = $i['username'];
    }

    public function listPrepareVars()
    {

        // priprava form. promennych       
        // $list=$_GET["list"];
	 
        $filtr_akceptovano = intval($_GET["filtr_akceptovano"]);
        $filtr_pripojeno = intval($_GET["filtr_pripojeno"]);

        //priprava dotazu              
        if( $filtr_akceptovano == 1 ){ $filtr .= " AND akceptovano = 1 "; }
        elseif( $filtr_akceptovano == 2 ){ $filtr .= " AND akceptovano = 0 "; }
                    
        if( $filtr_pripojeno == 1 ){ $filtr .= " AND pripojeno = 1 "; }
        elseif( $filtr_pripojeno == 2 ){ $filtr .= " AND pripojeno = 0 "; }
        
        $basic = "SELECT *,DATE_FORMAT(datum_vlozeni, '%d.%m.%Y %H:%i:%s') as datum_vlozeni2 FROM partner_klienti ";
                                
        $this->list_dotaz_sql = $basic;
        
        if( isset($user) )
        { $this->list_dotaz_sql .= " WHERE ( vlozil = '$user_plaint' ".$filtr." ) "; }
        else
        { $this->list_dotaz_sql .= " WHERE ( id > 0 ".$filtr." ) "; }
        
        $this->list_dotaz_sql .= " ORDER BY id DESC ";																											 
        
        // old name poradek
        $this->url_params = "filtr_akceptovano=".$filtr_akceptovano."&filtr_pripojeno=".$filtr_pripojeno;

        return true;
    }

    public function list()
    {
        $output = "";

        $this->listPrepareVars();

        $format_css = "font-size: 13px; padding-top: 5px; padding-bottom: 15px; ";

        if(strlen($_GET['list']) > 0 ){
            $list = intval($_GET['list']);
        }
        //vytvoreni objektu
        $listovani = new \c_listing_partner(
                            $this->conn_mysql,
                            "/partner/order/list?" . urlencode($this->url_params),
                            30,
                            $list,
                            "<center><div style=\"".$format_css."\">\n", "</div></center>\n",
                            $this->list_dotaz_sql
                        );
        $listovani->echo = false;

        if (($list == "")||($list == "1")){ $bude_chybet = 0; }
        else{ $bude_chybet = (($list-1) * $listovani->interval); }
        
        $interval = $listovani->interval;
                        
        $dotaz_limit = " LIMIT ".intval($interval)." OFFSET ".intval($bude_chybet)." ";

        $this->list_dotaz_sql .= $dotaz_limit;
            
        $output .= "<pre>" . var_export($this->list_dotaz_sql, true) . "</pre>";

        $output .= $listovani->listInterval();
            
        // $partner = new partner;
            
        // $partner->show_legend(); // prom vyrizeni update asi zde prazdne
            
        // $partner->show_art($filtr_akceptovano,$filtr_pripojeno,$dotaz_sql);
        
        $output .= $listovani->listInterval();    					         

        return array($output);
    }
}
<?php

namespace App\Partner;

use App\Models\PartnerOrder;
use App\Core\adminator;
use Psr\Container\ContainerInterface;
use Illuminate\Pagination\LengthAwarePaginator;

use Lloricode\LaravelHtmlTable\LaravelHtmlTableGenerator;

class partner extends adminator
{

    var $conn_pqsql;
    var $conn_mysql;

    var $logger;

    var $loggedUserEmail;

    var $adminator; // handler for instance of adminator class

    var $url_params;

    var $listItems;

    var $paginateItemsPerPage = 15;

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
        $filtr_akceptovano = intval($_GET["filtr_akceptovano"]);
        $filtr_pripojeno = intval($_GET["filtr_pripojeno"]);

        if( $filtr_akceptovano == 1 ){
            $this->listItems = $this->listItems->where('akceptovano', 1); 
        }
        elseif( $filtr_akceptovano == 2 ){
             $this->listItems = $this->listItems->where('akceptovano', 0); 
        }
                    
        if( $filtr_pripojeno == 1 ){
            $this->listItems = $this->listItems->where('pripojeno', 1);  
        }
        elseif( $filtr_pripojeno == 2 ){
            $this->listItems = $this->listItems->where('pripojeno', 0);
        }
        
        if( isset($_GET['user']) ){
            $this->listItems = $this->listItems->where('vlozil', $_GET['user']);
        }
        
        // old name poradek
        // $this->url_params = "filtr_akceptovano=".$filtr_akceptovano."&filtr_pripojeno=".$filtr_pripojeno;

        return true;
    }

    public function list()
    {
        $output = "";

        $this->listItems = PartnerOrder::get()
        ->sortByDesc('id');

        $this->listPrepareVars();

        $this->listItems = adminator::collectionPaginate(
                                $this->listItems, 
                                $this->paginateItemsPerPage, 
                                $_GET['page'],                 
                                [   // $options
                                    'path' => LengthAwarePaginator::resolveCurrentPath(strtok($_SERVER["REQUEST_URI"], '?')),
                                    'pageName' => 'page',
                                ]
                            );

        $data = $this->listItems->toArray();

        list($linkPreviousPage, $linkCurrentPage, $linkNextPage) = adminator::paginateGetLinks($data);
        // echo "<pre>" . var_export($data, true) . "</pre>";

		if( count($data) == 0 )
		{
			$output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"padding-top: 5px; padding-bottom: 5px;\">Žádné záznamy v databázi (num_rows: " . count($data) . ")</div>";
			return array($output);
		}

		$headers = ['id', 
                    'telefon',
                    'jmeno',
                    'adresa',
                    'email',
                    'poznamka',
                    'priorita',
                    'vlozil kdo',
                    'datum vlozeni',
                    'pripojeno',
                    'pripojeno linka',
                    'typ balicku',
                    'typ linky',
                    'akceptovano'
                    // 'datum vlozeni2'
        ] ;

		$attributes = 'class="a-common-table a-common-table-1line" '
					. 'id="partner-order-table" '
					. 'style="width: 99%"'
					;
                
        $listTable = new LaravelHtmlTableGenerator;
        
        $output .= adminator::paginateRenderLinks($linkPreviousPage, $linkCurrentPage, $linkNextPage);

		$output .= $listTable->generate($headers, $data['data'], $attributes);

        $output .= adminator::paginateRenderLinks($linkPreviousPage, $linkCurrentPage, $linkNextPage);

        return array($output);
    }

}
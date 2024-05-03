<?php

namespace App\Partner;

use App\Models\PartnerOrder;
use App\Core\adminator;
use Psr\Container\ContainerInterface;
use Illuminate\Pagination\LengthAwarePaginator;

use Lloricode\LaravelHtmlTable\LaravelHtmlTableGenerator;

class partner extends adminator
{
    public $conn_pgsql;
    public $conn_mysql;

    public $logger;

    public $loggedUserEmail;

    public $adminator; // handler for instance of adminator class

    public $csrf_html;

    public $url_params;

    public $listItems;

    public $paginateItemsPerPage = 15;

    private $action_form;

    private $form_fail = false;

    public $form_uri;

    private $form_error;

    private $form_jmeno_klienta;
    private $form_bydliste;
    private $form_email;
    private $form_tel;
    private $form_typ_balicku;
    private $form_typ_linky;
    private $form_pozn;
    private $form_odeslat;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->validator = $container->get('validator');
        $this->conn_mysql = $container->get('connMysql');
        $this->logger = $container->get('logger');
        $this->smarty = $container->get('smarty');

        $this->loggedUserEmail = \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email;
    }

    public function listPrepareVars()
    {
        $filtr_akceptovano = intval($_GET["filtr_akceptovano"]);
        $filtr_pripojeno = intval($_GET["filtr_pripojeno"]);

        if($filtr_akceptovano == 1) {
            $this->listItems = $this->listItems->where('akceptovano', 1);
        } elseif($filtr_akceptovano == 2) {
            $this->listItems = $this->listItems->where('akceptovano', 0);
        }

        if($filtr_pripojeno == 1) {
            $this->listItems = $this->listItems->where('pripojeno', 1);
        } elseif($filtr_pripojeno == 2) {
            $this->listItems = $this->listItems->where('pripojeno', 0);
        }

        if(isset($_GET['user'])) {
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

        if(count($data) == 0) {
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

        $listTable = new LaravelHtmlTableGenerator();

        $output .= adminator::paginateRenderLinks($linkPreviousPage, $linkCurrentPage, $linkNextPage);

        $output .= $listTable->generate($headers, $data['data'], $attributes);

        $output .= adminator::paginateRenderLinks($linkPreviousPage, $linkCurrentPage, $linkNextPage);

        return array($output);
    }

    private function addPrepareVars()
    {
        $this->form_jmeno_klienta = $_POST["jmeno_klienta"]; 
        $this->form_bydliste = $_POST["bydliste"];
        $this->form_email = $_POST["email"];
        $this->form_tel = $_POST["tel"];
        $this->form_typ_balicku = $_POST["typ_balicku"];
        $this->form_typ_linky = $_POST["typ_linky"];
       
        $this->form_pozn = $_POST["pozn"];
        $this->form_odeslat = $_POST["odeslat"];
    }

    private function addRenderForm()
    {
        $form_id = "partner-order-add";

        $this->action_form = $this->formInit();

        $form_data['f_open'] = $this->action_form->open($form_id, $form_id, $this->form_uri, '', '', $this->csrf_html);

        $form_data['f_close'] = $this->action_form->close();
        $form_data['f_submit_button'] = $this->action_form->input_submit('odeslano', '', 'OK / Odeslat / Uložit');

        $form_data['f_input_popis'] = $this->action_form->text('popis', 'Popis objektu', $data['popis']);

        $form_data['f_input_nod_find'] = $this->action_form->text('nod_find', 'Přípojný bod - filtr', $data['nod_find']);

        $form_data['f_input_nod_find_button'] = $this->action_form->input_submit(
            'g1',
            '',
            'Hledat (nody)',
            '',
            'class="btn btn-secondary" '
        );

        return $form_data;
    }

    public function add()
    {
        $this->logger->info("partner\add called");

        $bodyContent = "";

        $this->addPrepareVars();

        if( ( isset($this->form_odeslat) and ($this->form_fail == false) ) ) { 
        // mod ukladani
           $bodyContent .= "<div> missing saving code</div>";
        }
        else { 
            // zobrazime formular

            if( isset($this->form_odeslat) ){
                $this->smarty->assign("form_error_message", $this->form_error); 
            }

            $form_data = $this->addRenderForm();

            // $bodyContent .=  print_r($form_data);
            $this->smarty->assign("form_data", $form_data);

            // $this->logger->debug("partner\add: bodyContent: " . var_export($bodyContent, true));
            $this->smarty->assign("body", $bodyContent);

            $this->smarty->display('partner/order-add-form.tpl');

            return false;
        }

        $this->smarty->display('partner/order-add.tpl');
        
        return true;
    }
}

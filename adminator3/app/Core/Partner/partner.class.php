<?php

namespace App\Partner;

use Exception;
use App\Models\PartnerOrder;
use App\Core\adminator;
use Psr\Container\ContainerInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Lloricode\LaravelHtmlTable\LaravelHtmlTableGenerator;

class partner extends adminator
{
    private $container;

    private $validator;

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

    public function listPrepareVars($mode = null)
    {
        $filtr_akceptovano = intval($_GET["filtr_akceptovano"]);
        $filtr_pripojeno = intval($_GET["filtr_pripojeno"]);

        if($filtr_akceptovano == 1) {
            $this->listItems = $this->listItems->where('akceptovano', "Ano");
        } elseif($filtr_akceptovano == 2 or $mode == "accept") {
            $this->listItems = $this->listItems->where('akceptovano', "Ne");
        }

        if($filtr_pripojeno == 1) {
            $this->listItems = $this->listItems->where('pripojeno', 1);
        } elseif($filtr_pripojeno == 2) {
            $this->listItems = $this->listItems->where('pripojeno', 0);
        }

        if(isset($_GET['user'])) {
            $this->listItems = $this->listItems->where('vlozil', $_GET['user']);
        }

        if($mode == "accept"){
            $this->listItems = $this->listItems->select(
                [
                    'jmeno',
                    'adresa',
                    'tel',
                    'email',
                    'poznamky',
                    'prio',
                    'vlozil',
                    'datum_vlozeni',
                    'id'
                ]
            );

            $this->listItems = $this->listItems->transform(
                function ($item, $key) {
                    $id = $item['id'];
                    $item['id'] = "<a href=\"?id=" . $id . "\">ACCEPT</a>";
                    return $item;
                }
            );
        }

        return true;
    }

    private function getItems($mode = null)
    {
        $this->listItems = PartnerOrder::get()
            ->sortByDesc('id');

        $this->listPrepareVars($mode);

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

        return array(
            $data,
            $linkPreviousPage,
            $linkCurrentPage,
            $linkNextPage
        );
    }

    public function list($mode = null)
    {
        $output = "";

        list(
            $data,
            $linkPreviousPage,
            $linkCurrentPage,
            $linkNextPage
        ) = $this->getItems($mode);

        if(count($data) == 0) {
            $output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"padding-top: 5px; padding-bottom: 5px;\">Žádné záznamy v databázi (num_rows: " . count($data) . ")</div>";
            return array($output);
        }

        $headers = [
            'id',
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
        $this->form_typ_balicku = intval($_POST["typ_balicku"]);
        $this->form_typ_linky = intval($_POST["typ_linky"]);

        $this->form_pozn = $_POST["pozn"];
        $this->form_odeslat = $_POST["odeslat"];

        // TODO: add validation

    }

    private function addSaveData()
    {
        $data['Jméno a příjmení klienta'] = $this->form_jmeno_klienta;
        $data['Bydliště/přípojné místo'] = $this->form_bydliste;
        $data['Emailová adresa'] = $this->form_email;
        $data['Telefon'] = $this->form_tel;
        $data['Typ balícku'] = $this->form_typ_balicku;
        $data['Typ linky'] = $this->form_typ_linky;
        $data['Poznámka'] = $this->form_pozn;

        try {
            $item = PartnerOrder::create(
                [
                    'jmeno' => $this->form_jmeno_klienta,
                    'adresa' => $this->form_bydliste,
                    'email' => $this->form_email,
                    'tel' => $this->form_tel,
                    'poznamky' => $this->form_pozn,
                    'typ_balicku' => $this->form_typ_balicku,
                    'typ_linky' => $this->form_typ_linky,
                    'vlozil' => $this->loggedUserEmail
                ]
            );
        } catch (Exception $e) {
            $item = $e->getMessage();
        }

        return [$item, $data];
    }

    private function addRenderForm()
    {
        $form_id = "partner-order-add";

        $this->action_form = $this->formInit();

        $form_data['f_csrf'] = $this->csrf_html;
        $form_data['f_open'] = $this->action_form->open($form_id, $form_id, $this->form_uri, '', '', $this->csrf_html);
        $form_data['f_close'] = $this->action_form->close();

        $form_data['f_submit_button'] = $this->action_form->input_submit('odeslat', '', 'OK');

        $form_data['f_input_jmeno_klienta'] = $this->action_form->text('jmeno_klienta', 'Jméno a příjmení klienta', $this->form_jmeno_klienta);
        $form_data['f_input_bydliste'] = $this->action_form->text('bydliste', 'Bydliště/přípojné místo', $this->form_bydliste);

        $form_data['f_input_email'] = $this->action_form->email('email', 'Emailová adresa', $this->form_email, '', 'w-75 p-3', '');
        $form_data['f_input_tel'] = $this->action_form->tel('tel', 'Telefon', $this->form_tel);

        $form_data['f_input_typ_balicku'] = $this->action_form->number('typ_balicku', 'Typ balícku', $this->form_typ_balicku);
        $form_data['f_input_typ_linky'] = $this->action_form->number('typ_linky', 'Typ linky', $this->form_typ_linky);

        $form_data['f_input_pozn'] = $this->action_form->textarea('pozn', 'Poznámka', $this->form_pozn, 'rows="5" wrap="soft"');


        return $form_data;
    }

    public function add()
    {
        $this->logger->info("partner\add called");

        $this->addPrepareVars();

        if((isset($this->form_odeslat) and ($this->form_fail == false))) {
            // mod ukladani

            list($insertRs, $insertedData) = $this->addSaveData();

            if(is_object($insertRs)) {
                $insertMsg = '<div class="alert alert-success pb-2" role="alert" >Data byla úspěšně uložena.</div>';
                $this->logger->info("partner\add: insert into database was successful");
            } else {
                $insertMsg = '<div class="alert alert-danger pb-2" role="alert" >Chyba! Data se nepodařilo uložit. </div>'
                             .'<div class="alert alert-secondary pb-2" role="alert" >' . $insertRs . ')</div>';
                $this->logger->error("partner\add: insert into database has failed. Error message: " . var_export($insertRs, true));
            }

            // $this->logger->info("partner\add insertedData: " . var_export($insertedData, true));

            $this->smarty->assign("insertedData", $insertedData);
            $this->smarty->assign("insertMsg", $insertMsg);

            $this->smarty->display('partner/order-add.tpl');
            return true;
        } else {
            // zobrazime formular

            if(isset($this->form_odeslat)) {
                $this->smarty->assign("form_error_message", $this->form_error);
            }

            $form_data = $this->addRenderForm();
            // $bodyContent .=  print_r($form_data);
            $this->smarty->assign($form_data);

            $this->smarty->display('partner/order-add-form.tpl');
            return true;
        }
    }

    public function accept()
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        if ( $_GET["accept"] != 1 and !isset($_GET['id'])) {
            // list view
            $output = "";

            list(
                $data,
                $linkPreviousPage,
                $linkCurrentPage,
                $linkNextPage
            ) = $this->getItems("accept");

            if(count($data) == 0) {
                $output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"padding-top: 5px; padding-bottom: 5px;\">Žádné záznamy v databázi (num_rows: " . count($data) . ")</div>";
                return array($output);
            }

            $headers = [
                'jmeno',
                'adresa',
                'telefon',
                'email',
                'poznamka',
                'priorita',
                'vlozil kdo',
                'datum vlozeni',
                'akceptovat'
            ] ;

            $attributes = 'class="a-common-table a-common-table-1line" '
                        . 'id="partner-order-table" '
                        . 'style="width: 99%"'
            ;

            $listTable = new LaravelHtmlTableGenerator();

            $output .= adminator::paginateRenderLinks($linkPreviousPage, $linkCurrentPage, $linkNextPage);

            $output .= $listTable->generate($headers, $data['data'], $attributes);

            $output .= adminator::paginateRenderLinks($linkPreviousPage, $linkCurrentPage, $linkNextPage);

            $output = array($output);

            $this->smarty->assign("body", $output[0]);

            $this->smarty->display('partner/order-accept-list.tpl');
        } elseif ($_GET["accept"] != 1) {
            // confirm form

        } elseif($_GET["accept"] == 1 and intval($_GET['id']) > 0) {
            // update item in DB

        } else {
            // unknown mode
        }
    }
}

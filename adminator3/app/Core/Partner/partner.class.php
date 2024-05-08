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

    public$conn_pgsql;
    public $conn_mysql;

    public $pdoMysql;

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
        $this->pdoMysql = $container->get('pdoMysql');

        $this->logger = $container->get('logger');
        $this->smarty = $container->get('smarty');

        $this->loggedUserEmail = \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email;
    }

    public function listPrepareVars($mode = null)
    {
        $filtr_akceptovano = intval($_GET["filtr_akceptovano"]);
        $filtr_pripojeno = intval($_GET["filtr_pripojeno"]);

        if($filtr_akceptovano == 1 or $mode == "updateDesc") {
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

        if($mode == "accept" or $mode == "updateDesc") {
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
        }

        if($mode == "accept" or $mode == "updateDesc") {
            $this->listItems = $this->listItems->transform(
                function ($item, $key) use ($mode) {
                    list($a, $b) = preg_split('/(?=[A-Z])/', $mode);
                    $name = strtoupper($a . " " . $b);
                    $id = $item['id'];

                    $item['id'] = "<a href=\"?id=" . $id . "\">" . $name . "</a>";
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

    public function accept(): void
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");
        $output = "";

        if ($_GET["accept"] != 1 and !isset($_GET['id'])) {
            // list view

            list(
                $data,
                $linkPreviousPage,
                $linkCurrentPage,
                $linkNextPage
            ) = $this->getItems("accept");

            if(count($data) == 0) {
                $output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"padding-top: 5px; padding-bottom: 5px;\">Žádné záznamy v databázi (num_rows: " . count($data) . ")</div>";
                $this->smarty->assign("body", $output[0]);
                $this->smarty->display('partner/order-accept.tpl');
                return;
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
        } elseif ($_GET["accept"] != 1) {
            // confirm form

            $output .= "<form action=\"\" method=\"GET\" >";


            $output .=  "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >Pokud je třeba, vložte poznámku: </div>";

            $output .=  "<div style=\"padding-left: 40px; padding-bottom: 20px;\" >
                <textarea name=\"pozn\" cols=\"50\" rows=\"6\"></textarea>
            </div>";

            $output .=  "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >
                <input type=\"submit\" name=\"odeslat\" value=\"OK\" >
            </div>";

            $output .=  "<input type=\"hidden\" name=\"accept\" value=\"1\"> 
                <input type=\"hidden\" name=\"id\" value=\"".intval($_GET["id"])."\" >";
            $output .=  "</form>";

        } elseif($_GET["accept"] == 1 and intval($_GET['id']) > 0) {
            // update item in DB
            $pozn = $this->conn_mysql->real_escape_string($_GET["pozn"]);
            $id = intval($_GET["id"]);

            $uprava = $this->conn_mysql->query(
                "UPDATE partner_klienti "
                                . "SET akceptovano='1', "
                                    . "akceptovano_kym='". $this->loggedUserEmail ."', "
                                    . " akceptovano_pozn = '$pozn' "
                                . "WHERE id = ".$id." Limit 1"
            );

            if ($uprava == 1) {
                $output .= '<div 
                class="alert alert-success" 
                role="alert"
                style="width: 80%; "
                >'
                ."Zákazník úspěšně akceptován.</div>\n";
            } else {
                $output .= '<div 
                class="alert alert-danger" 
                role="alert"
                style="width: 80%; "
                >'
                ."Chyba! Zákazníka nelze akceptovat. Data nelze uložit do databáze. </div>\n";
            }
        } else {
            // unknown mode
        }

        $output = array($output);

        $this->smarty->assign("body", $output[0]);

        $this->smarty->display('partner/order-accept.tpl');
    }

    public function updateDesc(): void
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");
        $output = "";

        if ($_GET["accept"] != 1 and !isset($_GET['id'])) {
            // list view

            list(
                $data,
                $linkPreviousPage,
                $linkCurrentPage,
                $linkNextPage
            ) = $this->getItems("updateDesc");

            if(count($data) == 0) {
                $output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"padding-top: 5px; padding-bottom: 5px;\">Žádné záznamy v databázi (num_rows: " . count($data) . ")</div>";
                $this->smarty->assign("body", $output[0]);
                $this->smarty->display('partner/order-update-desc.tpl');
                return;
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
        } elseif ($_GET["edit"] != 1) {
            // update form

            $id = intval($_GET['id']);
            $dotaz = $this->pdoMysql->query("SELECT akceptovano_pozn FROM partner_klienti WHERE id = '" . $id. "' ");
            $data = $dotaz->fetchAll();
      
            $output .= "<form action=\"\" method=\"GET\" >"
        
            . "<div style=\"padding-left: 40px; padding-bottom: 20px; padding-top: 20px; \" >Upravte poznámku: </div>"
        
            . "<div style=\"padding-left: 40px; padding-bottom: 20px;\" >
                <textarea name=\"pozn\" cols=\"50\" rows=\"6\">".htmlspecialchars($data[0]["akceptovano_pozn"])."</textarea>
            </div>"
        
            . "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >
                <input type=\"submit\" name=\"odeslat\" value=\"OK\" >
            </div>" 
        
            . "<input type=\"hidden\" name=\"edit\" value=\"1\"> 
            <input type=\"hidden\" name=\"id\" value=\"".$id."\" >"

            . "</form>";

        } elseif($_GET["edit"] == 1 and intval($_GET['id']) > 0) {
            // update in DB

            $pozn = $this->conn_mysql->real_escape_string($_GET["pozn"]);
            $id = intval($_GET["id"]);
        
            try {
                $uprava=$this->conn_mysql->query("UPDATE partner_klienti SET akceptovano_pozn = '$pozn' WHERE id=".$id." Limit 1 ");

                $content = adminator::getHtmlBootstrapForAlertSuccess("Poznámka úspěšně upravena");
                $output .= adminator::getHtmlBootstrapForCenterColumn($content);
            }
            catch (Exception $e) {
                $content  = '<div 
                class="alert alert-danger" 
                role="alert"

                >'
                ."Chyba! Poznámku nelze upravit. Data nelze uložit do databáze.</div>\n";

                $content .= '<div 
                class="alert alert-secondary" 
                role="alert"
                >'
                ."(" . $e->getMessage() . ")</div>\n";
                $output .= adminator::getHtmlBootstrapForCenterColumn($content);
            }

        } else {
            // unknown mode
            $output .= "unknown mode";
        }

        $output = array($output);

        $this->smarty->assign("body", $output[0]);

        $this->smarty->display('partner/order-update-desc.tpl');

    }
}
